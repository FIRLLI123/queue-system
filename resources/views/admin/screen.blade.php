@extends('layouts.app')

@section('title', 'Screen Monitoring Antrian')

@push('styles')
<style>
/* Theme-driven CSS variables */
.screen-monitor-page.theme-dark {
    --bg-page: #07090e; /* Sangat gelap (hampir hitam) agar kartu kontras tinggi */
    --card-bg: #1e293b; /* Slate 800 - Lighter navy-gray agar kartu menonjol dan kontras */
    --card-border: rgba(255, 255, 255, 0.16); /* Border kartu lebih jelas */
    --text-main: #ffffff; /* Putih bersih untuk keterbacaan maksimum */
    --text-sub: #cbd5e1; /* Slate 300 - Abu-abu terang agar teks sekunder terlihat jelas */
    --border-glass: rgba(255, 255, 255, 0.2);
    --bg-glass: #1e293b;
    --shadow-card: 0 15px 35px -10px rgba(0, 0, 0, 0.7);
    --divider-color: rgba(255, 255, 255, 0.15);
    --stat-bg: rgba(255, 255, 255, 0.05);
    --stat-border: rgba(255, 255, 255, 0.1);
}

.screen-monitor-page.theme-light {
    --bg-page: #f1f5f9; /* Slate 100 */
    --card-bg: #ffffff; /* Solid white card for high contrast */
    --card-border: #cbd5e1;
    --text-main: #0f172a; /* Slate 900 */
    --text-sub: #475569; /* Slate 600 */
    --border-glass: #cbd5e1;
    --bg-glass: rgba(255, 255, 255, 0.9);
    --shadow-card: 0 10px 25px -8px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
    --divider-color: rgba(0, 0, 0, 0.08);
    --stat-bg: rgba(0, 0, 0, 0.02);
    --stat-border: rgba(0, 0, 0, 0.04);
}

/* Base style overrides */
.screen-monitor-page {
    background-color: var(--bg-page);
    color: var(--text-main);
    padding: 30px;
    border-radius: 16px;
    transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.15);
    border: 1px solid var(--card-border);
}

.bg-dark-glass {
    background: var(--bg-glass) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: var(--text-main);
    border: 1px solid var(--border-glass);
}

.border-glass {
    border: 1px solid var(--border-glass);
}

/* Responsive Grid */
.monitor-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-top: 10px;
}

@media (max-width: 1200px) {
    .monitor-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .monitor-grid {
        grid-template-columns: 1fr;
    }
}

/* Premium CC Card */
.monitor-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 20px;
    padding: 24px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 180px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, transform 0.3s ease;
}

.monitor-card:hover {
    transform: translateY(-2px);
    border-color: rgba(37, 99, 235, 0.5);
    box-shadow: 0 15px 35px -8px rgba(0, 0, 0, 0.6);
}

/* Glowing active card (NEXT) */
.monitor-card.active-turn {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.12) 0%, var(--card-bg) 100%);
    border-color: #3b82f6;
    box-shadow: 0 0 25px rgba(37, 99, 235, 0.25), var(--shadow-card);
}

.monitor-card.active-turn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 6px;
    height: 100%;
    background: linear-gradient(180deg, #3b82f6, #1d4ed8);
}

/* Offline state */
.monitor-card.offline-card {
    opacity: 0.95;
    border-color: var(--card-border);
}

.monitor-card.break-card {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.14) 0%, var(--card-bg) 100%);
    border-color: #f59e0b;
}

.monitor-card.break-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 6px;
    height: 100%;
    background: linear-gradient(180deg, #f59e0b, #c2410c);
}

/* Queue Number Bubble */
.queue-number-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border-radius: 14px;
    font-size: 22px;
    font-weight: 800;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid var(--card-border);
    color: var(--text-main);
    transition: all 0.3s ease;
}

