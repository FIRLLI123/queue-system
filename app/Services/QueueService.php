<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\QueuePosition;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class QueueService
{
    public function getActiveQueue(): Collection
    {
        return QueuePosition::active()
            ->with(['user'])
            ->orderBy('queue_number')
            ->get();
    }

    public function getQueueSnapshot(Collection $queue): array
    {
        return $queue->map(function (QueuePosition $position) {
            return [
                'user_id' => $position->user_id,
                'username' => optional($position->user)->username,
                'name' => optional($position->user)->name,
                'queue_number' => $position->queue_number,
                'status' => $position->status,
                'is_logged_in' => $position->user ? $position->user->isOnline() : false,
            ];
        })->toArray();
    }

    public function acceptOrder(User $user, OrderType $type): Order
    {
        $queue = $this->getActiveQueue();
        $firstPosition = $queue->first();

        if (!$firstPosition) {
            $this->abort(400, 'Queue is empty.');
        }

        if (!$user->isActive() || $user->id !== $firstPosition->user_id) {
            $this->abort(403, 'It is not your turn to accept an order.');
        }

        return DB::transaction(function () use ($queue, $firstPosition, $user, $type) {
            $queueBefore = $this->getQueueSnapshot($queue);

            $orderedUserIds = $queue->pluck('user_id')->toArray();
            array_shift($orderedUserIds);
            $orderedUserIds[] = $firstPosition->user_id;

            // Shift queue numbers using temporary offset to prevent unique constraint violation
            foreach ($orderedUserIds as $index => $userId) {
                $position = $queue->firstWhere('user_id', $userId);
                if ($position) {
                    $position->queue_number = ($index + 1) + 1000;
                    $position->save();
                }
            }

            foreach ($orderedUserIds as $index => $userId) {
                $position = $queue->firstWhere('user_id', $userId);
                if ($position) {
                    $position->queue_number = $index + 1;
                    $position->save();
                }
            }

            $queueAfter = $this->getQueueSnapshot($this->getActiveQueue());

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'order_type_id' => $type->id,
                'queue_before' => $queueBefore,
                'queue_after' => $queueAfter,
                'status' => 'COMPLETED',
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'ACCEPT_ORDER',
                'description' => sprintf('%s accepted order %s (%s).', $user->username, $order->order_number, $type->name),
            ]);

            return $order->load('orderType', 'user');
        });
    }

    public function voidLastOrder(User $user, string $reason): Order
    {
        $lastOrder = Order::where('status', 'COMPLETED')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastOrder) {
            $this->abort(404, 'No order is available to void.');
        }

        if ($lastOrder->user_id !== $user->id) {
            $this->abort(403, 'Only the CC who accepted the last order may void it.');
        }

        $nextOrderExists = Order::where('id', '>', $lastOrder->id)->exists();
        if ($nextOrderExists) {
            $this->abort(403, 'This order cannot be voided because a newer order already exists.');
        }

        return DB::transaction(function () use ($lastOrder, $user, $reason) {
            $this->restoreQueueFromSnapshot($lastOrder->queue_before);

            $lastOrder->update([
                'status' => 'VOID',
                'void_reason' => $reason,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'VOID_ORDER',
                'description' => sprintf('%s voided order %s: %s', $user->username, $lastOrder->order_number, $reason),
            ]);

            return $lastOrder->fresh();
        });
    }

    public function getDashboardData(User $user): array
    {
        $queue = $this->getActiveQueue();
        $queuePayload = $this->getQueueSnapshot($queue);
        $firstPosition = $queue->first();
        $lastOrder = Order::with(['orderType', 'user'])->orderBy('id', 'desc')->first();
        $activities = ActivityLog::with('user')->orderBy('id', 'desc')->limit(10)->get()->map(function (ActivityLog $activity) {
            return [
                'id' => $activity->id,
                'user' => optional($activity->user)->username,
                'action' => $activity->action,
                'description' => $activity->description,
                'created_at' => $activity->created_at->toDateTimeString(),
            ];
        })->toArray();

        $todayOrders = Order::with('orderType')
            ->whereDate('created_at', now()->toDateString())
            ->get();

        $stats = [
            'CRM' => 0,
            'CMS' => 0,
            'OTHER' => 0,
            'TOTAL' => $todayOrders->count(),
        ];

        foreach ($todayOrders as $order) {
            $typeName = optional($order->orderType)->name;
            if ($typeName && array_key_exists($typeName, $stats)) {
                $stats[$typeName]++;
            }
        }

        return [
            'queue' => $queuePayload,
            'current_turn' => [
                'user_id' => optional($firstPosition)->user_id,
                'username' => optional($firstPosition->user)->username,
                'name' => optional($firstPosition->user)->name,
                'status' => optional($firstPosition)->status,
                'last_accepted_at' => $firstPosition && $firstPosition->updated_at ? $firstPosition->updated_at->toDateTimeString() : null,
                'is_logged_in' => $firstPosition && $firstPosition->user ? $firstPosition->user->isOnline() : false,
            ],
            'last_order' => $lastOrder ? [
                'id' => $lastOrder->id,
                'order_number' => $lastOrder->order_number,
                'username' => optional($lastOrder->user)->username,
                'type' => optional($lastOrder->orderType)->name,
                'status' => $lastOrder->status,
                'void_reason' => $lastOrder->void_reason,
                'created_at' => $lastOrder->created_at->toDateTimeString(),
            ] : null,
            'statistics' => $stats,
            'activities' => $activities,
            'can_accept' => $firstPosition && $firstPosition->user_id === $user->id && $user->isActive(),
            'can_void' => $this->canVoidLastOrder($user),
        ];
    }

    public function canVoidLastOrder(User $user): bool
    {
        $lastOrder = Order::where('status', 'COMPLETED')
            ->orderBy('id', 'desc')
            ->first();

        return $lastOrder && $lastOrder->user_id === $user->id && !Order::where('id', '>', $lastOrder->id)->exists();
    }

    protected function abort(int $status, string $message): void
    {
        throw new HttpException($status, $message);
    }

    protected function generateOrderNumber(): string
    {
        return sprintf('ORD-%s-%s', now()->format('YmdHis'), strtoupper(Str::random(4)));
    }

    protected function restoreQueueFromSnapshot(array $snapshot): void
    {
        $snapshot = collect($snapshot)->sortBy('queue_number')->values();
        $userIds = $snapshot->pluck('user_id')->toArray();
        $positions = QueuePosition::whereIn('user_id', $userIds)
            ->get()
            ->keyBy('user_id');

        // Restore using temporary offsets to avoid unique constraint violations
        foreach ($snapshot as $item) {
            $position = $positions->get($item['user_id']);
            if ($position) {
                $position->queue_number = $item['queue_number'] + 1000;
                $position->save();
            }
        }

        foreach ($snapshot as $item) {
            $position = $positions->get($item['user_id']);
            if ($position) {
                $position->queue_number = $item['queue_number'];
                $position->save();
            }
        }
    }

    public function syncQueueForUser(User $user, bool $isDeleted = false): void
    {
        DB::transaction(function () use ($user, $isDeleted) {
            if ($isDeleted) {
                $remaining = QueuePosition::active()->orderBy('queue_number')->get();
                
                foreach ($remaining as $index => $pos) {
                    $pos->queue_number = ($index + 1) + 1000;
                    $pos->save();
                }
                
                foreach ($remaining as $index => $pos) {
                    $pos->queue_number = $index + 1;
                    $pos->save();
                }
                return;
            }

            if ($user->role === 'CC' && $user->isActive()) {
                $exists = QueuePosition::active()->where('user_id', $user->id)->exists();
                if (!$exists) {
                    $maxNumber = QueuePosition::active()->max('queue_number') ?? 0;
                    QueuePosition::create([
                        'user_id' => $user->id,
                        'queue_number' => $maxNumber + 1,
                        'status' => 'ACTIVE',
                    ]);
                }
            } else {
                $position = QueuePosition::active()->where('user_id', $user->id)->first();
                if ($position) {
                    $position->delete();

                    $remaining = QueuePosition::active()->orderBy('queue_number')->get();
                    
                    foreach ($remaining as $index => $pos) {
                        $pos->queue_number = ($index + 1) + 1000;
                        $pos->save();
                    }
                    
                    foreach ($remaining as $index => $pos) {
                        $pos->queue_number = $index + 1;
                        $pos->save();
                    }
                }
            }
        });
    }
}
