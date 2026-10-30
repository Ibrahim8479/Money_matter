// Mobile menu, sticky header shadow, loading state on forms, confirm dialogs
(function () {
  var b = document.getElementById('burger'), n = document.getElementById('nav'), t = document.getElementById('top');
  if (b && n) b.addEventListener('click', function () { var o = n.classList.toggle('open'); b.setAttribute('aria-expanded', o); b.textContent = o ? '✕' : '☰'; });
  if (t) { var f = function () { t.classList.toggle('sc', window.scrollY > 8); }; f(); window.addEventListener('scroll', f); }
  document.querySelectorAll('form[data-loading]').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
      var btn = form.querySelector('button[type=submit]:not([hidden])') || form.querySelector('button[type=submit]'); if (!btn) return;
      setTimeout(function () { if (ev.defaultPrevented) return; btn.disabled = true; btn.textContent = 'Sending…'; }, 0);
    });
  });
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (ev) { if (!confirm(el.getAttribute('data-confirm'))) ev.preventDefault(); });
  });
})();