.active-turn .queue-number-badge {
    background: rgba(37, 99, 235, 0.22);
    border-color: rgba(37, 99, 235, 0.5);
    color: #60a5fa;
    box-shadow: 0 0 12px rgba(37, 99, 235, 0.3);
}

/* Status Indicator */
.status-pill {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 99px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-pill.online {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
}

.status-pill.offline {
    background: rgba(148, 163, 184, 0.15);
    color: #94a3b8;
}

.status-pill.break {
    background: rgba(245, 158, 11, 0.16);
    color: #f59e0b;
}

.theme-dark .status-pill.offline {
    background: rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
}

/* Card Divider */
.card-divider {
    height: 1px;
    background: var(--divider-color);
    margin: 16px 0;
}

/* Statistics row */
.stats-row {
    display: flex;
    justify-content: space-between;
    gap: 8px;
}

.stat-pill {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    background: var(--stat-bg);
    border: 1px solid var(--stat-border);
    transition: all 0.2s ease;
}

.stat-pill.cms {
    color: #047857;
}

.theme-dark .stat-pill.cms {
    background-color: rgba(16, 185, 129, 0.2) !important;
    border-color: rgba(16, 185, 129, 0.45) !important;
    color: #6ee7b7 !important;
}

.stat-pill.crm {
    color: #1e4ed8;
}

.theme-dark .stat-pill.crm {
    background-color: rgba(59, 130, 246, 0.2) !important;
    border-color: rgba(59, 130, 246, 0.45) !important;
    color: #93c5fd !important;
}

.stat-pill.other {
    color: #b45309;
}

.theme-dark .stat-pill.other {
    background-color: rgba(245, 158, 11, 0.2) !important;
    border-color: rgba(245, 158, 11, 0.45) !important;
    color: #fcd34d !important;
}

.theme-dark .stat-pill strong {
    color: #ffffff !important;
}

/* Dynamic buttons style */
.screen-btn {
    background: var(--bg-glass);
    color: var(--text-main);
    border: 1px solid var(--border-glass);
    transition: all 0.2s ease;
}

.screen-btn:hover {
    background: var(--text-main);
    color: var(--bg-page);
    border-color: var(--text-main);
}

.screen-filter-group {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px;
    border-radius: 999px;
    background: var(--bg-glass);
    border: 1px solid var(--border-glass);
}

.screen-filter-btn {
    border: none;
    border-radius: 999px;
    padding: 7px 12px;
    background: transparent;
    color: var(--text-sub);
    font-size: 12px;
    font-weight: 800;
    line-height: 1;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.screen-filter-btn:hover,
.screen-filter-btn.active {
    background: var(--text-main);
    color: var(--bg-page);
}

/* Fullscreen Mode Overrides */
body.screen-fullscreen-mode {
    overflow: hidden;
}

body.screen-fullscreen-mode #accordionSidebar {
    display: none !important;
}

body.screen-fullscreen-mode #content-wrapper {
    margin: 0 !important;
    padding: 0 !important;
    background-color: var(--bg-page) !important;
    min-height: 100vh;
}

body.screen-fullscreen-mode .topbar {
    display: none !important;
}

body.screen-fullscreen-mode .sticky-footer {
    display: none !important;
}

body.screen-fullscreen-mode #content {
    background-color: var(--bg-page) !important;
    padding: 0 !important;
}

body.screen-fullscreen-mode .container-fluid {
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

body.screen-fullscreen-mode .screen-monitor-page {
    border-radius: 0;
    border: none;
    min-height: 100vh;
    padding: 30px;
}

/* Custom connection status dot pulse */
.pulse-green {
    box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7);
    animation: pulse-dot-green 2s infinite;
}

@keyframes pulse-dot-green {
    0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
    70% { box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
    100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
}

/* Styling for Titipan Order Notification on Screen */
.titipan-warning-card {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(30, 41, 59, 0.8) 100%);
    border: 2px solid #eab308;
    border-radius: 16px;
    padding: 20px;
    color: var(--text-main);
    box-shadow: 0 0 15px rgba(245, 158, 11, 0.2);
    position: relative;
    overflow: hidden;
}

