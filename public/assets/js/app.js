/**
 * WebDocTruyen - Global Application Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initNavbar();
    initHeaderSearch();
    initUserDropdown();
});

// ================= THEME TOGGLE =================
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);

    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    if (themeToggleBtn) {
        updateThemeIcon(savedTheme);
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', nextTheme);
            localStorage.setItem('theme', nextTheme);
            updateThemeIcon(nextTheme);
        });
    }
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('theme-icon');
    if (!icon) return;
    if (theme === 'light') {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />';
    } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />';
    }
}

// ================= TOAST NOTIFICATION ENGINE =================
window.Toast = {
    show(message, type = 'success', duration = 3000) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const iconSvg = type === 'success'
            ? '<svg style="width:20px;height:20px;color:var(--success);flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
            : '<svg style="width:20px;height:20px;color:var(--danger);flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';

        toast.innerHTML = `${iconSvg}<span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
    success(msg) { this.show(msg, 'success'); },
    error(msg) { this.show(msg, 'error', 4000); }
};

// ================= NAVBAR & MOBILE MENU =================
function initNavbar() {
    const toggleBtn = document.querySelector('.mobile-nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (toggleBtn && navMenu) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            navMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!navMenu.contains(e.target) && !toggleBtn.contains(e.target)) {
                navMenu.classList.remove('show');
            }
        });
    }
}

// ================= USER DROPDOWN =================
function initUserDropdown() {
    const trigger = document.getElementById('user-menu-trigger');
    const menu = document.getElementById('user-menu-dropdown');

    if (trigger && menu) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            menu.classList.remove('show');
        });
    }
}

// ================= HEADER SEARCH DEBOUNCE =================
function initHeaderSearch() {
    const searchInput = document.getElementById('header-search-input');
    const resultsBox = document.getElementById('header-search-results');
    if (!searchInput || !resultsBox) return;

    let debounceTimer = null;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            resultsBox.classList.remove('show');
            resultsBox.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const res = await API.get('/stories', { q: query, limit: 5 });
                if (res.data && res.data.length > 0) {
                    resultsBox.innerHTML = res.data.map(story => `
                        <a href="${window.APP_URL}/stories/${story.slug}" class="search-result-item">
                            <img src="${window.UPLOAD_URL}/covers/${story.cover_image}" onerror="this.onerror=null;this.src='${window.APP_URL}/public/assets/images/default-cover.jpg'" class="search-result-cover" alt="${story.title}">
                            <div style="overflow:hidden;">
                                <div style="font-weight:700;font-size:0.88rem;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${story.title}</div>
                                <div style="font-size:0.78rem;color:var(--text-muted);">${story.author_name || 'Đang cập nhật'}</div>
                                <div style="font-size:0.75rem;color:var(--primary);margin-top:2px;">${story.latest_chapter ? 'Chương ' + story.latest_chapter.chapter_number : ''}</div>
                            </div>
                        </a>
                    `).join('') + `
                        <a href="${window.APP_URL}/search?q=${encodeURIComponent(query)}" style="display:block;text-align:center;padding:10px;font-size:0.85rem;color:var(--primary);font-weight:600;background:var(--bg-primary);">
                            Xem tất cả kết quả cho "${query}" &rarr;
                        </a>
                    `;
                    resultsBox.classList.add('show');
                } else {
                    resultsBox.innerHTML = `<div style="padding:16px;text-align:center;font-size:0.85rem;color:var(--text-muted);">Không tìm thấy truyện phù hợp</div>`;
                    resultsBox.classList.add('show');
                }
            } catch (err) {
                console.error(err);
            }
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.classList.remove('show');
        }
    });
}

// Global Modal helper
window.openModal = function(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('show');
};

window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('show');
};

// Global Logout helper
window.handleLogout = async function() {
    try {
        await API.post('/auth/logout');
        API.setToken(null);
        window.location.href = window.APP_URL || '/';
    } catch (err) {
        window.location.reload();
    }
};
