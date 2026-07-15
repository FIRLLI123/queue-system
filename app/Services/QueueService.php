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

    public function getBreakQueue(): Collection
    {
        return QueuePosition::break()
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

        if ($queue->isEmpty()) {
            $this->abort(400, 'Queue is empty.');
        }

        // Any active CC user in the queue can accept an order
        $userPosition = $queue->firstWhere('user_id', $user->id);
        if (!$user->isActive() || !$userPosition) {
            $this->abort(403, 'You are not in the active queue.');
        }

        return DB::transaction(function () use ($queue, $userPosition, $user, $type) {
            $queueBefore = $this->getQueueSnapshot($queue);

            // Move the accepting user to the end of the queue
            $orderedUserIds = $queue->pluck('user_id')->toArray();
            $orderedUserIds = array_values(array_filter($orderedUserIds, fn($id) => $id !== $user->id));
            $orderedUserIds[] = $user->id;

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

    public function acceptTitipan(User $user, \App\Models\TitipanOrder $titipan): Order
    {
        $queue = $this->getActiveQueue();

        // Check if the user is in the active queue
        $userPosition = $queue->firstWhere('user_id', $user->id);
        if (!$user->isActive() || !$userPosition) {
            $this->abort(403, 'You are not in the active queue.');
        }

        return DB::transaction(function () use ($queue, $userPosition, $user, $titipan) {
            $queueBefore = $this->getQueueSnapshot($queue);

            // Move the accepting user to the end of the queue
            $orderedUserIds = $queue->pluck('user_id')->toArray();
            $orderedUserIds = array_values(array_filter($orderedUserIds, fn($id) => $id !== $user->id));
            $orderedUserIds[] = $user->id;

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

            // Update titipan order status
            $titipan->update([
                'status' => 'COMPLETED',
                'taken_by_user_id' => $user->id,
                'taken_at' => now(),
            ]);

            // Find or create 'TITIPAN' OrderType
            $type = OrderType::firstOrCreate(
                ['name' => 'TITIPAN'],
                ['status' => 'ACTIVE']
            );

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
                'description' => sprintf('%s accepted titipan order %s (%s - %s).', $user->username, $order->order_number, $titipan->requirement, $titipan->description),
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
            $this->abort(403, 'Only the CEC who accepted the last order may void it.');
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
        $breakQueue = $this->getBreakQueue();
        $queuePayload = $this->getQueueSnapshot($queue);
        $breakQueuePayload = $this->getQueueSnapshot($breakQueue);
        $firstPosition = $queue->first();
        $myPosition = QueuePosition::where('user_id', $user->id)->first();
        $lastOrder = Order::with(['orderType', 'user'])->orderBy('id', 'desc')->first();
        $activities = ActivityLog::with('user')->orderBy('id', 'desc')->limit(10)->get()->map(function (ActivityLog $activity) {
            return [
                'id' => $activity->id,
                'user_id' => $activity->user_id,
                'user' => optional($activity->user)->username,
                'name' => optional($activity->user)->name,
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

        $chats = \App\Models\Chat::where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'sender_id' => $chat->sender_id,
                    'sender_username' => optional($chat->sender)->username,
                    'sender_name' => optional($chat->sender)->name,
                    'receiver_id' => $chat->receiver_id,
                    'message' => $chat->message,
                    'is_read' => $chat->is_read,
                    'time' => $chat->created_at->format('H:i'),
                ];
            })->toArray();

        $chatUsers = \App\Models\User::where('status', 'ACTIVE')
            ->where('id', '!=', $user->id)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'username' => $u->username,
                    'name' => $u->name,
                    'role' => $u->role,
                    'is_online' => $u->isOnline(),
                ];
            })->toArray();

        $todayOrdersList = Order::with(['orderType', 'user'])
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'username' => optional($order->user)->username,
                    'type' => optional($order->orderType)->name,
                    'status' => $order->status,
                    'void_reason' => $order->void_reason,
                    'created_at' => $order->created_at->toDateTimeString(),
                ];
            })->toArray();

        return [
            'queue' => $queuePayload,
            'break_queue' => $breakQueuePayload,
            'current_turn' => [
                'user_id' => optional($firstPosition)->user_id,
                'username' => optional(optional($firstPosition)->user)->username,
                'name' => optional(optional($firstPosition)->user)->name,
                'status' => optional($firstPosition)->status,
                'last_accepted_at' => $firstPosition && $firstPosition->updated_at ? $firstPosition->updated_at->toDateTimeString() : null,
                'is_logged_in' => $firstPosition && $firstPosition->user ? $firstPosition->user->isOnline() : false,
            ],
            'my_queue_status' => $myPosition ? $myPosition->status : null,
            'last_order' => $lastOrder ? [
                'id' => $lastOrder->id,
                'order_number' => $lastOrder->order_number,
                'username' => optional($lastOrder->user)->username,
                'type' => optional($lastOrder->orderType)->name,
                'status' => $lastOrder->status,
                'void_reason' => $lastOrder->void_reason,
                'created_at' => $lastOrder->created_at->toDateTimeString(),
            ] : null,
            'today_orders' => $todayOrdersList,
            'statistics' => $stats,
            'activities' => $activities,
            'chats' => $chats,
            'chat_users' => $chatUsers,
            'can_accept' => $myPosition && $myPosition->status === 'ACTIVE' && $user->isActive(),
            'can_void' => $this->canVoidLastOrder($user),
            'titipan_orders' => \App\Models\TitipanOrder::where('status', 'CREATE')
                ->orderBy('booking_date', 'asc')
                ->orderBy('booking_time', 'asc')
                ->get()
                ->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'booking_date' => $t->booking_date->toDateString(),
                        'booking_time' => substr($t->booking_time, 0, 5),
                        'requirement' => $t->requirement,
                        'description' => $t->description,
                        'status' => $t->status,
                    ];
                })->toArray(),
        ];
    }

    public function startBreak(User $user): QueuePosition
    {
        if (!$user->isActive() || !$user->isCC()) {
            $this->abort(403, 'Only active CEC users may start a break.');
        }

        return DB::transaction(function () use ($user) {
            $position = QueuePosition::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$position) {
                $maxBreakNumber = QueuePosition::break()->max('queue_number') ?? 0;
                $position = QueuePosition::create([
                    'user_id' => $user->id,
                    'queue_number' => $maxBreakNumber + 1,
                    'status' => 'BREAK',
                ]);
            } elseif ($position->status !== 'BREAK') {
                $maxBreakNumber = QueuePosition::break()->max('queue_number') ?? 0;
                $position->queue_number = $maxBreakNumber + 1001;
                $position->status = 'BREAK';
                $position->save();
            }

            $this->normalizeQueueNumbers('ACTIVE');
            $this->normalizeQueueNumbers('BREAK');

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'BREAK_START',
                'description' => sprintf('%s mulai break/istirahat.', $user->username),
            ]);

            return $position->fresh('user');
        });
    }

    public function endBreak(User $user): QueuePosition
    {
        if (!$user->isActive() || !$user->isCC()) {
            $this->abort(403, 'Only active CEC users may return to ready.');
        }

        return DB::transaction(function () use ($user) {
            $position = QueuePosition::where('user_id', $user->id)->lockForUpdate()->first();
            $maxActiveNumber = QueuePosition::active()->max('queue_number') ?? 0;

            if (!$position) {
                $position = QueuePosition::create([
                    'user_id' => $user->id,
                    'queue_number' => $maxActiveNumber + 1,
                    'status' => 'ACTIVE',
                ]);
            } elseif ($position->status !== 'ACTIVE') {
                $position->queue_number = $maxActiveNumber + 1001;
                $position->status = 'ACTIVE';
                $position->save();
            }

            $this->normalizeQueueNumbers('BREAK');
            $this->normalizeQueueNumbers('ACTIVE');

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'BREAK_END',
                'description' => sprintf('%s kembali ready.', $user->username),
            ]);

            return $position->fresh('user');
        });
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

    protected function normalizeQueueNumbers(string $status): void
    {
        $positions = QueuePosition::where('status', $status)
            ->orderBy('queue_number')
            ->get();

        foreach ($positions as $index => $position) {
            $position->queue_number = ($index + 1) + 1000;
            $position->save();
        }

        foreach ($positions as $index => $position) {
            $position->queue_number = $index + 1;
            $position->save();
        }
    }

    public function syncQueueForUser(User $user, bool $isDeleted = false): void
    {
        DB::transaction(function () use ($user, $isDeleted) {
            if ($isDeleted) {
                $this->normalizeQueueNumbers('ACTIVE');
                $this->normalizeQueueNumbers('BREAK');
                return;
            }

            if ($user->role === 'CC' && $user->isActive()) {
                $position = QueuePosition::where('user_id', $user->id)->first();
                if (!$position) {
                    // New user: insert at FRONT (position 1) by shifting all existing users down
                    // First, shift all existing active positions up using a temp offset
                    $existingPositions = QueuePosition::active()->orderBy('queue_number')->get();
                    foreach ($existingPositions as $pos) {
                        $pos->queue_number = $pos->queue_number + 1000;
                        $pos->save();
                    }
                    // Create the new user at position 1
                    QueuePosition::create([
                        'user_id' => $user->id,
                        'queue_number' => 1,
                        'status' => 'ACTIVE',
                    ]);
                    // Shift existing users back down starting at position 2
                    foreach ($existingPositions as $index => $pos) {
                        $pos->queue_number = $index + 2;
                        $pos->save();
                    }
                }
            } else {
                $position = QueuePosition::where('user_id', $user->id)->first();
                if ($position) {
                    $position->delete();

                    $this->normalizeQueueNumbers('ACTIVE');
                    $this->normalizeQueueNumbers('BREAK');
                }
            }
        });
    }

    /**
     * Move all offline users to the end of the active queue.
     * Online users always come first and keep their relative order.
     * Offline users always come last and keep their relative order.
     * This runs on every poll, so the queue stays clean regardless of
     * cache state or when users went offline.
     */
    public function moveOfflineUsersToEnd(): void
    {
        DB::transaction(function () {
            $queue = QueuePosition::active()->with('user')->orderBy('queue_number')->get();

            if ($queue->isEmpty()) return;

            // Separate online and offline users
            $onlinePositions  = $queue->filter(fn($pos) => $pos->user && $pos->user->isOnline())->values();
            $offlinePositions = $queue->filter(fn($pos) => !$pos->user || !$pos->user->isOnline())->values();

            // Online first, then offline — both keeping their current relative order
            $reordered = $onlinePositions->concat($offlinePositions);

            // Check if the database already reflects this order
            $alreadyCorrect = true;
            foreach ($reordered as $index => $pos) {
                if ($pos->queue_number !== $index + 1) {
                    $alreadyCorrect = false;
                    break;
                }
            }
            if ($alreadyCorrect) return;

            // Apply with temp offsets to avoid unique constraint violations
            foreach ($reordered as $index => $pos) {
                $pos->queue_number = ($index + 1) + 1000;
                $pos->save();
            }
            foreach ($reordered as $index => $pos) {
                $pos->queue_number = $index + 1;
                $pos->save();
            }
        });
    }

    /**
     * Move a specific user to the absolute last position in the active queue.
     * Called immediately on logout so the queue is updated before last_seen_at expires.
     */
    public function moveUserToQueueEnd(User $user): void
    {
        DB::transaction(function () use ($user) {
            $queue = QueuePosition::active()->orderBy('queue_number')->get();

            if ($queue->isEmpty()) return;

            $userPosition = $queue->firstWhere('user_id', $user->id);
            if (!$userPosition) return;

            // Build new order: all other users first, then this user last
            $otherIds = $queue->where('user_id', '!=', $user->id)->pluck('user_id')->toArray();
            $reordered = array_values(array_merge($otherIds, [$user->id]));

            // Temp offset pass
            foreach ($reordered as $index => $userId) {
                $pos = $queue->firstWhere('user_id', $userId);
                if ($pos) {
                    $pos->queue_number = ($index + 1) + 1000;
                    $pos->save();
                }
            }
            // Final pass
            foreach ($reordered as $index => $userId) {
                $pos = $queue->firstWhere('user_id', $userId);
                if ($pos) {
                    $pos->queue_number = $index + 1;
                    $pos->save();
                }
            }
        });
    }

    public function reorderQueue(array $orderedUserIds): void
    {
        $orderedUserIds = array_map('intval', $orderedUserIds);

        DB::transaction(function () use ($orderedUserIds) {
            $positions = QueuePosition::active()->orderBy('queue_number')->get();
            $existingUserIds = $positions->pluck('user_id')->toArray();
            
            // Filter orderedUserIds to only include IDs that actually exist in the active queue
            $orderedUserIds = array_intersect($orderedUserIds, $existingUserIds);
            
            // Find any IDs that exist in the active queue but were not included in the orderedUserIds
            $missingUserIds = array_diff($existingUserIds, $orderedUserIds);
            
            // Merge them so all active queue positions are accounted for
            $finalUserIds = array_merge($orderedUserIds, $missingUserIds);
            
            // First pass: update with temporary offsets
            foreach ($finalUserIds as $index => $userId) {
                $pos = $positions->firstWhere('user_id', $userId);
                if ($pos) {
                    $pos->queue_number = ($index + 1) + 1000;
                    $pos->save();
                }
            }
            
            // Second pass: set the final queue numbers
            foreach ($finalUserIds as $index => $userId) {
                $pos = $positions->firstWhere('user_id', $userId);
                if ($pos) {
                    $pos->queue_number = $index + 1;
                    $pos->save();
                }
            }
        });

        \Illuminate\Support\Facades\Cache::put('last_queue_reorder_at', now()->timestamp, 86400);
    }

    public function getScreenData(): array
    {
        $today = now()->toDateString();
        $queue = $this->getActiveQueue();
        $breakQueue = $this->getBreakQueue();
        
        $orderCounts = Order::whereDate('created_at', $today)
            ->where('status', 'COMPLETED')
            ->select('user_id', 'order_type_id', DB::raw('count(*) as total'))
            ->groupBy('user_id', 'order_type_id')
            ->get()
            ->groupBy('user_id');

        $orderTypes = OrderType::pluck('name', 'id')->toArray();

        $mapPosition = function ($pos, string $queueStatus) use ($orderCounts, $orderTypes) {
            $userCounts = $orderCounts->get($pos->user_id) ?? collect();
            
            $counts = [];
            foreach ($orderTypes as $typeName) {
                $counts[$typeName] = 0;
            }
            
            foreach ($userCounts as $uc) {
                $typeName = $orderTypes[$uc->order_type_id] ?? null;
                if ($typeName) {
                    $counts[$typeName] = (int)$uc->total;
                }
            }
            
            return [
                'user_id' => $pos->user_id,
                'username' => optional($pos->user)->username,
                'name' => optional($pos->user)->name,
                'queue_number' => $pos->queue_number,
                'is_logged_in' => $pos->user ? $pos->user->isOnline() : false,
                'queue_status' => $queueStatus,
                'order_counts' => $counts,
            ];
        };

        $readyData = $queue->map(function ($pos, $index) use ($mapPosition) {
            $data = $mapPosition($pos, 'READY');
            $data['ready_index'] = $index;
            return $data;
        });

        $breakData = $breakQueue->map(function ($pos, $index) use ($mapPosition) {
            $data = $mapPosition($pos, 'BREAK');
            $data['break_index'] = $index;
            return $data;
        });

        // all_cc excludes offline users (online + break only)
        $allCcData = $readyData->filter(fn($d) => $d['is_logged_in'])->concat($breakData)->values();

        return [
            'queue' => $readyData->values()->toArray(),
            'break_queue' => $breakData->values()->toArray(),
            'all_cc' => $allCcData->toArray(),
            'titipan_orders' => \App\Models\TitipanOrder::where('status', 'CREATE')
                ->orderBy('booking_date', 'asc')
                ->orderBy('booking_time', 'asc')
                ->get()
                ->map(function ($t) {
                    return [
                        'id' => $t->id,
                        'booking_date' => $t->booking_date->toDateString(),
                        'booking_time' => substr($t->booking_time, 0, 5),
                        'requirement' => $t->requirement,
                        'description' => $t->description,
                        'status' => $t->status,
                    ];
                })->toArray(),
        ];
    }
}
