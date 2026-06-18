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
    color: #1e4ed8;
}

.theme-dark .stat-pill.cms {
    background-color: rgba(59, 130, 246, 0.2) !important;
    border-color: rgba(59, 130, 246, 0.45) !important;
    color: #93c5fd !important;
}

.stat-pill.crm {
    color: #047857;
}

.theme-dark .stat-pill.crm {
    background-color: rgba(16, 185, 129, 0.2) !important;
    border-color: rgba(16, 185, 129, 0.45) !important;
    color: #6ee7b7 !important;
}

.stat-pill.other {
    color: #6d28d9;
}

.theme-dark .stat-pill.other {
    background-color: rgba(139, 92, 246, 0.2) !important;
    border-color: rgba(139, 92, 246, 0.45) !important;
    color: #c4b5fd !important;
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
    let isSoundEnabled = localStorage.getItem('screen-monitor-sound') !== 'muted';

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
    
    function speakAnnouncement(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            const voices = window.speechSynthesis.getVoices();
            const idVoice = voices.find(voice => voice.lang.includes('id') || voice.lang.includes('ID'));
            if (idVoice) {
                utterance.voice = idVoice;
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
        const isNext = idx === 0;
        const cardClass = isNext 
            ? (pos.is_logged_in ? 'active-turn' : 'active-turn offline-card')
            : (pos.is_logged_in ? '' : 'offline-card');
        
        const statusBadge = pos.is_logged_in
            ? `<span class="status-pill online">ONLINE</span>`
            : `<span class="status-pill offline">OFFLINE</span>`;

        const cmsCount = pos.order_counts.CMS || 0;
        const crmCount = pos.order_counts.CRM || 0;
        const otherCount = pos.order_counts.OTHER || 0;

        return `
            <div class="monitor-card ${cardClass}" data-user-id="${pos.user_id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center" style="gap: 16px;">
                        <div class="queue-number-badge">
                            #${pos.queue_number}
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
                    <div class="stat-pill cms" title="Jumlah Order CMS Selesai Hari Ini">
                        <i class="fab fa-whatsapp"></i> CMS: <strong>${cmsCount}</strong>
                    </div>
                    <div class="stat-pill crm" title="Jumlah Order CRM Selesai Hari Ini">
                        <i class="fas fa-phone"></i> CRM: <strong>${crmCount}</strong>
                    </div>
                    <div class="stat-pill other" title="Jumlah Order Lainnya Selesai Hari Ini">
                        <i class="fas fa-cog"></i> Other: <strong>${otherCount}</strong>
                    </div>
                </div>
            </div>
        `;
    }

    // 5. Update Grid with 2D FLIP Animation
    function updateMonitorGrid(queue) {
        if (!gridContainer) return;

        // Check for queue differences and trigger voice announcements
        if (previousQueue !== null && isSoundEnabled) {
            // 1. Check if first person in queue has changed
            const currentFirst = queue.length > 0 ? queue[0] : null;
            const previousFirst = previousQueue.length > 0 ? previousQueue[0] : null;

            if (currentFirst && (!previousFirst || currentFirst.user_id !== previousFirst.user_id)) {
                // First turn shifted!
                speakAnnouncement(`Antrian bergeser. Giliran berikutnya adalah ${currentFirst.name}.`);
            }

            // 2. Check for individual user online/offline status changes
            queue.forEach(pos => {
                const prevPos = previousQueue.find(p => p.user_id === pos.user_id);
                if (prevPos) {
                    if (pos.is_logged_in && !prevPos.is_logged_in) {
                        speakAnnouncement(`${pos.name} sekarang online.`);
                    } else if (!pos.is_logged_in && prevPos.is_logged_in) {
                        speakAnnouncement(`${pos.name} sekarang offline.`);
                    }
                } else {
                    // New user joined queue
                    const statusText = pos.is_logged_in ? 'online' : 'offline';
                    speakAnnouncement(`${pos.name} masuk antrian, status ${statusText}.`);
                }
            });
        }

        // Save current queue as previous queue for next comparison
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
                    <h5 class="font-weight-bold mt-3 mb-2" style="color: var(--text-main);">Antrian Kosong</h5>
                    <p class="fs-7 mb-0" style="color: var(--text-sub);">Belum ada staff CC aktif dalam antrian.</p>
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
                updateMonitorGrid(data.queue);
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
