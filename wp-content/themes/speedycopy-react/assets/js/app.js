// Mobile nav toggle
document.addEventListener('click', e => {
  const btn = e.target.closest('[data-toggle-nav]');
  if(btn){
    const nav = document.getElementById('scMainNav');
    nav.classList.toggle('is-open');
  }
});

// Single product thumbnails switcher
document.addEventListener('click', e => {
  const th = e.target.closest('.sc-thumb');
  if(th){
    const full = th.getAttribute('data-full');
    const main = document.getElementById('scMainImage');
    if(full && main){
      main.src = full;
      document.querySelectorAll('.sc-thumb').forEach(b=>b.classList.remove('is-active'));
      th.classList.add('is-active');
    }
  }
  const tabBtn = e.target.closest('.sc-tab-btn');
  if(tabBtn){
    const target = tabBtn.getAttribute('data-tab-target');
    if(target){
      document.querySelectorAll('.sc-tab-btn').forEach(b=>b.classList.remove('is-active'));
      tabBtn.classList.add('is-active');
      document.querySelectorAll('.sc-tab-panel').forEach(p=>p.classList.remove('is-active'));
      const panel = document.getElementById('tab-'+target);
      if(panel) panel.classList.add('is-active');
    }
  }
});

// Cart + generic quantity component
(function(){
  const cartForm = document.querySelector('.woocommerce-cart-form');
  let updateTimer;
  const triggerUpdate = () => {
    if(!cartForm) return;
    const btn = cartForm.querySelector('button[name="update_cart"]');
    if(btn){ btn.disabled = false; }
    // Submit diretto del form (mantiene nonce e campi nascosti)
    cartForm.requestSubmit ? cartForm.requestSubmit(btn) : cartForm.submit();
  };
  const schedule = () => {
    if(!cartForm) return; // solo nel carrello
    clearTimeout(updateTimer);
    updateTimer = setTimeout(triggerUpdate, 400);
  };

  document.addEventListener('click', e => {
    const decr = e.target.closest('[data-qty-decr]');
    const incr = e.target.closest('[data-qty-incr]');
    if(!decr && !incr) return;
    const wrap = e.target.closest('[data-qty-wrapper]');
    if(!wrap) return;
    const input = wrap.querySelector('[data-qty-input], input.qty');
    if(!input) return;
    const current = parseInt(input.value||'0',10);
    const min = parseInt(input.getAttribute('min')||'0',10);
    const max = parseInt(input.getAttribute('max')||'0',10);
    if(decr) {
      input.value = current>min ? current-1 : min;
    } else if(incr){
      if(!isNaN(max) && max>0) {
        input.value = current < max ? current+1 : max;
      } else {
        input.value = current+1;
      }
    }
    input.dispatchEvent(new Event('change', {bubbles:true}));
    schedule();
  });

  document.addEventListener('change', e => {
    if(!e.target.matches('[data-qty-input], input.qty')) return;
    const input = e.target;
    const min = parseInt(input.getAttribute('min')||'1',10);
    const max = parseInt(input.getAttribute('max')||'0',10);
    let val = parseInt(input.value||min,10);
    if(isNaN(val) || val < min) val = min;
    if(max>0 && val>max) val = max;
    input.value = val;
    schedule();
  });
})();