.theme-light .titipan-warning-card {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(255, 255, 255, 0.95) 100%);
    border: 2px solid #eab308;
    color: #0f172a;
}

.titipan-warning-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: #eab308;
}

.animate-pulse-yellow {
    animation: pulse-yellow 2s infinite;
}

@keyframes pulse-yellow {
    0% { box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(234, 179, 8, 0); }
    100% { box-shadow: 0 0 0 0 rgba(234, 179, 8, 0); }
}

.bg-light.text-warning {
    background-color: rgba(245, 158, 11, 0.12) !important;
}
</style>
@endpush

@section('content')
<div class="screen-monitor-page theme-dark">
    <!-- Header Row -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 pb-3 border-bottom" style="border-bottom-color: var(--divider-color) !important;">
        <div class="mb-3 mb-sm-0">
            <h1 class="h3 font-weight-bold mb-0 d-flex align-items-center gap-2" style="color: var(--text-main) !important;">
                <i class="fas fa-desktop text-primary mr-2"></i> Screen Monitoring Antrian
            </h1>
            <p class="mb-0 fs-7" style="color: var(--text-sub) !important;">Realtime TV monitoring status & antrian Customer Care hari ini.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3" style="gap: 15px;">
            <!-- Connection Indicator -->
            <div class="d-flex align-items-center bg-dark-glass py-2 px-3 rounded-pill fs-8 border-glass" id="connection-indicator-panel">
                <span id="screen-status-dot" class="online-indicator-dot pulse-green mr-2"></span>
                <span id="screen-status-text" class="fw-semibold">Connected</span>
            </div>
            <!-- Clock -->
            <div id="digital-clock" class="bg-dark-glass py-2 px-3 rounded-pill fs-8 font-monospace border-glass font-weight-bold" style="letter-spacing: 0.05em;">
                00:00:00
            </div>
            <!-- Status Filter -->
            <div class="screen-filter-group" id="screen-filter-group" aria-label="Filter status CC">
                <button type="button" class="screen-filter-btn active" data-filter="all">Semua</button>
                <button type="button" class="screen-filter-btn" data-filter="online">Online</button>
                <button type="button" class="screen-filter-btn" data-filter="offline">Offline</button>
                <button type="button" class="screen-filter-btn" data-filter="break">Break</button>
            </div>
            <!-- Theme Toggle Button -->
            <button id="toggle-theme-btn" class="btn screen-btn rounded-pill btn-sm px-3 py-2 fs-8 font-weight-bold">
                <i class="fas fa-moon mr-1"></i> Dark Mode
            </button>
            <!-- Sound Toggle Button -->
            <button id="toggle-sound-btn" class="btn screen-btn rounded-pill btn-sm px-3 py-2 fs-8 font-weight-bold">
                <i class="fas fa-volume-up mr-1"></i> Suara Aktif
            </button>
            <!-- Fullscreen Button -->
            <button id="toggle-fullscreen-btn" class="btn screen-btn rounded-pill btn-sm px-3 py-2 fs-8 font-weight-bold">
                <i class="fas fa-expand mr-1"></i> Fullscreen Mode
            </button>
        </div>
    </div>

    <!-- Main Grid Container -->
    <div class="monitor-grid" id="monitor-grid-container">
        <!-- Will be dynamically populated via JS -->
    </div>

    <!-- Container untuk Titipan Order -->
    <div id="titipan-panel-container" class="mt-5 d-none">
        <hr style="border-color: var(--divider-color) !important;">
        <h4 class="font-weight-bold mb-3 d-flex align-items-center text-warning" style="font-size: 18px;">
            <i class="fas fa-exclamation-circle mr-2"></i> Terdapat Booking Titipan Order
        </h4>
        <div class="row" id="titipan-list-grid">
            <!-- Will be dynamically populated via JS -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const gridContainer = document.getElementById('monitor-grid-container');
    const statusDot = document.getElementById('screen-status-dot');
    const statusText = document.getElementById('screen-status-text');
    let pollingInterval = null;
    let previousQueue = null;
    let previousAllCc = null;
    let isSoundEnabled = localStorage.getItem('screen-monitor-sound') !== 'muted';
    let currentFilter = localStorage.getItem('screen-monitor-filter') || 'all';
    if (!['all', 'online', 'offline', 'break'].includes(currentFilter)) {
        currentFilter = 'all';
    }
    let latestCcList = [];
    let latestFilteredList = [];

    // State for 5-minute position-1 warning
    let firstPositionUserId = null;
    let firstPositionSince = null;  // timestamp (ms) when this person became position 1
    let lastWarningAt = null;       // timestamp (ms) of the last warning announcement
    const WARNING_INTERVAL_MS = 5 * 60 * 1000; // 5 minutes
    let lastTitipanWarningAt = {};

    // 1. Digital Clock System
    function updateClock() {
        const clockEl = document.getElementById('digital-clock');
        if (!clockEl) return;
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        clockEl.textContent = `${hours}:${minutes}:${seconds}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Fullscreen Mode Toggle
    const toggleBtn = document.getElementById('toggle-fullscreen-btn');
    toggleBtn.addEventListener('click', function() {
        const body = document.body;
        body.classList.toggle('screen-fullscreen-mode');
        
        if (body.classList.contains('screen-fullscreen-mode')) {
            toggleBtn.innerHTML = '<i class="fas fa-compress mr-1"></i> Exit Fullscreen';
        } else {
            toggleBtn.innerHTML = '<i class="fas fa-expand mr-1"></i> Fullscreen Mode';
        }
    });

    // 3. Theme Toggle System
    const themeBtn = document.getElementById('toggle-theme-btn');
    const screenPage = document.querySelector('.screen-monitor-page');
    
    // Load preference from localStorage (default: dark)
    let currentTheme = localStorage.getItem('screen-monitor-theme') || 'dark';
    setTheme(currentTheme);

    function setTheme(theme) {
        if (theme === 'light') {
            screenPage.classList.remove('theme-dark');
            screenPage.classList.add('theme-light');
            themeBtn.innerHTML = '<i class="fas fa-sun mr-1"></i> Light Mode';
            localStorage.setItem('screen-monitor-theme', 'light');
            currentTheme = 'light';
        } else {
            screenPage.classList.remove('theme-light');
            screenPage.classList.add('theme-dark');
            themeBtn.innerHTML = '<i class="fas fa-moon mr-1"></i> Dark Mode';
            localStorage.setItem('screen-monitor-theme', 'dark');
            currentTheme = 'dark';
        }
    }

    themeBtn.addEventListener('click', function() {
        if (currentTheme === 'dark') {
            setTheme('light');
        } else {
            setTheme('dark');
        }
    });

    // 3.5. Sound Announcement System (Speech Synthesis)
    const soundBtn = document.getElementById('toggle-sound-btn');
    
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
            // Convert to lowercase to prevent speech engines from spelling out uppercase names letter-by-letter
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

    function updateSoundButton() {
        if (isSoundEnabled) {
            soundBtn.innerHTML = '<i class="fas fa-volume-up mr-1"></i> Suara Aktif';
            soundBtn.classList.remove('btn-outline-danger');
        } else {
            soundBtn.innerHTML = '<i class="fas fa-volume-mute mr-1"></i> Suara Senyap';
            soundBtn.classList.add('btn-outline-danger');
        }
    }
    
    updateSoundButton();

    soundBtn.addEventListener('click', function() {
        isSoundEnabled = !isSoundEnabled;
        localStorage.setItem('screen-monitor-sound', isSoundEnabled ? 'active' : 'muted');
        updateSoundButton();
        if (isSoundEnabled) {
            speakAnnouncement('Pengumuman suara diaktifkan.');
        } else {
            window.speechSynthesis.cancel();
        }
    });

    // 4. Render Card HTML
    function buildCardHtml(pos, idx) {
        const isBreak = pos.queue_status === 'BREAK';
        const readyIndex = pos.ready_index !== undefined ? pos.ready_index : idx;
        const isNext = !isBreak && readyIndex === 0;
        const cardClass = isBreak
            ? 'break-card'
            : (isNext 
                ? (pos.is_logged_in ? 'active-turn' : 'active-turn offline-card')
                : (pos.is_logged_in ? '' : 'offline-card'));
        
        const statusBadge = isBreak
            ? `<span class="status-pill break">BREAK</span>`
            : (pos.is_logged_in
                ? `<span class="status-pill online">ONLINE</span>`
                : `<span class="status-pill offline">OFFLINE</span>`);

        const numberLabel = isBreak ? `B${(pos.break_index || 0) + 1}` : `#${pos.queue_number}`;

        const cmsCount = pos.order_counts.CMS || 0;
        const crmCount = pos.order_counts.CRM || 0;
        const otherCount = pos.order_counts.OTHER || 0;

        return `
            <div class="monitor-card ${cardClass}" data-user-id="${pos.user_id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center" style="gap: 16px;">
                        <div class="queue-number-badge">
                            ${numberLabel}
                        </div>
                        <div>
                            <h5 class="font-weight-bold mb-1" style="font-size: 16px; color: var(--text-main);">${pos.name}</h5>
                            <span class="fs-7" style="color: var(--text-sub);">@${pos.username}</span>
                        </div>
                    </div>
                    <div>
                        ${statusBadge}
                    </div>
                </div>
                
                <div class="card-divider"></div>
                
                <div class="stats-row">
                    <div class="stat-pill crm" title="Jumlah Order CRM Selesai Hari Ini">
                        <i class="fas fa-phone"></i> CRM: <strong>${crmCount}</strong>
                    </div>
                    <div class="stat-pill cms" title="Jumlah Order CMS Selesai Hari Ini">
                        <i class="fab fa-whatsapp"></i> CMS: <strong>${cmsCount}</strong>
                    </div>
                    <div class="stat-pill other" title="Jumlah Order Lainnya Selesai Hari Ini">
                        <i class="fas fa-cog"></i> Other: <strong>${otherCount}</strong>
                    </div>
                </div>
            </div>
        `;
    }

    // Filter logic
    function applyFilter(data) {
        // all_cc = online ready + break (excludes offline) — used for "Semua", "Online", "Break"
        let allCc = [];
        if (data && Array.isArray(data.all_cc)) {
            allCc = data.all_cc;
        }

        // full ready queue including offline — used only for "Offline" filter
        let fullQueue = [];
        if (data && Array.isArray(data.queue)) {
            fullQueue = data.queue;
        }

        if (currentFilter === 'all') {
            return allCc;
        }
        if (currentFilter === 'online') {
            return allCc
                .filter(pos => pos.is_logged_in && pos.queue_status !== 'BREAK')
                .map((pos, idx) => ({ ...pos, ready_index: idx }));
        }
        if (currentFilter === 'offline') {
            // Source from full queue so offline users are actually present
            return fullQueue
                .filter(pos => !pos.is_logged_in)
                .map((pos, idx) => ({ ...pos, ready_index: idx }));
        }
        if (currentFilter === 'break') {
            return allCc
                .filter(pos => pos.queue_status === 'BREAK')
                .map((pos, idx) => ({ ...pos, break_index: idx }));
        }
        return allCc;
    }

    function updateFilterButtons() {
        document.querySelectorAll('#screen-filter-group .screen-filter-btn').forEach(btn => {
            const filter = btn.getAttribute('data-filter');
            if (filter === currentFilter) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function getEmptyFilterText() {
        const messages = {
            all: 'Belum ada staff CEC aktif dalam antrian atau break.',
            online: 'Tidak ada staff CEC online di antrian ready.',
            offline: 'Tidak ada staff CEC offline di antrian ready.',
            break: 'Tidak ada staff CEC yang sedang break.',
        };

        return messages[currentFilter] || 'Tidak ada staff CEC untuk filter yang dipilih.';
    }

    document.querySelectorAll('#screen-filter-group .screen-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.getAttribute('data-filter');
            localStorage.setItem('screen-monitor-filter', currentFilter);
            updateFilterButtons();
            const filtered = applyFilter(latestCcList);
            latestFilteredList = filtered;
            updateMonitorGrid(filtered, true);
        });
    });

    // Initialize filter buttons state
    updateFilterButtons();

    // 5. Update Grid with 2D FLIP Animation
    function updateMonitorGrid(queue, isFilteredView = false) {
        if (!gridContainer) return;

        // Voice announcements should always be based on the full CC list,
        // not the filtered view, to avoid misleading announcements.
        if (!isFilteredView && previousAllCc !== null && isSoundEnabled && latestCcList && Array.isArray(latestCcList.all_cc)) {
            const currentAllCc = latestCcList.all_cc;
            const previousAllCcCopy = previousAllCc;

            // 1. Check if first ready person in the full queue has changed
            const currentFirst = currentAllCc.find(pos => pos.queue_status !== 'BREAK') || null;
            const previousFirst = previousAllCcCopy.find(pos => pos.queue_status !== 'BREAK') || null;

            if (currentFirst && (!previousFirst || currentFirst.user_id !== previousFirst.user_id)) {
                speakAnnouncement(`Antrian bergeser. Giliran berikutnya adalah ${currentFirst.name}.`);
            }

            // 2. Check for individual user online/offline status changes
            currentAllCc.forEach(pos => {
                const prevPos = previousAllCcCopy.find(p => p.user_id === pos.user_id);
                if (prevPos) {
                    if (pos.is_logged_in && !prevPos.is_logged_in) {
                        speakAnnouncement(`${pos.name} sekarang online.`);
                    } else if (!pos.is_logged_in && prevPos.is_logged_in) {
                        speakAnnouncement(`${pos.name} sekarang offline.`);
                    }
                } else {
                    const statusText = pos.is_logged_in ? 'online' : 'offline';
                    speakAnnouncement(`${pos.name} masuk antrian, status ${statusText}.`);
                }
            });
        }

        // Save current filtered queue as previous queue for next FLIP animation comparison
        previousQueue = JSON.parse(JSON.stringify(queue));

        // Step 1: First - record coordinates of existing cards
        const cards = gridContainer.querySelectorAll('.monitor-card');
        const initialRects = {};
        cards.forEach(card => {
            const id = card.getAttribute('data-user-id');
            initialRects[id] = card.getBoundingClientRect();
        });

        // Step 2: Render new HTML
        let html = '';
        if (queue && queue.length > 0) {
            queue.forEach((pos, idx) => {
                html += buildCardHtml(pos, idx);
            });
        } else {
            html = `
                <div class="col-12 text-center py-5 bg-dark-glass rounded-lg border-glass">
                    <span style="font-size: 40px;">👥</span>
                    <h5 class="font-weight-bold mt-3 mb-2" style="color: var(--text-main);">Data Tidak Ada</h5>
                    <p class="fs-7 mb-0" style="color: var(--text-sub);">${getEmptyFilterText()}</p>
                </div>
            `;
        }
        gridContainer.innerHTML = html;

        // Step 3: Invert & Play (FLIP 2D)
        const newCards = gridContainer.querySelectorAll('.monitor-card');
        newCards.forEach(card => {
            const id = card.getAttribute('data-user-id');
            const initialRect = initialRects[id];

            if (initialRect) {
                const finalRect = card.getBoundingClientRect();
                const deltaX = initialRect.left - finalRect.left;
                const deltaY = initialRect.top - finalRect.top;

                if (deltaX !== 0 || deltaY !== 0) {
                    // Invert
                    card.style.transform = `translate3d(${deltaX}px, ${deltaY}px, 0)`;
                    card.style.transition = 'none';

                    // Play
                    requestAnimationFrame(() => {
                        card.offsetHeight; // force repaint
                        card.style.transform = '';
                        card.style.transition = 'transform 380ms cubic-bezier(0.4, 0, 0.2, 1)';
                    });
                }
            } else {
                // New card entering
                card.style.opacity = '0';
                card.style.transform = 'translateY(15px)';
                requestAnimationFrame(() => {
                    card.offsetHeight;
                    card.style.opacity = '';
                    card.style.transform = '';
                    card.style.transition = 'opacity 300ms ease, transform 300ms ease';
                });
            }
        });
    }

    function updateTitipanPanel(titipanOrders) {
        const panelContainer = document.getElementById('titipan-panel-container');
        const grid = document.getElementById('titipan-list-grid');
        if (!panelContainer || !grid) return;

        if (!titipanOrders || titipanOrders.length === 0) {
            panelContainer.classList.add('d-none');
            grid.innerHTML = '';
            return;
        }

        panelContainer.classList.remove('d-none');
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
                    if (isSoundEnabled) {
                        const voiceText = `Pemberitahuan, ada titipan booking antrian untuk pukul ${formattedTime} kebutuhan ${item.requirement}`;
                        speakAnnouncement(voiceText);
                    }
                }
            }

            html += `
                <div class="col-md-6 mb-3">
                    <div class="titipan-warning-card animate-pulse-yellow border-glass">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-warning px-2.5 py-1 rounded-pill border border-warning fs-8 font-weight-bold mr-2"><i class="fas fa-clipboard-list mr-1"></i>${item.requirement}</span>
                                <span class="text-white font-weight-bold fs-7">${formattedDate}</span>
                            </div>
                            <span class="badge bg-warning text-dark px-2 py-1 fs-8 font-monospace font-weight-bold"><i class="far fa-clock mr-1"></i>${formattedTime}</span>
                        </div>
                        <div class="text-white-50 fs-8" style="font-size: 13px;">${item.description || 'Tidak ada deskripsi.'}</div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
    }

    // 6. Polling Data Fetcher
    function fetchMonitorData() {
        $.ajax({
            url: '/admin/screen/data',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (statusDot) {
                    statusDot.className = 'online-indicator-dot pulse-green mr-2';
                    statusText.textContent = 'Connected';
                }
                latestCcList = data;
                const filtered = applyFilter(data);
                latestFilteredList = filtered;
                updateMonitorGrid(filtered, false);
                updateTitipanPanel(data.titipan_orders);

                // Save full CC list for accurate voice-announcement comparisons next poll
                if (data && Array.isArray(data.all_cc)) {
                    previousAllCc = JSON.parse(JSON.stringify(data.all_cc));
                }

                // 5-minute warning: check if position-1 person is still waiting
                if (isSoundEnabled && data && Array.isArray(data.queue) && data.queue.length > 0) {
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
                            speakAnnouncement(`Antrian selanjutnya ${currentFirst.name} harap bersiap menerima order ERA`);
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Screen monitoring fetch error:", error);
                if (statusDot) {
                    statusDot.className = 'offline-indicator-dot bg-danger mr-2';
                    statusText.textContent = 'Disconnected';
                }
            }
        });
    }

    // Initial load
    fetchMonitorData();

    // Start Polling every 3 seconds
    pollingInterval = setInterval(fetchMonitorData, 3000);

    // Clean up interval on page unload
    $(window).on('beforeunload', function() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
});
</script>
@endpush
