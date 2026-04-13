// assets/js/main.js — Sportify Global JavaScript

document.addEventListener('DOMContentLoaded', () => {

  // ---- Sidebar Toggle (mobile) ----
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
    // Close when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (window.innerWidth < 992 && sidebar.classList.contains('open') &&
          !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  // ---- Active sidebar link highlight ----
  const currentPath = window.location.pathname;
  document.querySelectorAll('.sidebar .nav-item a').forEach(link => {
    if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href').split('/').pop().replace('.php',''))) {
      link.classList.add('active');
    }
  });

  // ---- Photo file input preview label ----
  document.querySelectorAll('input[type=file]').forEach(input => {
    if (input.id && document.querySelector(`label[for="${input.id}"]`)) return;
    input.addEventListener('change', function() {
      const label = this.nextElementSibling;
      if (label && label.tagName === 'LABEL') {
        label.textContent = this.files.length > 1 ? `${this.files.length} files selected` : this.files[0]?.name || 'Choose file';
      }
    });
  });

  // ---- Auto-dismiss alerts after 5s ----
  document.querySelectorAll('.alert-sportify').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity .5s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });

  // ---- Fade-in-up animation for stat cards ----
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.animationPlayState = 'running';
      }
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));

  // ---- Confirm before deleting ----
  document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      if (!confirm(btn.dataset.confirm)) e.preventDefault();
    });
  });

  // ---- Number formatting for stat values ----
  document.querySelectorAll('.stat-value').forEach(el => {
    const text = el.textContent.trim();
    if (/^\d+$/.test(text)) {
      el.textContent = parseInt(text).toLocaleString('en-IN');
    }
  });

  // ---- Tooltip initialisation (MDBootstrap) ----
  if (typeof mdb !== 'undefined') {
    document.querySelectorAll('[title]').forEach(el => {
      try { new mdb.Tooltip(el); } catch(e) {}
    });
  }

});

// ---- Utility: debounce ----
function debounce(fn, delay) {
  let timer;
  return function(...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  };
}
