@extends('layouts.app')

@section('title', 'Antrian Order')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Antrian & Penerimaan Order</h1>
        <p class="mb-0 text-gray-600 fs-7">Sistem distribusi antrian Customer Care secara realtime.</p>
    </div>
    
    @if(auth()->user()->isAdmin())
        <div class="alert alert-warning py-1.5 px-3 border-0 rounded shadow-sm mb-0 fs-8 fw-semibold d-flex align-items-center gap-2">
            <i class="fas fa-eye text-warning mr-1"></i>
            <span>Mode Monitoring Admin (Aksi Dinonaktifkan)</span>
        </div>
    @else
        <div class="alert alert-success py-1.5 px-3 border-0 rounded shadow-sm mb-0 fs-8 fw-semibold d-flex align-items-center gap-2" id="cc-status-alert-top">
            <i class="fas fa-headset mr-1"></i>
            <span id="cc-status-text-top">Memuat posisi antrian...</span>
        </div>
    @endif
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow next-turn-hero-card border-0">
            <div class="card-body p-4 p-lg-5">
                <div class="next-turn-hero-layout">
                    <div class="next-turn-hero-main">
                        <div class="next-turn-hero-label">
                            <span class="next-turn-hero-chip">
                                <i class="fas fa-play mr-2"></i>Staf CEC Terdepan
                            </span>
                        </div>
                        <div class="next-turn-hero-profile">
                            <div class="next-turn-hero-avatar shadow-sm" id="current-turn-avatar">
                                --
                            </div>
                            <div class="next-turn-hero-copy">
                                <h2 class="next-turn-hero-name mb-2" id="current-turn-name">Memuat Data...</h2>
                                <div class="next-turn-hero-meta">
                                    <span class="d-inline-flex align-items-center">
                                        <span id="current-turn-status-dot" class="offline-indicator-dot mr-2"></span>
                                        <span id="current-turn-username" class="font-weight-bold">@username</span>
                                    </span>
                                    <span class="next-turn-hero-divider d-none d-md-inline">|</span>
                                    <span>Waktu Terakhir: <span id="current-turn-time" class="font-monospace">--:--:--</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="next-turn-hero-side">
                        <div class="next-turn-hero-badge">NEXT</div>
                        <p class="next-turn-hero-note mb-0">Staff ini berada di posisi terdepan untuk menerima order berikutnya secara realtime.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 4: Statistik Hari Ini -->
<div class="row mb-4">
    <!-- Stat CRM (blue, first) -->
    <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">CRM Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 font-monospace num-counter" id="stat-crm-val">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-phone fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat CMS (green, second) -->
    <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">CMS Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 font-monospace num-counter" id="stat-cms-val">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fab fa-whatsapp fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat OTHER (yellow/amber, third) -->
    <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">OTHER Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 font-monospace num-counter" id="stat-other-val">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat TOTAL -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">TOTAL Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 font-monospace num-counter" id="stat-total-val">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calculator fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    
    <!-- KOLOM KIRI (col-lg-4): Daftar Antrian CEC (TV monitoring mode) -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow mb-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users-line mr-2"></i>Daftar Antrian CEC</h6>
                <span class="badge bg-primary text-white font-weight-bold px-2 py-1" id="queue-count">0 Staf</span>
            </div>
            <div class="card-body p-3">
                <!-- Queue List container with FLIP transitions -->
                <div class="queue-list" id="queue-list-container">
                    <div class="skeleton-shimmer rounded mb-2" style="height: 64px;"></div>
                    <div class="skeleton-shimmer rounded mb-2" style="height: 64px;"></div>
                    <div class="skeleton-shimmer rounded" style="height: 64px;"></div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-mug-hot mr-2"></i>Daftar Break</h6>
                <span class="badge bg-warning text-white font-weight-bold px-2 py-1" id="break-count">0 Staf</span>
            </div>
            <div class="card-body p-3">
                <div class="break-queue-list" id="break-list-container">
                    <div class="break-empty-state text-center py-3">
                        <i class="fas fa-mug-hot text-muted mb-2"></i>
                        <p class="text-secondary mb-0" style="font-size: 11px;">Tidak ada CEC yang sedang break.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Global status chip di bawah antrian -->
        <div class="alert alert-warning border-0 shadow-sm rounded p-3 mb-0 d-none" id="global-waiting-chip" style="background-color: var(--warning-light);">
            <div class="d-flex align-items-center fs-8 fw-semibold text-warning" style="gap: 8px;">
                <i class="fas fa-hourglass-start fa-spin"></i>
                <span id="global-waiting-text">Menunggu antrian CEC...</span>
            </div>
        </div>
    </div>
    
    <!-- KOLOM KANAN (col-lg-8): Form Aksi CEC atau Monitor & Log -->
    <div class="col-xl-8 col-lg-7">
        
        <div class="row">
            <!-- Titipan Order Alerts Panel -->
            <div class="col-12 mb-4 d-none" id="titipan-alerts-card">
                <div class="card border-left-warning shadow py-2" style="background-color: #fffbeb; border-left: .25rem solid #eab308 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-bell text-warning mr-2" style="font-size: 20px;"></i>
                            <h6 class="m-0 font-weight-bold text-dark fs-7">Booking Titipan Order Menunggu Tindakan</h6>
                        </div>
                        <div id="titipan-alerts-list" class="d-flex flex-column gap-3" style="gap: 12px;">
                            <!-- Dynamically populated -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1. Form Terima Order (Role-Based) -->
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i>Penerimaan Order Baru</h6>
                        
                        <!-- Mini status position for CEC -->
                        @if(auth()->user()->role === 'CC')
                            <span class="badge badge-secondary" id="cc-sidebar-position-badge">MENGANTRI</span>
                        @endif
                    </div>
                    <div class="card-body p-4 position-relative">
                        
                        <!-- Overlay Peringatan untuk Admin (Read-Only) -->
                        @if(auth()->user()->isAdmin())
                            <div class="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-3 rounded" style="
                                top: 0; left: 0; background-color: rgba(255, 255, 255, 0.82); z-index: 100;
                            ">
                                <div class="bg-gray-100 rounded-circle p-3 mb-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-lock text-gray-500 fs-4"></i>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-1">Aksi Khusus Staf Customer Care</h6>
                                <p class="text-secondary fs-8 mb-0" style="max-width: 320px;">Anda masuk sebagai Admin. Panel ini hanya menampilkan monitoring aktivitas secara realtime.</p>
                            </div>
                        @endif

                        <form id="accept-order-form">
                            @csrf
                            <div class="mb-4">
                                <label class="text-xs font-weight-bold text-gray-600 text-uppercase mb-3 d-block">PILIH TIPE ORDER</label>
                                
                                <div class="row g-3" id="order-types-container">
                                    <!-- AJAX loaded list -->
                                    <div class="col-4">
                                        <div class="skeleton-shimmer rounded" style="height: 100px;"></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="skeleton-shimmer rounded" style="height: 100px;"></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="skeleton-shimmer rounded" style="height: 100px;"></div>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="order_type_id" id="selected-order-type-id" value="">
                            </div>

                            <!-- Alert Error jika belum pilih tipe order -->
                            <div id="accept-order-error" class="alert alert-danger py-2 px-3 border-0 rounded shadow-sm d-none mb-3" style="font-size: 11px; font-weight: 600;">
                                <i class="fas fa-exclamation-circle mr-1"></i> Silakan pilih tipe order (CRM/CMS/OTHER) terlebih dahulu!
                            </div>

                            @if(auth()->user()->role === 'CC')
                                <div class="break-control-panel mb-3">
                                    <div>
                                        <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">STATUS KESIAPAN</div>
                                        <div class="break-control-copy" id="break-status-copy">Anda sedang ready dalam antrian.</div>
                                    </div>
                                    <button type="button" id="btn-toggle-break" class="btn btn-warning font-weight-bold shadow-sm">
                                        <i class="fas fa-mug-hot mr-2"></i>Break
                                    </button>
                                </div>
                            @endif

                            <button type="submit" id="btn-accept-order" class="btn btn-secondary w-100 py-3 font-weight-bold shadow-sm" disabled style="height: 52px; font-size: 16px;">
                                Belum Giliran Anda
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 2. Void Order Terakhir (Khusus CC) -->
            @if(auth()->user()->role === 'CC')
            <div class="col-12 mb-4 d-none" id="void-order-card">
                <div class="card border-left-danger shadow py-2" style="background-color: var(--danger-light);">
                    <div class="card-body py-3">
                        <div class="row align-items-center no-gutters">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Batalkan Order Terakhir</div>
                                <div class="h6 mb-1 font-weight-bold text-gray-800 font-monospace" id="void-order-number">ORD-XXXX</div>
                                <div class="text-xs text-gray-600">
                                    Tipe: <span class="badge bg-white text-dark border font-weight-bold" id="void-order-type">--</span> •
                                    Waktu: <span id="void-order-time" class="font-monospace">--:--:--</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-danger font-weight-bold shadow-sm" data-toggle="modal" data-target="#voidConfirmModal">
                                    Void Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="row">
            <!-- 4. Tabel Log Order -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-list mr-2"></i>Log Order</h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 fs-8">
                                <thead class="bg-light text-secondary text-uppercase" style="font-size: 10px; position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th class="pl-4">Order</th>
                                        <th>CC</th>
                                        <th>Tipe</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="orders-table-body">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-circle-notch fa-spin mr-2"></i>Memuat order...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Log Aktivitas Terakhir -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Log Aktivitas</h6>
                    </div>
                    <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                        <div class="activity-feed-list" id="activity-feed-container">
                            <div class="text-center py-4 text-muted fs-8">
                                <i class="fas fa-circle-notch fa-spin mr-2"></i>Memuat log...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
