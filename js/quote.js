// Multi-step quote form: step navigation, per-step validation, service-specific fields, review summary
(function () {
  var form = document.getElementById('qf'); if (!form) return;
  var steps = [].slice.call(form.querySelectorAll('.step')), dots = [].slice.call(form.querySelectorAll('[data-dot]'));
  var back = document.getElementById('back'), next = document.getElementById('next'), send = document.getElementById('send');
  var cur = 0;
  function $(id) { return document.getElementById(id); }
  function selected() { return [].slice.call(form.querySelectorAll('input[name="services[]"]:checked')); }
  function setErr(input, msg) { var box = input.parentNode.querySelector('.err'); if (!box) { box = document.createElement('div'); box.className = 'err'; box.setAttribute('role', 'alert'); input.parentNode.appendChild(box); } box.textContent = msg; if (msg) input.setAttribute('aria-invalid', 'true'); else input.removeAttribute('aria-invalid'); return !msg; }
  function validate(step) {
    var ok = true;
    if (step === 0) {
      ok = setErr($('full_name'), $('full_name').value.trim().length < 2 ? 'Full name is required' : '') && ok;
      ok = setErr($('business_name'), $('business_name').value.trim() ? '' : 'Business name is required') && ok;
      ok = setErr($('email'), /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test($('email').value.trim()) ? '' : 'Enter a valid email address') && ok;
      ok = setErr($('phone'), $('phone').value.trim().length < 5 ? 'Phone number is required' : '') && ok;
    }
    if (step === 1) { ok = selected().length > 0; $('e-services').textContent = ok ? '' : 'Select at least one service'; }
    if (step === 3) { ok = $('agree').checked; $('e-agree').textContent = ok ? '' : 'Please agree to be contacted'; }
    return ok;
  }
  function syncGroups() { var on = selected().map(function (c) { return c.value; }); form.querySelectorAll('.grp').forEach(function (g) { g.classList.toggle('on', on.indexOf(g.getAttribute('data-service')) > -1); }); }
  function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
  function review() {
    var h = '<p><b>' + esc($('full_name').value) + '</b>, ' + esc($('business_name').value) + '<br>' + esc($('email').value) + ' · ' + esc($('phone').value) + ' (prefers ' + esc($('contact').value) + ')</p>';
    h += '<p><b>Services:</b> ' + selected().map(function (c) { return esc(c.getAttribute('data-name')); }).join(', ') + '</p>';
    form.querySelectorAll('.grp.on [data-label]').forEach(function (f) { if (f.value) h += '<p style="margin:4px 0"><span class="mut">' + esc(f.getAttribute('data-label')) + ':</span> ' + esc(f.value) + '</p>'; });
    if ($('message').value.trim()) h += '<p><b>Notes:</b> ' + esc($('message').value) + '</p>';
    $('review').innerHTML = h;
  }
  function show(n) {
    cur = n; steps.forEach(function (s, i) { s.classList.toggle('on', i === n); });
    dots.forEach(function (d, i) { d.classList.toggle('on', i === n); if (i === n) d.setAttribute('aria-current', 'step'); else d.removeAttribute('aria-current'); });
    back.hidden = n === 0; next.hidden = n === 3; send.hidden = n !== 3;
    if (n === 2) syncGroups(); if (n === 3) review();
    window.scrollTo({ top: form.offsetTop - 90, behavior: 'smooth' });
  }
  form.querySelectorAll('input[name="services[]"]').forEach(function (c) { c.addEventListener('change', function () { c.closest('.opt').classList.toggle('on', c.checked); syncGroups(); }); });
  form.querySelectorAll('input[name="services[]"]:checked').forEach(function (c) { c.closest('.opt').classList.add('on'); });
  next.addEventListener('click', function () { if (validate(cur)) show(cur + 1); });
  back.addEventListener('click', function () { show(cur - 1); });
  form.addEventListener('submit', function (e) { if (!validate(3)) { e.preventDefault(); } });
  form.addEventListener('keydown', function (e) { if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && cur < 3) { e.preventDefault(); next.click(); } });
  syncGroups(); show(0);
})();
