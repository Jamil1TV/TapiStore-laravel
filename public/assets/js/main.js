/**
 * TAPI Store — Main JavaScript
 * Vanilla JS for interactions, mobile menu, cart, and form validation.
 */

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initDropdowns();
    initFlashMessages();
    initQuantitySelectors();
    initTabs();
    initPaymentToggle();
    initStarRating();
    initStickyNav();
});

/* ----- Mobile Menu ----- */
function initMobileMenu() {
    const btn = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('mobileMenu');
    if (!btn || !menu) return;
    btn.addEventListener('click', () => {
        btn.classList.toggle('active');
        menu.classList.toggle('show');
    });
}

/* ----- Dropdowns ----- */
function initDropdowns() {
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                m.style.opacity = ''; m.style.visibility = '';
            });
        }
    });
}

/* ----- Flash Messages Auto-dismiss ----- */
function initFlashMessages() {
    const flash = document.getElementById('flashMessage');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity .5s, transform .5s';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-20px)';
            setTimeout(() => flash.remove(), 500);
        }, 4000);
    }
}

/* ----- Quantity Selectors ----- */
function initQuantitySelectors() {
    document.querySelectorAll('.qty-selector, .cart-qty').forEach(sel => {
        const input = sel.querySelector('input');
        const minus = sel.querySelector('[data-action="minus"]');
        const plus = sel.querySelector('[data-action="plus"]');
        if (!input) return;
        const max = parseInt(input.dataset.max) || 999;
        if (minus) minus.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            if (val > 1) { input.value = val - 1; input.dispatchEvent(new Event('change')); }
        });
        if (plus) plus.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            if (val < max) { input.value = val + 1; input.dispatchEvent(new Event('change')); }
        });
    });
}

/* ----- Tabs ----- */
function initTabs() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const group = btn.closest('.product-tabs') || document;
            group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            group.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            const target = document.getElementById(btn.dataset.tab);
            if (target) target.classList.add('active');
        });
    });
}

/* ----- Payment Method Toggle ----- */
function initPaymentToggle() {
    const radios = document.querySelectorAll('input[name="payment_method"]');
    const ccFields = document.getElementById('creditCardFields');
    if (!ccFields || !radios.length) return;
    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
            radio.closest('.payment-option')?.classList.add('selected');
            ccFields.classList.toggle('show', radio.value === 'credit_card');
        });
    });
}

/* ----- Star Rating Input ----- */
function initStarRating() {
    const container = document.querySelector('.star-rating-input');
    if (!container) return;
    const labels = container.querySelectorAll('label');
    const radios = container.querySelectorAll('input');
    labels.forEach((label, i) => {
        label.addEventListener('click', () => {
            labels.forEach((l, j) => l.classList.toggle('active', j <= i));
        });
        label.addEventListener('mouseenter', () => {
            labels.forEach((l, j) => l.style.color = j <= i ? '#f59e0b' : '');
        });
        label.addEventListener('mouseleave', () => {
            labels.forEach(l => l.style.color = '');
        });
    });
}

/* ----- Sticky Nav Shadow ----- */
function initStickyNav() {
    const nav = document.getElementById('mainNav');
    if (!nav) return;
    window.addEventListener('scroll', () => {
        nav.style.boxShadow = window.scrollY > 10
            ? '0 4px 20px rgba(0,0,0,.08)' : '0 1px 2px rgba(0,0,0,.05)';
    });
}

/* ----- Cart AJAX Helpers ----- */
function addToCart(productId, quantity = 1) {
    fetch(siteUrl + '/pages/cart_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&product_id=${productId}&quantity=${quantity}&csrf_token=${csrfToken}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cartCount;
            else {
                const link = document.querySelector('.cart-link');
                if (link) {
                    const b = document.createElement('span');
                    b.className = 'cart-badge'; b.id = 'cartBadge';
                    b.textContent = data.cartCount;
                    link.appendChild(b);
                }
            }
        } else {
            if (data.redirect) window.location.href = data.redirect;
            else showToast(data.message, 'error');
        }
    })
    .catch(() => showToast('An error occurred.', 'error'));
}

function removeFromWishlist(productId) {
    fetch(siteUrl + '/pages/wishlist_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&product_id=${productId}&csrf_token=${csrfToken}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-wishlist-id="${productId}"]`);
            if (card) { card.style.opacity = '0'; card.style.transform = 'scale(.9)'; setTimeout(() => card.remove(), 300); }
            showToast(data.message, 'success');
        } else showToast(data.message, 'error');
    });
}

function addToWishlist(productId) {
    fetch(siteUrl + '/pages/wishlist_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&product_id=${productId}&csrf_token=${csrfToken}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) showToast(data.message, 'success');
        else {
            if (data.redirect) window.location.href = data.redirect;
            else showToast(data.message, 'error');
        }
    });
}

/* ----- Toast Notification ----- */
function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()">&times;</button>`;
    toast.style.cssText = `position:fixed;top:20px;right:20px;z-index:9999;padding:14px 20px;border-radius:8px;font-size:.9rem;font-weight:500;display:flex;align-items:center;gap:12px;animation:slideDown .4s ease;box-shadow:0 10px 25px rgba(0,0,0,.15);`;
    if (type === 'success') toast.style.background = '#ecfdf5'; toast.style.color = '#065f46';
    if (type === 'error') { toast.style.background = '#fef2f2'; toast.style.color = '#991b1b'; }
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }, 3500);
}

/* ----- Form Validation ----- */
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    let valid = true;
    form.querySelectorAll('.form-error').forEach(e => e.remove());
    form.querySelectorAll('.form-control.error').forEach(e => e.classList.remove('error'));

    form.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'This field is required.');
            valid = false;
        }
    });

    form.querySelectorAll('input[type="email"]').forEach(input => {
        if (input.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
            showFieldError(input, 'Please enter a valid email address.');
            valid = false;
        }
    });

    const pw = form.querySelector('input[name="password"]');
    const cpw = form.querySelector('input[name="confirm_password"]');
    if (pw && cpw && pw.value !== cpw.value) {
        showFieldError(cpw, 'Passwords do not match.');
        valid = false;
    }

    if (pw && pw.value && pw.value.length < 6) {
        showFieldError(pw, 'Password must be at least 6 characters.');
        valid = false;
    }

    return valid;
}

function showFieldError(input, message) {
    input.classList.add('error');
    const err = document.createElement('div');
    err.className = 'form-error';
    err.textContent = message;
    input.parentNode.appendChild(err);
}

/* ----- Price Range Filter ----- */
function updatePriceLabel(val) {
    const label = document.getElementById('priceMaxLabel');
    if (label) label.textContent = '$' + val;
}

/* ----- Confirm Delete ----- */
function confirmDelete(message = 'Are you sure you want to delete this?') {
    return confirm(message);
}
