(function () {
  var form = document.getElementById('sc-newsletter-form');
  if (!form || typeof scNewsletter === 'undefined') return;

  var msg = form.querySelector('.sc-newsletter-msg');
  var btn = form.querySelector('button[type="submit"]');

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (msg) {
      msg.hidden = true;
      msg.classList.remove('is-ok', 'is-err');
    }

    var email = (form.querySelector('[name="email"]') || {}).value || '';
    var consent = form.querySelector('[name="consent"]');
    if (!email || !consent || !consent.checked) {
      if (msg) {
        msg.textContent = scNewsletter.strings.invalid;
        msg.classList.add('is-err');
        msg.hidden = false;
      }
      return;
    }

    if (btn) btn.disabled = true;
    var body = new FormData();
    body.append('action', 'sc_newsletter_subscribe');
    body.append('nonce', scNewsletter.nonce);
    body.append('email', email);
    body.append('consent', '1');

    fetch(scNewsletter.ajax_url, { method: 'POST', body: body, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var ok = !!(data && data.success);
        var text = ok
          ? (data.data && data.data.message) || scNewsletter.strings.ok
          : (data && data.data && data.data.message) || scNewsletter.strings.error;
        if (msg) {
          msg.textContent = text;
          msg.classList.add(ok ? 'is-ok' : 'is-err');
          msg.hidden = false;
        }
        if (ok) form.reset();
      })
      .catch(function () {
        if (msg) {
          msg.textContent = scNewsletter.strings.error;
          msg.classList.add('is-err');
          msg.hidden = false;
        }
      })
      .finally(function () {
        if (btn) btn.disabled = false;
      });
  });
})();
