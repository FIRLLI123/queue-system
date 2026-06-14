// Queue Animation System (FLIP - First, Last, Invert, Play)

window.renderQueueList = function(containerId, queue) {
  const container = document.getElementById(containerId);
  if (!container) return;

  // 1. First: record coordinates of existing cards
  const items = container.querySelectorAll('.queue-item');
  const initialRects = {};
  items.forEach(item => {
    const id = item.getAttribute('data-user-id');
    initialRects[id] = item.getBoundingClientRect();
  });

  // 2. Build the new HTML structure
  let html = '';
  if (queue && queue.length > 0) {
    queue.forEach((pos, idx) => {
      const isNext = idx === 0;
      const statusDot = pos.is_logged_in 
        ? '<span class="online-indicator-dot pulse-green"></span>' 
        : '<span class="offline-indicator-dot"></span>';
      
      const statusBadge = pos.is_logged_in
        ? `<span class="badge-pill-custom ${isNext ? 'badge-primary-custom' : 'badge-success-custom'}">NEXT</span>`
        : `<span class="badge-pill-custom badge-warning-custom">OFFLINE</span>`;
      
      const cardClass = isNext 
        ? (pos.is_logged_in ? 'next-turn' : 'offline-state')
        : '';
        
      const statusText = pos.is_logged_in ? 'Online' : 'Belum login';

      html += `
        <div class="queue-item ${cardClass} d-flex align-items-center justify-content-between p-3 mb-2 rounded shadow-sm" data-user-id="${pos.user_id}" style="
          transition: transform 280ms cubic-bezier(0.4, 0, 0.2, 1), opacity 280ms ease;
          background-color: var(--card);
          border: 1px solid var(--border);
        ">
          <div class="d-flex align-items-center gap-3" style="gap: 15px;">
            <span class="fw-bold ${isNext ? 'text-primary fs-5' : 'text-muted'}" style="width: 25px; font-weight: 700;">#${pos.queue_number}</span>
            ${statusDot}
            <div>
              <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 13px;">${pos.name}</h6>
              <span class="text-secondary" style="font-size: 11px;">@${pos.username}</span>
            </div>
          </div>
          <div class="text-right">
            ${isNext ? statusBadge : `<span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 10px;">${statusText}</span>`}
          </div>
        </div>
      `;
    });
  } else {
    html = `
      <div class="empty-state-container bg-white border border-light-subtle rounded py-4 w-100 text-center">
        <span class="empty-state-icon" style="font-size: 32px;">👥</span>
        <h6 class="font-weight-bold text-dark mb-1 mt-2">Antrian Kosong</h6>
        <p class="text-secondary mb-0" style="font-size: 11px;">Staff CC aktif belum dimasukkan ke antrian.</p>
      </div>
    `;
  }

  // 3. Update the DOM
  container.innerHTML = html;

  // 4. Invert & Play (FLIP)
  const newItems = container.querySelectorAll('.queue-item');
  newItems.forEach((item, idx) => {
    const id = item.getAttribute('data-user-id');
    const initialRect = initialRects[id];
    
    if (initialRect) {
      const finalRect = item.getBoundingClientRect();
      const deltaY = initialRect.top - finalRect.top;
      
      if (deltaY !== 0) {
        // Invert
        item.style.transform = `translate3d(0, ${deltaY}px, 0)`;
        item.style.transition = 'none';
        
        // Play
        requestAnimationFrame(() => {
          item.offsetHeight; // trigger repaint
          item.style.transform = '';
          item.style.transition = 'transform 280ms cubic-bezier(0.4, 0, 0.2, 1)';
        });
      }
    } else if (queue && queue.length > 0) {
      // It's a new card entering at the end
      item.classList.add('entering');
      setTimeout(() => {
        item.classList.remove('entering');
      }, 300);
    }
  });

  // Stagger highlight check on first item if it changed
  const firstItem = container.querySelector('.queue-item');
  if (firstItem) {
    const firstId = firstItem.getAttribute('data-user-id');
    const firstRect = initialRects[firstId];
    if (firstRect && firstRect.top > firstItem.getBoundingClientRect().top) {
      firstItem.classList.add('first-highlight');
      setTimeout(() => {
        firstItem.classList.remove('first-highlight');
      }, 600);
    }
  }
};
