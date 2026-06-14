// Realtime AJAX Polling System

window.startRealtimePolling = function(callback, intervalMs = 3000) {
  let failureCount = 0;
  const statusDot = document.getElementById('connection-status-dot');
  
  function createBanner() {
    if (document.getElementById('connection-warning-banner')) return;
    const banner = document.createElement('div');
    banner.id = 'connection-warning-banner';
    banner.className = 'alert alert-warning text-center border-0 rounded-0 py-2 fs-8 mb-0 w-100 shadow-sm';
    banner.style.position = 'fixed';
    banner.style.top = '0';
    banner.style.left = '0';
    banner.style.zIndex = '9999';
    banner.innerHTML = '<i class="fas fa-exclamation-triangle text-warning mr-2"></i> Koneksi ke server terganggu, mencoba menghubungkan kembali...';
    document.body.prepend(banner);
  }

  function removeBanner() {
    const banner = document.getElementById('connection-warning-banner');
    if (banner) banner.remove();
  }

  function updateStatusIndicator(isOk) {
    if (!statusDot) return;
    const statusText = document.getElementById('connection-status-text');
    if (isOk) {
      statusDot.className = 'online-indicator-dot pulse-green mr-2';
      statusDot.style.backgroundColor = '#10b981';
      statusDot.style.boxShadow = '0 0 0 2px rgba(16, 185, 129, 0.4)';
      statusDot.setAttribute('title', 'Koneksi aktif');
      if (statusText) statusText.innerText = 'Online';
      removeBanner();
    } else {
      statusDot.className = 'offline-indicator-dot bg-danger mr-2';
      statusDot.style.backgroundColor = '#ef4444';
      statusDot.style.boxShadow = '0 0 0 2px rgba(239, 68, 68, 0.4)';
      statusDot.setAttribute('title', 'Koneksi terputus');
      if (statusText) statusText.innerText = 'Offline';
      createBanner();
    }
  }

  function fetchDashboardData() {
    fetch('/dashboard/data', {
      headers: { 
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(async res => {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      failureCount = 0;
      updateStatusIndicator(true);
      
      if (typeof callback === 'function') {
        callback(data);
      }
    })
    .catch(err => {
      console.error('Polling error:', err);
      failureCount++;
      if (failureCount >= 3) {
        updateStatusIndicator(false);
      }
    });
  }

  // Initial load
  fetchDashboardData();

  // Polling loop
  return setInterval(fetchDashboardData, intervalMs);
};