</div>

<!-- MODAL VOID CONFIRMATION (Khusus CC) -->
@if(auth()->user()->role === 'CC')
<div class="modal fade" id="voidConfirmModal" tabindex="-1" role="dialog" aria-labelledby="voidConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 450px;">
        <div class="modal-content border-0 shadow rounded-lg">
            <div class="modal-header border-bottom-0 pb-0 pl-4 pt-4">
                <h5 class="modal-title font-weight-bold text-dark fs-6" id="voidConfirmModalLabel">Void Order <span id="modal-void-order-num" class="font-monospace">#ORD-XXX</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pl-4 pr-4 pt-3 pb-4">
                <form id="void-order-form">
                    @csrf
                    
                    <label class="form-label fs-8 font-weight-bold text-secondary mb-2">Pilih Alasan Void *</label>
                    <div class="d-flex flex-column gap-2 mb-3" style="gap: 8px;">
                        <div class="form-check-custom">
                            <input class="form-check-input-custom" type="radio" name="preset_reason" id="reason-1" value="Customer batal">
                            <label class="form-check-label-custom" for="reason-1">Customer batal</label>
                        </div>
                        <div class="form-check-custom">
                            <input class="form-check-input-custom" type="radio" name="preset_reason" id="reason-2" value="Salah klik">
                            <label class="form-check-label-custom" for="reason-2">Salah klik</label>
                        </div>
                        <div class="form-check-custom">
                            <input class="form-check-input-custom" type="radio" name="preset_reason" id="reason-3" value="Data tidak valid">
                            <label class="form-check-label-custom" for="reason-3">Data tidak valid</label>
                        </div>
                        <div class="form-check-custom">
                            <input class="form-check-input-custom" type="radio" name="preset_reason" id="reason-4" value="Duplicate order">
                            <label class="form-check-label-custom" for="reason-4">Duplicate order</label>
                        </div>
                        <div class="form-check-custom">
                            <input class="form-check-input-custom" type="radio" name="preset_reason" id="reason-other" value="OTHER">
                            <label class="form-check-label-custom" for="reason-other">Lainnya...</label>
                        </div>
                    </div>

                    <!-- Textarea Alasan Lainnya -->
                    <div class="mb-4 d-none" id="custom-reason-container">
                        <label for="custom_reason" class="form-label fs-8 font-weight-bold text-secondary">Tulis Alasan Lainnya *</label>
                        <textarea class="form-control fs-8 rounded" id="custom_reason" name="custom_reason" rows="3" placeholder="Masukkan detail alasan pembatalan..."></textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end border-top pt-3" style="gap: 8px;">
                        <button type="button" class="btn btn-light px-3 py-2 font-weight-bold rounded fs-8" data-dismiss="modal">Batal</button>
                        <button type="submit" id="btn-confirm-void" class="btn btn-danger px-4 py-2 font-weight-bold rounded fs-8 shadow-sm" disabled>
                            Konfirmasi Void
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- WIDGET CHAT LIVE INTERNAL -->
<div class="chat-widget-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'Inter', sans-serif;">
    <!-- Chat Button Trigger -->
    <button class="chat-trigger-btn btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" id="chat-trigger-btn" style="width: 60px; height: 60px; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); border: none; background: linear-gradient(135deg, #4f46e5, #6366f1); position: relative;">
        <i class="fas fa-comments text-white" style="font-size: 24px;"></i>
        <span class="badge badge-danger position-absolute d-none" id="chat-unread-badge" style="top: -2px; right: -2px; border-radius: 50%; padding: 4px 7px; font-size: 10px; border: 2px solid white; font-weight: bold;">0</span>
    </button>

    <!-- Chat Window Box -->
    <div class="chat-box shadow-lg border-0 rounded-lg d-none" id="chat-box-window" style="position: absolute; bottom: 75px; right: 0; width: 350px; height: 460px; background: white; transition: all 0.3s ease; display: flex; flex-direction: column; overflow: hidden; border-radius: 12px !important;">
        <!-- Chat Header -->
        <div class="chat-header text-white px-3 py-2 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #4f46e5, #6366f1); height: 60px;">
            <div class="d-flex align-items-center">
                <!-- Back Button -->
                <button class="btn btn-link text-white p-0 mr-2 d-none" id="chat-back-btn" style="text-decoration: none; outline: none; box-shadow: none;">
                    <i class="fas fa-chevron-left" style="font-size: 16px;"></i>
                </button>
                <div style="line-height: 1.2;">
                    <h6 class="mb-0 font-weight-bold" id="chat-title" style="font-size: 14px; color: white;">Pesan Internal CC</h6>
                    <small id="chat-subtitle" class="text-white-50" style="font-size: 10px;">Pilih rekan untuk mulai chat</small>
                </div>
            </div>
            <button class="close text-white" id="chat-close-btn" style="opacity: 0.8; font-size: 20px; outline: none; border: none; background: none; margin-top: -2px;">
                &times;
            </button>
        </div>

        <!-- Chat Body -->
        <div class="chat-body" style="flex: 1; overflow-y: auto; background-color: #f8fafc; position: relative; display: flex; flex-direction: column;">
            <!-- User List Panel -->
            <div id="chat-user-list-panel" class="p-2 h-100" style="overflow-y: auto;">
                <div class="text-muted text-center py-4 fs-8">Memuat daftar rekan...</div>
            </div>

            <!-- Message Thread Panel -->
            <div id="chat-thread-panel" class="d-none h-100" style="display: none; flex-direction: column; overflow: hidden; flex: 1;">
                <!-- Messages List -->
                <div id="chat-messages-container" class="p-3" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                    <!-- Messages will be injected here -->
                </div>
            </div>
        </div>

        <!-- Chat Footer -->
        <div class="chat-footer p-2 border-top bg-white d-none" id="chat-footer-panel" style="display: none; height: 60px; align-items: center;">
            <form id="chat-send-form" class="d-flex align-items-center w-100" style="gap: 8px; margin: 0;">
                <input type="text" class="form-control form-control-sm border-0 bg-light px-3 py-2" id="chat-message-input" placeholder="Tulis pesan..." autocomplete="off" style="font-size: 13px; border-radius: 20px; outline: none; box-shadow: none; flex: 1; height: 36px;">
                <button type="submit" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border: none; background: #4f46e5; outline: none; flex-shrink: 0; box-shadow: 0 2px 5px rgba(79, 70, 229, 0.3);">
                    <i class="fas fa-paper-plane text-white" style="font-size: 12px; margin-left: 2px;"></i>
                </button>
            </form>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentUserId = {{ auth()->user()->id }};
        const userRole = '{{ auth()->user()->role }}';
        let isFirstTurnNotification = false;
        let lastOrderNumber = null;
        let selectedTypeId = null;
        let currentStats = { CRM: 0, CMS: 0, OTHER: 0, TOTAL: 0 };
        let lastTurnUserId = null;
        let lastOnlineStates = {};
        const processedActivityIds = new Set();
        let hasLoadedActivities = false;

        // 5-minute warning state
        let firstPositionUserId = null;
        let firstPositionSince = null;
        let lastWarningAt = null;
        const WARNING_INTERVAL_MS = 5 * 60 * 1000;
        let lastTitipanWarningAt = {};

        function isSoundEnabled() {
            return localStorage.getItem('dashboard-sound-enabled') !== 'false';
        }

        // Penanganan Voice List Lintas Browser (Safari/Firefox/Chrome Async Loader)
        let indonesianVoice = null;
        function loadVoices() {
            if ('speechSynthesis' in window) {
                const voices = window.speechSynthesis.getVoices();
                indonesianVoice = voices.find(v => 
                    v.lang === 'id-ID' || 
                    v.lang === 'id_ID' || 
                    v.lang.toLowerCase().startsWith('id-') || 
                    v.lang.toLowerCase().startsWith('id_') || 
                    v.lang.toLowerCase() === 'id' || 
                    v.name.toLowerCase().includes('indonesia')
                );
            }
        }
        
        loadVoices();
        if ('speechSynthesis' in window && window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
        }

        function speakAnnouncement(text) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text.toLowerCase());
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                if (!indonesianVoice) {
                    loadVoices();
                }
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                }
                window.speechSynthesis.speak(utterance);
            }
        }

        // Web Audio API Synthesized Chime (Ting-Tung)
        function playChime() {
            return new Promise((resolve) => {
                if (!isSoundEnabled()) return resolve();
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return resolve();
                    
                    const audioCtx = new AudioContext();
                    
                    // Nada 1: Ting (C5)
                    const osc1 = audioCtx.createOscillator();
                    const gain1 = audioCtx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
                    gain1.gain.setValueAtTime(0.12, audioCtx.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
                    osc1.connect(gain1);
                    gain1.connect(audioCtx.destination);
                    osc1.start();
                    osc1.stop(audioCtx.currentTime + 0.6);
                    
                    // Nada 2: Tung (G4) setelah delay 0.22s
                    setTimeout(() => {
                        try {
                            const osc2 = audioCtx.createOscillator();
                            const gain2 = audioCtx.createGain();
                            osc2.type = 'sine';
                            osc2.frequency.setValueAtTime(392.00, audioCtx.currentTime); // G4
                            gain2.gain.setValueAtTime(0.12, audioCtx.currentTime);
                            gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
                            osc2.connect(gain2);
                            gain2.connect(audioCtx.destination);
                            osc2.start();
                            osc2.stop(audioCtx.currentTime + 0.6);
                            
                            setTimeout(resolve, 600);
                        } catch (err) {
                            resolve();
                        }
                    }, 220);
                } catch (e) {
                    console.warn("Web Audio API chime error:", e);
                    resolve();
                }
            });
        }

        // Web Speech API Text-to-Speech (Bahasa Indonesia)
        async function playQueueVoice(name) {
            if (!isSoundEnabled()) return;
            await playChime();
            
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                
                let speakName = name.toLowerCase();
                speakName = speakName.replace(/1/g, ' satu')
                                     .replace(/2/g, ' dua')
                                     .replace(/3/g, ' tiga')
                                     .replace(/4/g, ' empat')
                                     .replace(/5/g, ' lima')
                                     .replace(/6/g, ' enam')
                                     .replace(/7/g, ' tujuh')
                                     .replace(/8/g, ' delapan')
                                     .replace(/9/g, ' sembilan')
                                     .replace(/0/g, ' nol');

                const utterance = new SpeechSynthesisUtterance('Antrian selanjutnya, ' + speakName);
                utterance.lang = 'id-ID';
                utterance.rate = 0.85; 
                utterance.pitch = 1.0;
                
                if (!indonesianVoice) {
                    loadVoices();
                }
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                }
                
                window.speechSynthesis.speak(utterance);
            }
        }

        // Web Audio API untuk Login Chime (Arpeggio Naik)
        function playLoginChime() {
            return new Promise((resolve) => {
                if (!isSoundEnabled()) return resolve();
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return resolve();
                    const audioCtx = new AudioContext();
                    const now = audioCtx.currentTime;
                    const freqs = [261.63, 329.63, 392.00]; 
                    freqs.forEach((freq, index) => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, now + (index * 0.08));
                        gain.gain.setValueAtTime(0.08, now + (index * 0.08));
                        gain.gain.exponentialRampToValueAtTime(0.001, now + (index * 0.08) + 0.25);
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.start(now + (index * 0.08));
                        osc.stop(now + (index * 0.08) + 0.25);
                    });
                    setTimeout(resolve, 500);
                } catch (e) {
                    resolve();
                }
            });
        }

        // Web Audio API untuk Logout Chime (Arpeggio Turun)
        function playLogoutChime() {
            return new Promise((resolve) => {
                if (!isSoundEnabled()) return resolve();
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return resolve();
                    const audioCtx = new AudioContext();
                    const now = audioCtx.currentTime;
                    const freqs = [392.00, 329.63, 261.63]; 
                    freqs.forEach((freq, index) => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, now + (index * 0.08));
                        gain.gain.setValueAtTime(0.08, now + (index * 0.08));
                        gain.gain.exponentialRampToValueAtTime(0.001, now + (index * 0.08) + 0.25);
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.start(now + (index * 0.08));
                        osc.stop(now + (index * 0.08) + 0.25);
                    });
                    setTimeout(resolve, 500);
                } catch (e) {
                    resolve();
                }
            });
        }

        // Suara Panggilan Login Staf CC
        async function speakCCLogin(name) {
            if (!isSoundEnabled()) return;
            await playLoginChime();
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let speakName = name.toLowerCase().replace(/1/g, ' satu').replace(/2/g, ' dua').replace(/3/g, ' tiga').replace(/4/g, ' empat').replace(/5/g, ' lima');
                const utterance = new SpeechSynthesisUtterance(speakName + ' telah online');
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                
                if (!indonesianVoice) {
                    loadVoices();
                }
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                }
                
                window.speechSynthesis.speak(utterance);
            }
        }

        // Suara Panggilan Logout Staf CC
        async function speakCCLogout(name) {
            if (!isSoundEnabled()) return;
            await playLogoutChime();
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let speakName = name.toLowerCase().replace(/1/g, ' satu').replace(/2/g, ' dua').replace(/3/g, ' tiga').replace(/4/g, ' empat').replace(/5/g, ' lima');
                const utterance = new SpeechSynthesisUtterance(speakName + ' telah offline');
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                
                if (!indonesianVoice) {
                    loadVoices();
                }
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                }
                
                window.speechSynthesis.speak(utterance);
            }
        }

        // Suara Panggilan Break CC
        async function speakCCBreak(name, isReady) {
            if (!isSoundEnabled()) return;
            await (isReady ? playLoginChime() : playLogoutChime());
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let speakName = name.toLowerCase().replace(/1/g, ' satu')
                                    .replace(/2/g, ' dua')
                                     .replace(/3/g, ' tiga')
                                    .replace(/4/g, ' empat')
                                    .replace(/5/g, ' lima')
                                    .replace(/6/g, ' enam')
                                    .replace(/7/g, ' tujuh')
                                    .replace(/8/g, ' delapan')
                                    .replace(/9/g, ' sembilan')
                                    .replace(/0/g, ' nol');
                const message = isReady
                    ? speakName + ' kembali ready'
                    : speakName + ' sedang break atau istirahat';
                const utterance = new SpeechSynthesisUtterance(message);
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;

                if (!indonesianVoice) {
                    loadVoices();
                }
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                }

                window.speechSynthesis.speak(utterance);
            }
        }

        // Animasi Count-Up
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const val = Math.floor(progress * (end - start) + start);
                obj.innerHTML = val.toLocaleString();
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    obj.innerHTML = end.toLocaleString();
                }
            };
            window.requestAnimationFrame(step);
        }

        function updateTitipanDashboard(titipanOrders, canAccept) {
            const cardContainer = document.getElementById('titipan-alerts-card');
            const listContainer = document.getElementById('titipan-alerts-list');
            if (!cardContainer || !listContainer) return;

            if (!titipanOrders || titipanOrders.length === 0) {
                cardContainer.classList.add('d-none');
                listContainer.innerHTML = '';
                return;
            }

            cardContainer.classList.remove('d-none');
            let html = '';
            const now = new Date();

            titipanOrders.forEach(item => {
                const dateStr = item.booking_date; // YYYY-MM-DD
                const timeStr = item.booking_time; // HH:MM
                const bookingDateTime = new Date(`${dateStr}T${timeStr}:00`);
                const timeDiffMs = bookingDateTime.getTime() - now.getTime();
                const timeDiffMinutes = timeDiffMs / (60 * 1000);

                // Format date
                const d = new Date(item.booking_date);
                const formattedDate = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                const formattedTime = item.booking_time.substring(0, 5);

                // Play voice reminder 1 hour before booking (60 minutes) and repeat every 15 minutes
                if (timeDiffMinutes <= 60 && item.status === 'CREATE') {
                    const lastPlay = lastTitipanWarningAt[item.id] || 0;
                    if (now.getTime() - lastPlay >= 15 * 60 * 1000) {
                        lastTitipanWarningAt[item.id] = now.getTime();
                        if (isSoundEnabled()) {
                            const voiceText = `Pemberitahuan, ada titipan booking antrian untuk pukul ${formattedTime} kebutuhan ${item.requirement}`;
                            speakAnnouncement(voiceText);
                        }
                    }
                }

                // If userRole !== 'CC', or canAccept is false, disable button
                const disabledAttr = (userRole !== 'CC' || !canAccept) ? 'disabled' : '';
                const btnTitle = (userRole !== 'CC') 
                    ? 'Hanya Staf CC yang dapat mengambil titipan order.' 
                    : (!canAccept ? 'Anda harus ready di antrian untuk mengambil titipan.' : 'Terima Titipan Order ini.');

                html += `
                    <div class="p-3 rounded border" style="background-color: #fff; border-color: rgba(234,179,8,0.2) !important;">
                        <div class="row align-items-center">
                            <div class="col-md-9 mb-2 mb-md-0">
                                <div class="d-flex flex-wrap align-items-center mb-1" style="gap: 8px;">
                                    <span class="badge bg-light text-warning px-2.5 py-1 rounded-pill border border-warning fs-8 font-weight-bold"><i class="fas fa-clipboard-list mr-1"></i>${item.requirement}</span>
                                    <span class="badge bg-warning text-dark px-2 py-1 fs-8 font-monospace font-weight-bold"><i class="far fa-clock mr-1"></i>Pukul ${formattedTime}</span>
                                    <span class="text-dark font-weight-bold fs-8">${formattedDate}</span>
                                </div>
                                <div class="text-secondary fs-8">${item.description || 'Tidak ada deskripsi.'}</div>
                            </div>
                            <div class="col-md-3 text-right">
                                <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 font-weight-bold accept-titipan-btn" data-id="${item.id}" ${disabledAttr} title="${btnTitle}">
                                    <i class="fas fa-check-circle mr-1"></i> Terima Titipan
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            listContainer.innerHTML = html;
        }

        function updateStats(newStats) {
            const keys = ['CRM', 'CMS', 'OTHER', 'TOTAL'];
            keys.forEach(key => {
                const el = document.getElementById(`stat-${key.toLowerCase()}-val`);
                if (!el) return;
                const newVal = newStats[key] || 0;
                const oldVal = currentStats[key] || 0;
                if (newVal !== oldVal) {
                    animateValue(el, oldVal, newVal, 600);
                }
            });
            currentStats = { ...newStats };
        }

        // Fetch Daftar Tipe Order Aktif
        function fetchActiveOrderTypes() {
            fetch('/order-types')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('order-types-container');
                    let html = '';
                    if (data.order_types && data.order_types.length > 0) {
                        const ORDER_MAPPING = { 'CRM': 0, 'CMS': 1, 'OTHER': 2 };
                        data.order_types.sort((a, b) => (ORDER_MAPPING[a.name] ?? 99) - (ORDER_MAPPING[b.name] ?? 99));
                        data.order_types.forEach(type => {
                            let iconHtml = '<i class="fas fa-phone" style="font-size: 24px; color: #2563eb;"></i>';
                            let desc = 'Telepon CRM';
                            if (type.name === 'CMS') {
                                iconHtml = '<i class="fab fa-whatsapp" style="font-size: 28px; color: #16a34a;"></i>';
                                desc = 'WhatsApp CMS';
                            } else if (type.name === 'OTHER') {
                                iconHtml = '<i class="fas fa-cog" style="font-size: 24px; color: #d97706;"></i>';
                                desc = 'Lain-lain';
                            }

                            // Jika admin, card tipe order otomatis dinonaktifkan
                            const disabledClass = userRole === 'ADMIN' ? 'disabled' : '';

                            html += `
                                <div class="col-4">
                                    <div class="custom-toggle-card p-3 text-center border rounded h-100 ${disabledClass}" 
                                         data-id="${type.id}" 
                                         style="transition: all 0.2s ease; border-color: var(--border); background-color: var(--card);">
                                         <div class="mb-2" style="height: 32px; display: flex; align-items: center; justify-content: center;">
                                             ${iconHtml}
                                         </div>
                                         <h6 class="font-weight-bold mb-1 text-dark fs-8">${type.name}</h6>
                                         <p class="text-secondary mb-0" style="font-size: 10px;">${desc}</p>
                                     </div>
                                 </div>
                            `;
                        });
                    } else {
                        html = `
                            <div class="col-12">
                                <div class="alert alert-warning border-0 fs-8 py-3 text-center rounded">
                                    Tidak ada tipe order aktif.
                                </div>
                            </div>
                        `;
                    }
                    container.innerHTML = html;

                    // Bind klik event jika bukan admin
                    if (userRole !== 'ADMIN') {
                        document.querySelectorAll('.custom-toggle-card').forEach(card => {
                            card.addEventListener('click', function() {
                                document.querySelectorAll('.custom-toggle-card').forEach(c => {
                                    c.classList.remove('selected');
                                    c.style.borderColor = 'var(--border)';
                                    c.style.backgroundColor = 'var(--card)';
                                    c.style.boxShadow = 'none';
                                });
                                
                                this.classList.add('selected');
                                this.style.borderColor = 'var(--primary)';
                                this.style.backgroundColor = 'var(--primary-light)';
                                this.style.boxShadow = '0 0 0 3px var(--primary-glow)';
                                
                                selectedTypeId = this.getAttribute('data-id');
                                document.getElementById('selected-order-type-id').value = selectedTypeId;
                            });
                        });
                    }
                })
                .catch(err => console.error('Gagal mengambil tipe order:', err));
        }

        fetchActiveOrderTypes();

        // Polling loop
        window.startRealtimePolling(function(data) {
            // 1. Render Antrian — hanya tampilkan yang online (is_logged_in = true)
            const onlineQueue = (data.queue || []).filter(pos => pos.is_logged_in);
            window.renderQueueList('queue-list-container', onlineQueue);
            document.getElementById('queue-count').innerText = `${onlineQueue.length} Staf`;
            window.renderBreakQueueList('break-list-container', data.break_queue || []);
            document.getElementById('break-count').innerText = `${(data.break_queue || []).length} Staf`;
            updateTitipanDashboard(data.titipan_orders, data.can_accept);

            // Deteksi perubahan status online/offline staf CC
            if (data.queue && data.queue.length > 0) {
                const isInitialLoad = Object.keys(lastOnlineStates).length === 0;
                
                data.queue.forEach(cc => {
                    const userId = cc.user_id;
                    const isLoggedInNow = cc.is_logged_in ? true : false;
                    const wasLoggedIn = lastOnlineStates[userId];

                    if (!isInitialLoad && wasLoggedIn !== undefined && wasLoggedIn !== isLoggedInNow) {
                        if (isLoggedInNow) {
                            speakCCLogin(cc.name);
                        } else {
                            speakCCLogout(cc.name);
                        }
                    }
                    lastOnlineStates[userId] = isLoggedInNow;
                });
            }

            // 2. Info CC Terdepan (Next)
            const current = data.current_turn;
            const avatar = document.getElementById('current-turn-avatar');
            const nameEl = document.getElementById('current-turn-name');
            const usernameEl = document.getElementById('current-turn-username');
            const statusDot = document.getElementById('current-turn-status-dot');
            const timeEl = document.getElementById('current-turn-time');
            const globalWaitingChip = document.getElementById('global-waiting-chip');
            const globalWaitingText = document.getElementById('global-waiting-text');

            if (current && current.user_id) {
                const initials = current.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                avatar.innerText = initials;
                
                nameEl.innerText = current.name;
                usernameEl.innerText = `@${current.username}`;
                timeEl.innerText = current.last_accepted_at ? current.last_accepted_at.substring(11, 19) : '--:--:--';
                
                if (current.is_logged_in) {
                    statusDot.className = 'online-indicator-dot pulse-green mr-2';
                    statusDot.setAttribute('title', 'CEC Online');
                    globalWaitingChip.classList.remove('d-none');
                    globalWaitingText.innerText = `Menunggu ${current.name} menerima order`;
                } else {
                    statusDot.className = 'offline-indicator-dot mr-2';
                    statusDot.setAttribute('title', 'CEC Belum Login (Offline)');
                    globalWaitingChip.classList.remove('d-none');
                    globalWaitingText.innerText = `Menunggu ${current.name} login untuk menerima order`;
                }

                // Mainkan suara panggilan jika CEC terdepan bergeser/berubah
                if (lastTurnUserId !== null && lastTurnUserId !== current.user_id) {
                    playQueueVoice(current.name);
                }
                lastTurnUserId = current.user_id;
            } else {
                avatar.innerText = '--';
                nameEl.innerText = 'Antrian Kosong';
                usernameEl.innerText = '@-';
                timeEl.innerText = '--:--:--';
                statusDot.className = 'offline-indicator-dot mr-2';
                globalWaitingChip.classList.add('d-none');
                lastTurnUserId = null;
            }

            // 3. Update Statistik
            updateStats(data.statistics);

            // 4. Update Aksi Form Khusus CC
            if (userRole === 'CC') {
                const myQueueItem = data.queue.find(item => parseInt(item.user_id) === currentUserId);
                const myBreakItem = (data.break_queue || []).find(item => parseInt(item.user_id) === currentUserId);
                const btnAccept = document.getElementById('btn-accept-order');
                const btnBreak = document.getElementById('btn-toggle-break');
                const breakStatusCopy = document.getElementById('break-status-copy');
                const positionBadgeTop = document.getElementById('cc-status-text-top');
                const sidebarBadge = document.getElementById('cc-sidebar-position-badge');

                if (myBreakItem || data.my_queue_status === 'BREAK') {
                    const breakPos = myBreakItem ? (data.break_queue || []).indexOf(myBreakItem) + 1 : '-';
                    if (sidebarBadge) {
                        sidebarBadge.innerText = 'BREAK';
                        sidebarBadge.className = 'badge badge-warning';
                    }
                    if (positionBadgeTop) {
                        positionBadgeTop.innerHTML = `<i class="fas fa-mug-hot mr-1"></i> Anda sedang break (List Break #${breakPos})`;
                    }
                    if (btnBreak) {
                        btnBreak.className = 'btn btn-success font-weight-bold shadow-sm';
                        btnBreak.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Ready';
                        btnBreak.dataset.mode = 'ready';
                    }
                    if (breakStatusCopy) {
                        breakStatusCopy.innerText = `Anda sedang break. Klik Ready untuk masuk lagi ke antrian aktif paling bawah.`;
                    }
                    btnAccept.setAttribute('disabled', 'true');
                    btnAccept.className = 'btn btn-secondary w-100 py-3 font-weight-bold shadow-sm';
                    btnAccept.innerText = 'Sedang Break';
                    isFirstTurnNotification = false;
                } else if (myQueueItem) {
                    const myPos = data.queue.indexOf(myQueueItem) + 1;
                    if (sidebarBadge) {
                        sidebarBadge.innerText = myPos === 1 ? 'GILIRAN ANDA' : `ANTRIAN #${myPos}`;
                        sidebarBadge.className = myPos === 1 ? 'badge badge-primary' : 'badge badge-secondary';
                    }
                    if (positionBadgeTop) {
                        positionBadgeTop.innerHTML = `<i class="fas fa-headset mr-1"></i> Posisi Anda: Antrian #${myPos} (${myPos === 1 ? 'GILIRAN ANDA' : 'Mengantri'})`;
                    }
                    if (btnBreak) {
                        btnBreak.className = 'btn btn-warning font-weight-bold shadow-sm';
                        btnBreak.innerHTML = '<i class="fas fa-mug-hot mr-2"></i>Break';
                        btnBreak.dataset.mode = 'break';
                    }
                    if (breakStatusCopy) {
                        breakStatusCopy.innerText = `Anda ready di antrian #${myPos}. Klik Break untuk keluar sementara dari antrian ready.`;
                    }

                    // Any CC in the active queue can now accept an order
                    btnAccept.removeAttribute('disabled');
                    btnAccept.className = 'btn btn-primary w-100 py-3 font-weight-bold shadow-sm';
                    btnAccept.innerText = myPos === 1 ? 'Terima Order (Giliran Anda)' : `Terima Order (Antrian #${myPos})`;

                    if (myPos === 1 && !isFirstTurnNotification) {
                        window.showToast('Giliran Anda sekarang! Silakan pilih tipe order dan terima.', 'primary');
                        isFirstTurnNotification = true;
                    } else if (myPos !== 1) {
                        isFirstTurnNotification = false;
                    }
                } else {
                    if (sidebarBadge) {
                        sidebarBadge.innerText = 'OFFLINE';
                        sidebarBadge.className = 'badge badge-warning';
                    }
                    if (positionBadgeTop) {
                        positionBadgeTop.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i> Anda tidak aktif dalam antrian`;
                    }
                    if (btnBreak) {
                        btnBreak.className = 'btn btn-warning font-weight-bold shadow-sm';
                        btnBreak.innerHTML = '<i class="fas fa-mug-hot mr-2"></i>Break';
                        btnBreak.dataset.mode = 'break';
                    }
                    if (breakStatusCopy) {
                        breakStatusCopy.innerText = 'Anda belum aktif dalam antrian.';
                    }
                    btnAccept.setAttribute('disabled', 'true');
                    btnAccept.className = 'btn btn-secondary w-100 py-3 font-weight-bold shadow-sm';
                    btnAccept.innerText = 'Tidak Aktif di Antrian';
                    isFirstTurnNotification = false;
                }

                // Void card visibility
                const voidCard = document.getElementById('void-order-card');
                if (data.can_void && data.last_order) {
                    const order = data.last_order;
                    document.getElementById('void-order-number').innerText = order.order_number;
                    document.getElementById('void-order-type').innerText = order.type;
                    document.getElementById('void-order-time').innerText = order.created_at.substring(11, 19);
                    
                    const mVoidNum = document.getElementById('modal-void-order-num');
                    if (mVoidNum) mVoidNum.innerText = `#${order.order_number}`;
                    
                    voidCard.classList.remove('d-none');
                } else {
                    voidCard.classList.add('d-none');
                }
            }

            // 5. Update Tabel Log Order
            const tableBody = document.getElementById('orders-table-body');
            if (data.today_orders && data.today_orders.length > 0) {
                let html = '';
                data.today_orders.forEach(order => {
                    const badgeClass = order.status === 'COMPLETED' ? 'badge-success-custom' : 'badge-danger-custom';
                    const typeBadgeClass = order.type === 'CRM' ? 'bg-success text-white' : (order.type === 'CMS' ? 'bg-primary text-white' : 'bg-purple text-white');
                    
                    // Format waktu: HH:MM dari created_at
                    const orderTime = order.created_at.substring(11, 16);
                    
                    html += `
                        <tr class="fade-slide-up">
                            <td class="font-monospace font-weight-bold text-dark pl-4">${order.order_number}</td>
                            <td><span class="font-weight-bold text-gray-700">@${order.username}</span></td>
                            <td><span class="badge px-2.5 py-1 ${typeBadgeClass}" style="font-size: 10px;">${order.type}</span></td>
                            <td class="font-monospace text-secondary fs-8">${orderTime}</td>
                            <td><span class="badge-pill-custom ${badgeClass}">${order.status}</span></td>
                        </tr>
                    `;
                });
                tableBody.innerHTML = html;

                if (data.last_order) {
                    const order = data.last_order;
                    if (lastOrderNumber && lastOrderNumber !== order.order_number && order.status === 'COMPLETED') {
                        window.showToast(`Order ${order.order_number} berhasil diterima oleh @${order.username}!`, 'info');
                    }
                    lastOrderNumber = order.order_number;
                }
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted fs-8">
                            Belum ada order hari ini.
                        </td>
                    </tr>
                `;
            }

            // 6. Update Log Feed
            const feedContainer = document.getElementById('activity-feed-container');
            if (data.activities && data.activities.length > 0) {
                let feedHtml = '';
                data.activities.forEach(activity => {
                    let dotClass = '';
                    if (activity.action === 'ACCEPT_ORDER') dotClass = 'action-accept';
                    else if (activity.action === 'VOID_ORDER') dotClass = 'action-void';
                    else if (activity.action === 'LOGIN') dotClass = 'action-login';
                    else if (activity.action === 'BREAK_START' || activity.action === 'BREAK_END') dotClass = 'action-break';

                    feedHtml += `
                        <div class="activity-feed-item">
                            <div class="activity-feed-dot ${dotClass}"></div>
                            <div class="w-100 mb-2">
                                <div class="d-flex align-items-center justify-content-between" style="justify-content: space-between; display: flex;">
                                    <span class="font-weight-bold text-dark fs-8">${activity.name ? `${activity.name} <span class="text-muted font-weight-normal" style="font-size: 10px; font-weight: 400;">(@${activity.user})</span>` : (activity.user ? `@${activity.user}` : 'System')}</span>
                                    <span class="text-muted font-monospace" style="font-size: 10px;">${activity.created_at.substring(11, 16)}</span>
                                </div>
                                <p class="mb-0 text-secondary fs-8 mt-0.5">${activity.description}</p>
                            </div>
                        </div>
                    `;
                });
                feedContainer.innerHTML = feedHtml;

                const isInitialActivityLoad = !hasLoadedActivities;
                data.activities.forEach(activity => {
                    if (isInitialActivityLoad) {
                        processedActivityIds.add(activity.id);
                        return;
                    }

                    if (!processedActivityIds.has(activity.id)) {
                        processedActivityIds.add(activity.id);
                        if (activity.user_id !== currentUserId && (activity.action === 'BREAK_START' || activity.action === 'BREAK_END')) {
                            const isReady = activity.action === 'BREAK_END';
                            speakCCBreak(activity.name || activity.user || 'Staf CC', isReady);
                            window.showToast(activity.description, isReady ? 'success' : 'warning');
                        }
                    }
                });
                hasLoadedActivities = true;
            } else {
                feedContainer.innerHTML = `<div class="text-center py-4 text-muted fs-8">Belum ada log.</div>`;
                hasLoadedActivities = true;
            }

            // 7. Update Chat Live Internal State
            if (data.chats) {
                const isInitialLoad = localChats.length === 0 && processedChatIds.size === 0;
                
                // Jika load pertama kali, daftarkan semua ID pesan yang ada agar tidak memicu notifikasi suara
                if (isInitialLoad) {
                    data.chats.forEach(chat => {
                        processedChatIds.add(chat.id);
                    });
                    localChats = data.chats;
                } else {
                    // Cari pesan baru
                    data.chats.forEach(chat => {
                        if (!processedChatIds.has(chat.id)) {
                            processedChatIds.add(chat.id);
                            localChats.push(chat);

                            // Jika pesan masuk (dikirim orang lain) dan belum dibaca
                            if (chat.sender_id !== currentUserId && !chat.is_read) {
                                // Bunyikan notifikasi suara dan teks
                                speakChatMessage(chat.sender_name, chat.message);

                                // Jika sedang membuka thread dengan pengirim tersebut, langsung tandai dibaca
                                if (currentActiveContactId === chat.sender_id && isChatBoxOpen) {
                                    markChatsAsRead(chat.sender_id);
                                } else {
                                    // Tampilkan toast kustom jika chat box sedang tertutup atau membuka chat lain
                                    window.showToast(`Pesan baru dari @${chat.sender_username}: ${chat.message.substring(0, 30)}...`, 'info');
                                }
                            }
                        } else {
                            // Update status is_read pesan yang sudah ada
                            const localChat = localChats.find(c => c.id === chat.id);
                            if (localChat) {
                                localChat.is_read = chat.is_read;
                            }
                        }
                    });
                }

                updateUnreadBadge();
                
                if (isChatBoxOpen) {
                    if (currentActiveContactId) {
                        renderChatThread();
                    } else {
                        renderUserList();
                    }
                }
            }

            if (data.chat_users) {
                localChatUsers = data.chat_users;
                if (isChatBoxOpen && !currentActiveContactId) {
                    renderUserList();
                }
            }

            // 5-minute warning: check if position-1 person is still waiting (audible for all CC/CEC users)
            if (isSoundEnabled() && data && Array.isArray(data.queue) && data.queue.length > 0) {
                const currentFirst = data.queue[0]; // first in the ready queue
                const now = Date.now();

                if (currentFirst.user_id !== firstPositionUserId) {
                    // Position 1 changed – reset the timer
                    firstPositionUserId = currentFirst.user_id;
                    firstPositionSince = now;
                    lastWarningAt = null;
                } else {
                    // Same person still at position 1 — check if 5 minutes have elapsed since last warning
                    const elapsedSinceFirst = now - firstPositionSince;
                    const elapsedSinceLastWarning = lastWarningAt ? (now - lastWarningAt) : elapsedSinceFirst;

                    if (elapsedSinceFirst >= WARNING_INTERVAL_MS && elapsedSinceLastWarning >= WARNING_INTERVAL_MS) {
                        lastWarningAt = now;
                        if ('speechSynthesis' in window) {
                            window.speechSynthesis.cancel();
                            const warningText = `Antrian selanjutnya ${currentFirst.name} harap bersiap menerima order ERA`;
                            const utterance = new SpeechSynthesisUtterance(warningText.toLowerCase());
                            utterance.lang = 'id-ID';
                            utterance.rate = 0.9;
                            if (!indonesianVoice) {
                                loadVoices();
                            }
                            if (indonesianVoice) {
                                utterance.voice = indonesianVoice;
                            }
                            window.speechSynthesis.speak(utterance);
                        }
                    }
                }
            }
        }, 3000);

        // Submit Form Terima Order (Khusus CC)
        if (userRole === 'CC') {
            const acceptForm = document.getElementById('accept-order-form');
            acceptForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const errorEl = document.getElementById('accept-order-error');
                if (!selectedTypeId) {
                    if (errorEl) {
                        errorEl.classList.remove('d-none');
                        setTimeout(() => errorEl.classList.add('d-none'), 5000);
                    }
                    window.showToast('Silakan pilih tipe order (CRM/CMS/OTHER) terlebih dahulu!', 'warning');
                    return;
                }
                if (errorEl) errorEl.classList.add('d-none');

                const btnAccept = document.getElementById('btn-accept-order');
                const originalText = btnAccept.innerHTML;
                btnAccept.setAttribute('disabled', 'true');
                btnAccept.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

                fetch('/orders/accept', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_type_id: selectedTypeId })
                })
                .then(res => {
                    if (!res.ok) return res.json().then(data => { throw new Error(data.message || 'Gagal menerima order') });
                    return res.json();
                })
                .then(data => {
                    window.showToast(`Order ${data.order.order_number} berhasil diterima!`, 'success');
                    // Voice announcement for the user who accepted the order
                    if (isSoundEnabled()) {
                        const orderType = data.order && data.order.order_type ? data.order.order_type.name : '';
                        if ('speechSynthesis' in window) {
                            window.speechSynthesis.cancel();
                            const msg = orderType
                                ? `Order ${orderType} berhasil diterima. Anda dipindahkan ke urutan terakhir.`
                                : 'Order berhasil diterima. Anda dipindahkan ke urutan terakhir.';
                            const utterance = new SpeechSynthesisUtterance(msg);
                            utterance.lang = 'id-ID';
                            utterance.rate = 0.9;
                            if (!indonesianVoice) {
                                loadVoices();
                            }
                            if (indonesianVoice) {
                                utterance.voice = indonesianVoice;
                            }
                            window.speechSynthesis.speak(utterance);
                        }
                    }
                    selectedTypeId = null;
                    document.getElementById('selected-order-type-id').value = '';
                    document.querySelectorAll('.custom-toggle-card').forEach(c => {
                        c.classList.remove('selected');
                        c.style.borderColor = 'var(--border)';
                        c.style.backgroundColor = 'var(--card)';
                        c.style.boxShadow = 'none';
                    });
                })
                .catch(err => {
                    window.showToast(err.message, 'error');
                })
                .finally(() => {
                    btnAccept.innerHTML = originalText;
                });
            });

            const btnToggleBreak = document.getElementById('btn-toggle-break');
            if (btnToggleBreak) {
                btnToggleBreak.addEventListener('click', function() {
                    const mode = this.dataset.mode === 'ready' ? 'ready' : 'break';
                    const endpoint = mode === 'ready' ? '/queue/ready' : '/queue/break';
                    const originalText = this.innerHTML;

                    this.setAttribute('disabled', 'true');
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(data => { throw new Error(data.message || 'Gagal mengubah status antrian') });
                        return res.json();
                    })
                    .then(() => {
                        window.showToast(mode === 'ready' ? 'Anda kembali ready di antrian.' : 'Anda masuk mode break/istirahat.', mode === 'ready' ? 'success' : 'warning');
                    })
                    .catch(err => {
                        window.showToast(err.message, 'error');
                    })
                    .finally(() => {
                        this.removeAttribute('disabled');
                        this.innerHTML = originalText;
                    });
                });
            }

            // Form Void Confirmation Logic
            const radioPreset = document.querySelectorAll('input[name="preset_reason"]');
            const customReasonContainer = document.getElementById('custom-reason-container');
            const btnConfirmVoid = document.getElementById('btn-confirm-void');

            function checkVoidFormValidity() {
                const selectedPreset = document.querySelector('input[name="preset_reason"]:checked');
                if (!selectedPreset) {
                    btnConfirmVoid.setAttribute('disabled', 'true');
                    return;
                }
                if (selectedPreset.value === 'OTHER') {
                    const customText = document.getElementById('custom_reason').value.trim();
                    if (customText.length === 0) {
                        btnConfirmVoid.setAttribute('disabled', 'true');
                    } else {
                        btnConfirmVoid.removeAttribute('disabled');
                    }
                } else {
                    btnConfirmVoid.removeAttribute('disabled');
                }
            }

            radioPreset.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'OTHER') {
                        customReasonContainer.classList.remove('d-none');
                        document.getElementById('custom_reason').setAttribute('required', 'true');
                    } else {
                        customReasonContainer.classList.add('d-none');
                        document.getElementById('custom_reason').removeAttribute('required');
                    }
                    checkVoidFormValidity();
                });
            });

            document.getElementById('custom_reason').addEventListener('input', checkVoidFormValidity);

            const voidForm = document.getElementById('void-order-form');
            voidForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const selectedPreset = document.querySelector('input[name="preset_reason"]:checked');
                let finalReason = '';
                if (selectedPreset.value === 'OTHER') {
                    finalReason = document.getElementById('custom_reason').value.trim();
                } else {
                    finalReason = selectedPreset.value;
                }

                const btnConfirm = document.getElementById('btn-confirm-void');
                const originalText = btnConfirm.innerHTML;
                btnConfirm.setAttribute('disabled', 'true');
                btnConfirm.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

                fetch('/orders/void', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reason: finalReason })
                })
                .then(res => {
                    if (!res.ok) return res.json().then(data => { throw new Error(data.message || 'Gagal membatalkan order') });
                    return res.json();
                })
                .then(data => {
                    window.showToast(`Order ${data.order.order_number} berhasil dibatalkan.`, 'success');
                    
                    // Close Bootstrap Modal
                    $('#voidConfirmModal').modal('hide');
                    
                    voidForm.reset();
                    customReasonContainer.classList.add('d-none');
                    btnConfirmVoid.setAttribute('disabled', 'true');
                })
                .catch(err => {
                    window.showToast(err.message, 'error');
                })
                .finally(() => {
                    btnConfirm.innerHTML = originalText;
                });
            });

            // Bind click event listener for accept titipan buttons
            $(document).on('click', '.accept-titipan-btn', function() {
                const id = $(this).data('id');
                const btn = $(this);
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

                $.ajax({
                    url: '/orders/titipan/accept',
                    method: 'POST',
                    data: JSON.stringify({ id: id }),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        window.showToast(`Titipan order berhasil diterima!`, 'success');
                        if (isSoundEnabled()) {
                            if ('speechSynthesis' in window) {
                                window.speechSynthesis.cancel();
                                const msg = `Titipan order telah diterima. Anda dipindahkan ke urutan terakhir.`;
                                const utterance = new SpeechSynthesisUtterance(msg);
                                utterance.lang = 'id-ID';
                                utterance.rate = 0.9;
                                if (indonesianVoice) {
                                    utterance.voice = indonesianVoice;
                                }
                                window.speechSynthesis.speak(utterance);
                            }
                        }
                        // Reset selected order card style
                        selectedTypeId = null;
                        document.getElementById('selected-order-type-id').value = '';
                        document.querySelectorAll('.custom-toggle-card').forEach(c => {
                            c.classList.remove('selected');
                            c.style.borderColor = 'var(--border)';
                            c.style.backgroundColor = 'var(--card)';
                            c.style.boxShadow = 'none';
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(originalHtml);
                        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menerima titipan order.';
                        window.showToast(msg, 'danger');
                    }
                });
            });
        }

        // ==========================================
        // LOGIKA CHAT LIVE INTERNAL
        // ==========================================
        let currentActiveContactId = null;
        let localChats = [];
        let localChatUsers = [];
        const processedChatIds = new Set();
        let isChatBoxOpen = false;

        const chatTriggerBtn = document.getElementById('chat-trigger-btn');
        const chatBoxWindow = document.getElementById('chat-box-window');
        const chatCloseBtn = document.getElementById('chat-close-btn');
        const chatBackBtn = document.getElementById('chat-back-btn');
        const chatTitle = document.getElementById('chat-title');
        const chatSubtitle = document.getElementById('chat-subtitle');
        const chatUserListPanel = document.getElementById('chat-user-list-panel');
        const chatThreadPanel = document.getElementById('chat-thread-panel');
        const chatFooterPanel = document.getElementById('chat-footer-panel');
        const chatMessagesContainer = document.getElementById('chat-messages-container');
        const chatSendForm = document.getElementById('chat-send-form');
        const chatMessageInput = document.getElementById('chat-message-input');
        const chatUnreadBadge = document.getElementById('chat-unread-badge');

        // Audio Alert Khusus Chat (Double-Beep)
        function playChatAlertChime() {
            return new Promise((resolve) => {
                if (!isSoundEnabled()) return resolve();
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return resolve();
                    const audioCtx = new AudioContext();
                    const now = audioCtx.currentTime;
                    
                    const osc1 = audioCtx.createOscillator();
                    const gain1 = audioCtx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(880.00, now);
                    gain1.gain.setValueAtTime(0.08, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.15);
                    osc1.connect(gain1);
                    gain1.connect(audioCtx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.15);
                    
                    setTimeout(() => {
                        try {
                            const osc2 = audioCtx.createOscillator();
                            const gain2 = audioCtx.createGain();
                            osc2.type = 'sine';
                            osc2.frequency.setValueAtTime(1046.50, audioCtx.currentTime);
                            gain2.gain.setValueAtTime(0.08, audioCtx.currentTime);
                            gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.2);
                            osc2.connect(gain2);
                            gain2.connect(audioCtx.destination);
                            osc2.start();
                            osc2.stop(audioCtx.currentTime + 0.2);
                            setTimeout(resolve, 200);
                        } catch (err) { resolve(); }
                    }, 100);
                } catch (e) {
                    resolve();
                }
            });
        }

        // Text-to-Speech Notifikasi Chat Bahasa Indonesia
        async function speakChatMessage(senderName, messageText) {
            if (!isSoundEnabled()) return;
            await playChatAlertChime();
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let speakSender = senderName.toLowerCase().replace(/1/g, ' satu')
                                            .replace(/2/g, ' dua')
                                            .replace(/3/g, ' tiga')
                                            .replace(/4/g, ' empat')
                                            .replace(/5/g, ' lima')
                                            .replace(/6/g, ' enam')
                                            .replace(/7/g, ' tujuh')
                                            .replace(/8/g, ' delapan')
                                            .replace(/9/g, ' sembilan')
                                            .replace(/0/g, ' nol');
                let truncatedMsg = messageText.substring(0, 100);
                const utterance = new SpeechSynthesisUtterance('Pesan dari ' + speakSender + ': ' + truncatedMsg);
                utterance.lang = 'id-ID';
                utterance.rate = 0.95;
                
                if (!indonesianVoice) {
                    loadVoices();
                }
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                }
                window.speechSynthesis.speak(utterance);
            }
        }

        // Menampilkan daftar rekan/kontak
        function renderUserList() {
            if (localChatUsers.length === 0) {
                chatUserListPanel.innerHTML = '<div class="text-muted text-center py-4 fs-8">Tidak ada rekan aktif saat ini.</div>';
                return;
            }

            // Sort: users with unread messages first, then online status, then by name
            localChatUsers.sort((a, b) => {
                const unreadA = localChats.filter(c => c.sender_id === a.id && !c.is_read).length;
                const unreadB = localChats.filter(c => c.sender_id === b.id && !c.is_read).length;
                
                if (unreadA > 0 && unreadB === 0) return -1;
                if (unreadA === 0 && unreadB > 0) return 1;
                if (unreadA > 0 && unreadB > 0) {
                    return unreadB - unreadA; // more unread messages first
                }
                
                // If both are read, sort by online status
                if (a.is_online && !b.is_online) return -1;
                if (!a.is_online && b.is_online) return 1;
                
                // Then sort alphabetically
                return a.name.localeCompare(b.name);
            });

            let html = '';
            localChatUsers.forEach(user => {
                const unreadCount = localChats.filter(c => c.sender_id === user.id && !c.is_read).length;
                const badgeHtml = unreadCount > 0 ? `<span class="badge badge-danger ml-auto font-weight-bold" style="border-radius: 50%; padding: 4px 7px; font-size: 10px;">${unreadCount}</span>` : '';
                const statusClass = user.is_online ? 'online' : 'offline';
                const statusTitle = user.is_online ? 'Online' : 'Offline';
                const initials = user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

                html += `
                    <div class="chat-user-item border-bottom pb-2" data-id="${user.id}" data-name="${user.name}" data-role="${user.role}">
                        <div class="chat-user-avatar text-white">
                            ${initials}
                            <span class="chat-user-status-dot ${statusClass}" title="${statusTitle}"></span>
                        </div>
                        <div class="ml-3" style="line-height: 1.2;">
                            <div class="font-weight-bold text-dark fs-8">${user.name}</div>
                            <small class="text-muted" style="font-size: 10px;">@${user.username} • ${user.role}</small>
                        </div>
                        ${badgeHtml}
                    </div>
                `;
            });
            chatUserListPanel.innerHTML = html;

            // Bind klik ke item kontak
            document.querySelectorAll('.chat-user-item').forEach(item => {
                item.addEventListener('click', function() {
                    const contactId = parseInt(this.getAttribute('data-id'));
                    const contactName = this.getAttribute('data-name');
                    const contactRole = this.getAttribute('data-role');

                    openChatThread(contactId, contactName, contactRole);
                });
            });
        }

        // Membuka thread chat dengan kontak tertentu
        function openChatThread(contactId, contactName, contactRole) {
            currentActiveContactId = contactId;
            chatTitle.innerText = contactName;
            chatSubtitle.innerText = contactRole;
            chatBackBtn.classList.remove('d-none');
            
            chatUserListPanel.classList.add('d-none');
            chatThreadPanel.style.display = 'flex';
            chatThreadPanel.classList.remove('d-none');
            chatFooterPanel.style.display = 'flex';
            chatFooterPanel.classList.remove('d-none');

            // Tandai langsung sebagai dibaca
            markChatsAsRead(contactId);

            // Render pesan
            renderChatThread();

            // Auto-focus input
            setTimeout(() => chatMessageInput.focus(), 100);
        }

        // Menutup thread chat dan kembali ke daftar user
        function closeChatThread() {
            currentActiveContactId = null;
            chatTitle.innerText = 'Pesan Internal CC';
            chatSubtitle.innerText = 'Pilih rekan untuk mulai chat';
            chatBackBtn.classList.add('d-none');

            chatThreadPanel.classList.add('d-none');
            chatThreadPanel.style.display = 'none';
            chatFooterPanel.classList.add('d-none');
            chatFooterPanel.style.display = 'none';
            chatUserListPanel.classList.remove('d-none');

            renderUserList();
        }

        // Render gelembung pesan di thread aktif
        function renderChatThread() {
            if (!currentActiveContactId) return;

            const filteredChats = localChats.filter(c => 
                (c.sender_id === currentUserId && c.receiver_id === currentActiveContactId) ||
                (c.sender_id === currentActiveContactId && c.receiver_id === currentUserId)
            );

            if (filteredChats.length === 0) {
                chatMessagesContainer.innerHTML = `
                    <div class="text-center text-muted my-auto py-5" style="font-size: 11px;">
                        <i class="fas fa-comments fs-3 mb-2 text-gray-300"></i>
                        <p class="mb-0">Belum ada pesan hari ini.</p>
                        <p class="text-secondary" style="font-size: 9px;">Kirim pesan di bawah untuk memulai percakapan.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            filteredChats.forEach(chat => {
                const isSent = chat.sender_id === currentUserId;
                const bubbleClass = isSent ? 'sent' : 'received';
                
                html += `
                    <div class="chat-message-bubble-wrapper">
                        <div class="chat-message-bubble ${bubbleClass}">
                            <div>${escapeHtml(chat.message)}</div>
                            <span class="chat-message-time">${chat.time}</span>
                        </div>
                    </div>
                `;
            });

            chatMessagesContainer.innerHTML = html;
            
            // Scroll ke bawah
            setTimeout(() => {
                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
            }, 50);
        }

        // Tandai pesan dari pengirim sebagai telah dibaca
        function markChatsAsRead(senderId) {
            const hasUnread = localChats.some(c => c.sender_id === senderId && !c.is_read);
            if (!hasUnread) return;

            // Update secara lokal dulu agar UI responsif
            localChats.forEach(c => {
                if (c.sender_id === senderId && c.receiver_id === currentUserId) {
                    c.is_read = true;
                }
            });

            updateUnreadBadge();

            fetch('/chats/read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ sender_id: senderId })
            })
            .catch(err => console.error('Gagal menandai pesan dibaca:', err));
        }

        // Update badge unread total di tombol utama
        function updateUnreadBadge() {
            const totalUnread = localChats.filter(c => c.sender_id !== currentUserId && !c.is_read).length;
            if (totalUnread > 0) {
                chatUnreadBadge.innerText = totalUnread;
                chatUnreadBadge.classList.remove('d-none');
            } else {
                chatUnreadBadge.classList.add('d-none');
            }
        }

        // Escape HTML untuk mencegah XSS
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Toggles tampilan pop-up chat box
        chatTriggerBtn.addEventListener('click', function() {
            isChatBoxOpen = !isChatBoxOpen;
            if (isChatBoxOpen) {
                chatBoxWindow.classList.remove('d-none');
                chatTriggerBtn.style.transform = 'scale(0.9)';
                if (currentActiveContactId) {
                    openChatThread(currentActiveContactId, chatTitle.innerText, chatSubtitle.innerText);
                } else {
                    closeChatThread();
                }
            } else {
                chatBoxWindow.classList.add('d-none');
                chatTriggerBtn.style.transform = 'none';
            }
        });

        chatCloseBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            isChatBoxOpen = false;
            chatBoxWindow.classList.add('d-none');
            chatTriggerBtn.style.transform = 'none';
        });

        chatBackBtn.addEventListener('click', function() {
            closeChatThread();
        });

        // Submit form kirim pesan
        chatSendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageText = chatMessageInput.value.trim();
            if (!messageText || !currentActiveContactId) return;

            // Clear input segera agar UI terasa cepat
            chatMessageInput.value = '';

            const receiverId = currentActiveContactId;

            fetch('/chats/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    message: messageText
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengirim pesan');
                return res.json();
            })
            .then(data => {
                const formattedChat = {
                    id: data.chat.id,
                    sender_id: data.chat.sender_id,
                    sender_username: data.chat.sender.username,
                    sender_name: data.chat.sender.name,
                    receiver_id: data.chat.receiver_id,
                    message: data.chat.message,
                    is_read: data.chat.is_read,
                    time: new Date(data.chat.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                };

                // Daftarkan di processed agar tidak berbunyi notifikasi untuk diri sendiri
                processedChatIds.add(formattedChat.id);
                localChats.push(formattedChat);

                if (currentActiveContactId === receiverId) {
                    renderChatThread();
                }
            })
            .catch(err => {
                window.showToast(err.message, 'error');
            });
        });
    });
