
(function() {
  function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
  function qsa(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }
  function on(el, evt, handler, opts) { if (el) el.addEventListener(evt, handler, opts || false); }
  function ajaxPost(url, data) {
    const body = typeof data === 'string' ? data : new URLSearchParams(data).toString();
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body
    });
  }
  function show(el) { if (el) el.style.display = 'block'; }
  function hide(el) { if (el) el.style.display = 'none'; }
  function html(el, content) { if (el) el.innerHTML = content; }

  // Suggestions for product search
  function suggestion() {
    const sugInput = qs('#sug_input');
    const resultBox = qs('#result');

    if (!sugInput || !resultBox) return;

    on(sugInput, 'keyup', function(e) {
      const titleInput = qs('input[name=title]');
      const productName = titleInput ? titleInput.value : sugInput.value;

      if ((productName || '').length >= 1) {
        ajaxPost('ajax.php', { product_name: productName })
          .then(r => r.text())
          .then(data => {
            html(resultBox, data);
            show(resultBox);
            // Delegate clicks on list items
            qsa('#result li').forEach(function(li) {
              on(li, 'click', function() {
                sugInput.value = this.textContent.trim();
                hide(resultBox);
              });
            });
          })
          .catch(() => { hide(resultBox); });
      } else {
        hide(resultBox);
      }

      e.preventDefault();
    });

    on(sugInput, 'blur', function() { setTimeout(function(){ hide(resultBox); }, 150); });
  }

  // Form submit for product lookup
  function bindSuggestionForm() {
    const form = qs('#sug-form');
    if (!form) return;

    on(form, 'submit', function(e) {
      e.preventDefault();
      const titleInput = qs('input[name=title]');
      const pName = titleInput ? titleInput.value : '';
      const productInfo = qs('#product_info');

      ajaxPost('ajax.php', { p_name: pName })
        .then(r => r.text())
        .then(data => {
          if (productInfo) {
            html(productInfo, data);
            productInfo.style.display = 'block';
          }
          total();
        })
        .catch(() => {
          if (productInfo) {
            productInfo.style.display = 'block';
          }
        });
    });
  }

  // Calculate totals
  function total() {
    const container = qs('#product_info') || document;
    on(container, 'input', function(e) {
      const priceInput = qs('input[name=price]', container);
      const qtyInput = qs('input[name=quantity]', container);
      const totalInput = qs('input[name=total]', container);
      const price = parseFloat(priceInput && priceInput.value) || 0;
      const qty = parseFloat(qtyInput && qtyInput.value) || 0;
      if (totalInput) totalInput.value = (qty * price).toFixed(2);
    });
  }

  // Submenu toggle (sidebar)
  function bindSubmenus() {
    qsa('.submenu-toggle').forEach(function(btn) {
      on(btn, 'click', function() {
        var parent = this.parentElement;
        if (!parent) return;
        var submenu = parent.querySelector('ul.submenu');
        if (submenu) {
          if (submenu.style.display === 'block') {
            submenu.style.display = 'none';
          } else {
            submenu.style.display = 'block';
          }
        }
      });
    });
  }

  
  // auto-dismiss and manual close
  function initAlerts() {
    function closeAlert(el) {
      if (!el || el.dataset.closing) return;
      el.dataset.closing = '1';
      el.style.transition = 'opacity 0.3s ease';
      el.style.opacity = '0';
      setTimeout(function() {
        if (el && el.parentNode) el.parentNode.removeChild(el);
      }, 300);
    }

    qsa('.alert').forEach(function(alert) {
      setTimeout(function(){ closeAlert(alert); }, 5000);

      // Make the "x" close button work
      alert.addEventListener('click', function(e) {
        var target = e.target;
        var isClose = (target && target.classList && target.classList.contains('close')) ||
                      (target && target.closest && target.closest('.close'));
        if (isClose) {
          e.preventDefault();
          e.stopPropagation();
          closeAlert(alert);
        }
      }, true);
    });
  }

  // Toggle password visibility
  window.togglePassword = function() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    if (!passwordField) return;
    if (passwordField.type === 'password') {
      passwordField.type = 'text';
      if (toggleIcon) {
        toggleIcon.classList.remove('glyphicon-eye-open');
        toggleIcon.classList.add('glyphicon-eye-close');
      }
    } else {
      passwordField.type = 'password';
      if (toggleIcon) {
        toggleIcon.classList.remove('glyphicon-eye-close');
        toggleIcon.classList.add('glyphicon-eye-open');
      }
    }
  };

  document.addEventListener('DOMContentLoaded', function() {
    // Initialize behaviors
    suggestion();
    bindSuggestionForm();
    total();
    bindSubmenus();
    initAlerts();

  });
})();



// Minimal Chart.js stub for offline mode
(function(global){
  function Chart(ctx, config){
    // no-op; optionally store data for debugging
    this.ctx = ctx;
    this.config = config || {};
    this.destroy = function(){};
    this.update = function(){};
  }
  Chart.defaults = {};
  Chart.version = 'stub-0.0.1';
  global.Chart = Chart;
})(typeof window !== 'undefined' ? window : this);


// dropdown-menu.js
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll("[data-toggle='dropdown']").forEach(function (toggle) {
    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      document.querySelectorAll(".dropdown.open").forEach(function (openMenu) {
        if (openMenu !== toggle.parentElement) {
          openMenu.classList.remove("open");
        }
      });

      toggle.parentElement.classList.toggle("open");
    });
  });

  document.addEventListener("click", function () {
    document.querySelectorAll(".dropdown.open").forEach(function (openMenu) {
      openMenu.classList.remove("open");
    });
  });
});

(function(global){
  function Chart(ctx, config){
   
    this.ctx = ctx;
    this.config = config || {};
    this.destroy = function(){};
    this.update = function(){};
  }
  Chart.defaults = {};
  Chart.version = 'stub-0.0.1';
  global.Chart = Chart;
})(typeof window !== 'undefined' ? window : this);





