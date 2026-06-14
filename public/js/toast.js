// Custom Toast Notification System

window.showToast = function(message, type = 'success') {
  let container = document.getElementById('toast-notification-container');
  
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-notification-container';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '99999';
    container.style.width = '350px';
    document.body.appendChild(container);
  }

  const toastId = 'toast-' + Date.now();
  
  let icon = 'fa-solid fa-circle-check';
  let themeColor = '#10B981';
  let bgColor = '#ECFDF5';
  let borderColor = 'rgba(16, 185, 129, 0.2)';
  
  if (type === 'error' || type === 'danger') {
    icon = 'fa-solid fa-circle-xmark';
    themeColor = '#EF4444';
    bgColor = '#FEF2F2';
    borderColor = 'rgba(239, 68, 68, 0.2)';
  } else if (type === 'info' || type === 'primary') {
    icon = 'fa-solid fa-circle-info';
    themeColor = '#2563EB';
    bgColor = '#EFF6FF';
    borderColor = 'rgba(37, 99, 235, 0.2)';
  } else if (type === 'warning') {
    icon = 'fa-solid fa-triangle-exclamation';
    themeColor = '#F59E0B';
    bgColor = '#FFFBEB';
    borderColor = 'rgba(245, 158, 11, 0.2)';
  }

  const toastHtml = `
    <div id="${toastId}" class="toast-slide-in p-3 rounded-4 mb-3" style="
      background-color: ${bgColor || '#ecfdf5'};
      border: 1px solid ${borderColor || 'rgba(16, 185, 129, 0.2)'};
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    ">
      <div class="d-flex align-items-start gap-3">
        <i class="${icon} fs-5" style="color: ${themeColor}; margin-top: 2px;"></i>
        <div style="flex: 1;">
          <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">
            ${type.charAt(0).toUpperCase() + type.slice(1)}
          </h6>
          <p class="mb-0 text-secondary mt-1" style="font-size: 12px; line-height: 1.4;">
            ${message}
          </p>
        </div>
        <button type="button" class="close" style="font-size: 16px; opacity: 0.6; padding: 0; border: none; background: transparent;" onclick="dismissToast('${toastId}')">&times;</button>
      </div>
      <!-- Progress timer bar -->
      <div class="toast-progress" id="${toastId}-progress" style="
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        background-color: ${themeColor};
        opacity: 0.3;
        transition: width 4000ms linear;
      "></div>
    </div>
  `;

  container.insertAdjacentHTML('beforeend', toastHtml);

  // Trigger progress bar transition in the next frame
  requestAnimationFrame(() => {
    const progress = document.getElementById(`${toastId}-progress`);
    if (progress) progress.style.width = '0%';
  });

  // Auto dismiss after 4 seconds
  const autoDismissTimer = setTimeout(() => {
    dismissToast(toastId);
  }, 4000);

  // Expose dismiss function to window if not already done
  if (!window.dismissToast) {
    window.dismissToast = function(id) {
      const toast = document.getElementById(id);
      if (toast) {
        toast.style.animation = 'slideOutRight 0.2s ease forwards';
        
        toast.addEventListener('animationend', function() {
          toast.remove();
        });
      }
    };
  }
};