</script>

<style>
    /* Styling form-check-custom untuk modal void */
    .form-check-custom {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .form-check-custom:hover {
        border-color: var(--primary);
        background-color: var(--primary-light);
    }
    .form-check-input-custom {
        width: 16px;
        height: 16px;
        accent-color: var(--primary);
        cursor: pointer;
    }
    .form-check-label-custom {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-primary);
        cursor: pointer;
        flex: 1;
        margin-bottom: 0;
    }
    .bg-purple {
        background-color: #8b5cf6 !important;
    }
    .text-purple {
        color: #8b5cf6 !important;
    }

    /* Floating Chat styles */
    .chat-trigger-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4) !important;
    }
    .chat-trigger-btn:active {
        transform: scale(0.95);
    }
    .chat-user-item {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        margin-bottom: 6px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }
    .chat-user-item:hover {
        background-color: #f1f5f9;
        transform: translateY(-1px);
    }
    .chat-user-item:active {
        background-color: #e2e8f0;
    }
    .chat-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #4f46e5;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        position: relative;
    }
    .chat-user-status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid white;
    }
    .chat-user-status-dot.online {
        background-color: #10b981;
    }
    .chat-user-status-dot.offline {
        background-color: #94a3b8;
    }
    .chat-message-bubble-wrapper {
        display: flex;
        flex-direction: column;
        width: 100%;
        margin-bottom: 4px;
    }
    .chat-message-bubble {
        max-width: 80%;
        padding: 8px 12px;
        font-size: 13px;
        line-height: 1.4;
    }
    .chat-message-bubble.sent {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: white;
        align-self: flex-end;
        border-radius: 12px 12px 0px 12px;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.15);
    }
    .chat-message-bubble.received {
        background-color: #e2e8f0;
        color: #1e293b;
        align-self: flex-start;
        border-radius: 12px 12px 12px 0px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .chat-message-time {
        font-size: 9px;
        margin-top: 2px;
        opacity: 0.65;
        font-family: monospace;
        display: block;
    }
    .chat-message-bubble.sent .chat-message-time {
        color: rgba(255, 255, 255, 0.8);
        text-align: right;
    }
    .chat-message-bubble.received .chat-message-time {
        color: #64748b;
        text-align: left;
    }
</style>
@endpush
