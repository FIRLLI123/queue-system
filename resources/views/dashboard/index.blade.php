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

<!-- SECTION 4: Statistik Hari Ini -->
<div class="row mb-4">
    <!-- Stat CRM -->
    <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">CRM Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 font-monospace num-counter" id="stat-crm-val">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat CMS -->
    <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">CMS Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 font-monospace num-counter" id="stat-cms-val">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-headset fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat OTHER -->
    <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
        <div class="card border-left-purple shadow h-100 py-2" style="border-left-color: #8b5cf6 !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-purple text-uppercase mb-1" style="color: #8b5cf6;">OTHER Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 font-monospace num-counter" id="stat-other-val">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-circle-nodes fa-2x text-gray-300"></i>
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
    
    <!-- KOLOM KIRI (col-lg-4): Daftar Antrian CC (TV monitoring mode) -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow mb-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users-line mr-2"></i>Daftar Antrian CC</h6>
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
        
        <!-- Global status chip di bawah antrian -->
        <div class="alert alert-warning border-0 shadow-sm rounded p-3 mb-0 d-none" id="global-waiting-chip" style="background-color: var(--warning-light);">
            <div class="d-flex align-items-center fs-8 fw-semibold text-warning" style="gap: 8px;">
                <i class="fas fa-hourglass-start fa-spin"></i>
                <span id="global-waiting-text">Menunggu antrian CC...</span>
            </div>
        </div>
    </div>
    
    <!-- KOLOM KANAN (col-lg-8): Form Aksi CC atau Monitor & Log -->
    <div class="col-xl-8 col-lg-7">
        
        <div class="row">
            <!-- 1. Form Terima Order (Role-Based) -->
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i>Penerimaan Order Baru</h6>
                        
                        <!-- Mini status position for CC -->
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
            <!-- 3. Giliran Sekarang (Next CC Detail) -->
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-play mr-2"></i>Staf CC Terdepan (Next)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-auto mb-3 mb-md-0 text-center">
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center shadow-sm" id="current-turn-avatar" style="width: 70px; height: 70px; font-size: 24px; font-weight: 700;">
                                    --
                                </div>
                            </div>
                            <div class="col text-center text-md-start">
                                <h5 class="font-weight-bold text-dark mb-1" id="current-turn-name">Memuat Data...</h5>
                                <div class="d-flex align-items-center justify-content-center justify-content-md-start fs-8 text-secondary" style="gap: 8px;">
                                    <span id="current-turn-status-dot" class="offline-indicator-dot"></span>
                                    <span id="current-turn-username" class="font-weight-bold">@username</span>
                                    <span class="text-gray-300">|</span>
                                    <span>Waktu Terakhir: <span id="current-turn-time" class="font-monospace">--:--:--</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Tabel Order Terakhir -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-list mr-2"></i>Order Terakhir</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 fs-8">
                                <thead class="bg-light text-secondary text-uppercase" style="font-size: 10px;">
                                    <tr>
                                        <th class="pl-4">Order</th>
                                        <th>CC</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="orders-table-body">
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
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

        // Penanganan Voice List Lintas Browser (Safari/Firefox/Chrome Async Loader)
        let indonesianVoice = null;
        function loadVoices() {
            if ('speechSynthesis' in window) {
                const voices = window.speechSynthesis.getVoices();
                indonesianVoice = voices.find(v => v.lang === 'id-ID' || v.lang === 'id_ID' || v.lang.toLowerCase().startsWith('id') || v.lang.toLowerCase().includes('indonesia'));
            }
        }
        
        loadVoices();
        if ('speechSynthesis' in window && window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = loadVoices;
        }

        // Web Audio API Synthesized Chime (Ting-Tung)
        function playChime() {
            return new Promise((resolve) => {
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
            await playChime();
            
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                
                let speakName = name;
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
                
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                } else {
                    const voices = window.speechSynthesis.getVoices();
                    const idVoice = voices.find(v => v.lang === 'id-ID' || v.lang === 'id_ID' || v.lang.toLowerCase().startsWith('id') || v.lang.toLowerCase().includes('indonesia'));
                    if (idVoice) utterance.voice = idVoice;
                }
                
                window.speechSynthesis.speak(utterance);
            }
        }

        // Web Audio API untuk Login Chime (Arpeggio Naik)
        function playLoginChime() {
            return new Promise((resolve) => {
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
            await playLoginChime();
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let speakName = name.replace(/1/g, ' satu').replace(/2/g, ' dua').replace(/3/g, ' tiga').replace(/4/g, ' empat').replace(/5/g, ' lima');
                const utterance = new SpeechSynthesisUtterance(speakName + ' telah online');
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                } else {
                    const voices = window.speechSynthesis.getVoices();
                    const idVoice = voices.find(v => v.lang === 'id-ID' || v.lang === 'id_ID' || v.lang.toLowerCase().startsWith('id') || v.lang.toLowerCase().includes('indonesia'));
                    if (idVoice) utterance.voice = idVoice;
                }
                
                window.speechSynthesis.speak(utterance);
            }
        }

        // Suara Panggilan Logout Staf CC
        async function speakCCLogout(name) {
            await playLogoutChime();
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let speakName = name.replace(/1/g, ' satu').replace(/2/g, ' dua').replace(/3/g, ' tiga').replace(/4/g, ' empat').replace(/5/g, ' lima');
                const utterance = new SpeechSynthesisUtterance(speakName + ' telah offline');
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                
                if (indonesianVoice) {
                    utterance.voice = indonesianVoice;
                } else {
                    const voices = window.speechSynthesis.getVoices();
                    const idVoice = voices.find(v => v.lang === 'id-ID' || v.lang === 'id_ID' || v.lang.toLowerCase().startsWith('id') || v.lang.toLowerCase().includes('indonesia'));
                    if (idVoice) utterance.voice = idVoice;
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
                        data.order_types.forEach(type => {
                            let emoji = '💬';
                            let desc = 'Pesanan CRM';
                            if (type.name === 'CMS') {
                                emoji = '📞';
                                desc = 'Panggilan Masuk';
                            } else if (type.name === 'OTHER') {
                                emoji = '⚙️';
                                desc = 'Lain-lain';
                            }

                            // Jika admin, card tipe order otomatis dinonaktifkan
                            const disabledClass = userRole === 'ADMIN' ? 'disabled' : '';

                            html += `
                                <div class="col-4">
                                    <div class="custom-toggle-card p-3 text-center border rounded h-100 ${disabledClass}" 
                                         data-id="${type.id}" 
                                         style="transition: all 0.2s ease; border-color: var(--border); background-color: var(--card);">
                                        <div class="mb-2" style="font-size: 24px;">${emoji}</div>
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
            // 1. Render Antrian
            window.renderQueueList('queue-list-container', data.queue);
            document.getElementById('queue-count').innerText = `${data.queue.length} Staf`;

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
                    statusDot.setAttribute('title', 'CC Online');
                    globalWaitingChip.classList.remove('d-none');
                    globalWaitingText.innerText = `Menunggu ${current.name} menerima order`;
                } else {
                    statusDot.className = 'offline-indicator-dot mr-2';
                    statusDot.setAttribute('title', 'CC Belum Login (Offline)');
                    globalWaitingChip.classList.remove('d-none');
                    globalWaitingText.innerText = `Menunggu ${current.name} login untuk menerima order`;
                }

                // Mainkan suara panggilan jika CC terdepan bergeser/berubah
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
                const btnAccept = document.getElementById('btn-accept-order');
                const positionBadgeTop = document.getElementById('cc-status-text-top');
                const sidebarBadge = document.getElementById('cc-sidebar-position-badge');

                if (myQueueItem) {
                    const myPos = data.queue.indexOf(myQueueItem) + 1;
                    if (sidebarBadge) {
                        sidebarBadge.innerText = myPos === 1 ? 'GILIRAN ANDA' : `ANTRIAN #${myPos}`;
                        sidebarBadge.className = myPos === 1 ? 'badge badge-primary' : 'badge badge-secondary';
                    }
                    if (positionBadgeTop) {
                        positionBadgeTop.innerHTML = `<i class="fas fa-headset mr-1"></i> Posisi Anda: Antrian #${myPos} (${myPos === 1 ? 'GILIRAN ANDA' : 'Mengantri'})`;
                    }

                    if (myPos === 1) {
                        btnAccept.removeAttribute('disabled');
                        btnAccept.className = 'btn btn-primary w-100 py-3 font-weight-bold shadow-sm';
                        btnAccept.innerText = 'Terima Order';

                        if (!isFirstTurnNotification) {
                            window.showToast('Giliran Anda sekarang! Silakan pilih tipe order dan terima.', 'primary');
                            isFirstTurnNotification = true;
                        }
                    } else {
                        btnAccept.setAttribute('disabled', 'true');
                        btnAccept.className = 'btn btn-secondary w-100 py-3 font-weight-bold shadow-sm';
                        btnAccept.innerText = 'Belum Giliran Anda';
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

            // 5. Update Tabel Order Terakhir
            const tableBody = document.getElementById('orders-table-body');
            if (data.last_order) {
                const order = data.last_order;
                const badgeClass = order.status === 'COMPLETED' ? 'badge-success-custom' : 'badge-danger-custom';
                const typeBadgeClass = order.type === 'CRM' ? 'bg-success text-white' : (order.type === 'CMS' ? 'bg-primary text-white' : 'bg-purple text-white');
                
                tableBody.innerHTML = `
                    <tr class="fade-slide-up">
                        <td class="font-monospace font-weight-bold text-dark pl-4">${order.order_number}</td>
                        <td><span class="font-weight-bold text-gray-700">@${order.username}</span></td>
                        <td><span class="badge px-2.5 py-1 ${typeBadgeClass}" style="font-size: 10px;">${order.type}</span></td>
                        <td><span class="badge-pill-custom ${badgeClass}">${order.status}</span></td>
                    </tr>
                `;

                if (lastOrderNumber && lastOrderNumber !== order.order_number && order.status === 'COMPLETED') {
                    window.showToast(`Order ${order.order_number} berhasil diterima oleh @${order.username}!`, 'info');
                }
                lastOrderNumber = order.order_number;
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted fs-8">
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

                    feedHtml += `
                        <div class="activity-feed-item">
                            <div class="activity-feed-dot ${dotClass}"></div>
                            <div class="w-100 mb-2">
                                <div class="d-flex align-items-center justify-content-between" style="justify-content: space-between; display: flex;">
                                    <span class="font-weight-bold text-dark fs-8">@${activity.user || 'system'}</span>
                                    <span class="text-muted font-monospace" style="font-size: 10px;">${activity.created_at.substring(11, 16)}</span>
                                </div>
                                <p class="mb-0 text-secondary fs-8 mt-0.5">${activity.description}</p>
                            </div>
                        </div>
                    `;
                });
                feedContainer.innerHTML = feedHtml;
            } else {
                feedContainer.innerHTML = `<div class="text-center py-4 text-muted fs-8">Belum ada log.</div>`;
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
        }
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
</style>
@endpush
