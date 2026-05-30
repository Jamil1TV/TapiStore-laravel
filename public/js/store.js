(function () {
    const root = document.documentElement;
    const savedTheme = localStorage.getItem('theme');
    const header = document.querySelector('.navbar');

    if (savedTheme) {
        root.dataset.theme = savedTheme;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('loaded');

        if (window.lucide) {
            window.lucide.createIcons();
        }

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
            new bootstrap.Tooltip(el);
        });

        updateHeaderState();
        window.addEventListener('scroll', updateHeaderState);

        document.getElementById('themeToggle')?.addEventListener('click', () => {
            const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            localStorage.setItem('theme', next);
        });

        document.querySelectorAll('.ajax-cart-form').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const button = form.querySelector('button[type="submit"]');
                button?.setAttribute('disabled', 'disabled');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: new FormData(form),
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || 'Unable to add item.');
                    }
                    document.querySelectorAll('.cart-count').forEach((el) => {
                        el.textContent = data.cart_count;
                    });
                    toast(data.message || 'Added to cart.');
                } catch (error) {
                    toast(error.message, true);
                } finally {
                    button?.removeAttribute('disabled');
                }
            });
        });

        const mainImage = document.querySelector('.gallery-main');
        document.querySelectorAll('.gallery-thumbs img').forEach((thumb) => {
            thumb.addEventListener('click', () => {
                if (mainImage) {
                    mainImage.src = thumb.src;
                }
            });
        });
    });

    function updateHeaderState() {
        if (!header) {
            return;
        }
        header.classList.toggle('scrolled', window.scrollY > 20);
    }

    function toast(message, isError = false) {
        const note = document.createElement('div');
        note.className = 'toast-note show';
        note.style.background = isError ? '#c2413a' : 'rgba(15, 23, 32, 0.96)';
        note.textContent = message;
        document.body.appendChild(note);
        setTimeout(() => note.classList.remove('show'), 2600);
        setTimeout(() => note.remove(), 2800);
    }
})();
