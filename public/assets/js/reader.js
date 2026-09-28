/**
 * WebDocTruyen - Reader Engine
 * Hotkeys, customizable fonts, width, themes, auto progress save & sync
 */

document.addEventListener('DOMContentLoaded', () => {
    initReaderEngine();
});

function initReaderEngine() {
    const readerContainer = document.querySelector('.reader-container');
    const readerContent = document.querySelector('.reader-content');
    if (!readerContainer || !readerContent) return;

    // Load saved settings
    const settings = loadReaderSettings();
    applyReaderSettings(settings);

    // Settings Toggle
    const settingsBtn = document.getElementById('reader-settings-toggle');
    const settingsPanel = document.getElementById('reader-settings-panel');
    if (settingsBtn && settingsPanel) {
        settingsBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            settingsPanel.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!settingsPanel.contains(e.target) && !settingsBtn.contains(e.target)) {
                settingsPanel.classList.remove('show');
            }
        });
    }

    // Font size controls
    document.getElementById('btn-font-dec')?.addEventListener('click', () => {
        settings.fontSize = Math.max(14, settings.fontSize - 2);
        applyReaderSettings(settings);
    });

    document.getElementById('btn-font-inc')?.addEventListener('click', () => {
        settings.fontSize = Math.min(32, settings.fontSize + 2);
        applyReaderSettings(settings);
    });

    // Line height controls
    document.getElementById('btn-line-dec')?.addEventListener('click', () => {
        settings.lineHeight = Math.max(1.4, Math.round((settings.lineHeight - 0.2) * 10) / 10);
        applyReaderSettings(settings);
    });

    document.getElementById('btn-line-inc')?.addEventListener('click', () => {
        settings.lineHeight = Math.min(2.4, Math.round((settings.lineHeight + 0.2) * 10) / 10);
        applyReaderSettings(settings);
    });

    // Font family select
    document.getElementById('reader-font-select')?.addEventListener('change', (e) => {
        settings.fontFamily = e.target.value;
        applyReaderSettings(settings);
    });

    // Width select
    document.querySelectorAll('.btn-width-select').forEach(btn => {
        btn.addEventListener('click', () => {
            settings.width = btn.dataset.width;
            applyReaderSettings(settings);
        });
    });

    // Theme circles
    document.querySelectorAll('.theme-circle').forEach(circle => {
        circle.addEventListener('click', () => {
            settings.theme = circle.dataset.theme;
            applyReaderSettings(settings);
        });
    });

    // Chapter Dropdown change
    document.querySelectorAll('.chapter-select').forEach(select => {
        select.addEventListener('change', (e) => {
            if (e.target.value) {
                window.location.href = e.target.value;
            }
        });
    });

    // Hotkey navigation: Arrow Left -> Prev, Arrow Right -> Next
    document.addEventListener('keydown', (e) => {
        if (['input', 'textarea', 'select'].includes(e.target.tagName.toLowerCase())) return;

        if (e.key === 'ArrowLeft') {
            const prevBtn = document.getElementById('prev-chapter-btn');
            if (prevBtn && prevBtn.href) {
                window.location.href = prevBtn.href;
            }
        } else if (e.key === 'ArrowRight') {
            const nextBtn = document.getElementById('next-chapter-btn');
            if (nextBtn && nextBtn.href) {
                window.location.href = nextBtn.href;
            }
        }
    });

    // Reading progress bar & scroll position restore / auto-save
    initScrollAndProgressTracking();
}

function loadReaderSettings() {
    const defaults = {
        fontSize: 18,
        lineHeight: 1.8,
        fontFamily: 'font-sans-serif',
        width: 'reader-width-medium',
        theme: 'theme-reader-dark'
    };

    try {
        const saved = JSON.parse(localStorage.getItem('reader_settings'));
        return { ...defaults, ...(saved || {}) };
    } catch (e) {
        return defaults;
    }
}

function applyReaderSettings(settings) {
    const readerContainer = document.querySelector('.reader-container');
    const readerContent = document.querySelector('.reader-content');
    const body = document.body;

    if (!readerContainer || !readerContent) return;

    // Apply font size & line height
    readerContent.style.fontSize = `${settings.fontSize}px`;
    readerContent.style.lineHeight = settings.lineHeight;

    const fontSizeDisplay = document.getElementById('display-font-size');
    if (fontSizeDisplay) fontSizeDisplay.innerText = `${settings.fontSize}px`;

    const lineDisplay = document.getElementById('display-line-height');
    if (lineDisplay) lineDisplay.innerText = `${settings.lineHeight}`;

    // Apply font family
    readerContent.className = `reader-content ${settings.fontFamily}`;
    const fontSelect = document.getElementById('reader-font-select');
    if (fontSelect) fontSelect.value = settings.fontFamily;

    // Apply width
    readerContainer.className = `reader-container ${settings.width}`;
    document.querySelectorAll('.btn-width-select').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.width === settings.width);
    });

    // Apply theme
    body.className = `reader-page ${settings.theme}`;
    document.querySelectorAll('.theme-circle').forEach(circle => {
        circle.classList.toggle('active', circle.dataset.theme === settings.theme);
    });

    // Save
    localStorage.setItem('reader_settings', JSON.stringify(settings));
}

function initScrollAndProgressTracking() {
    const progressBar = document.getElementById('reading-progress-bar');
    const storyId = window.READER_DATA?.storyId;
    const chapterId = window.READER_DATA?.chapterId;
    const storageKey = `progress_story_${storyId}_chapter_${chapterId}`;

    // Restore saved scroll position
    const savedPos = localStorage.getItem(storageKey);
    if (savedPos && parseInt(savedPos, 10) > 0) {
        window.scrollTo({ top: parseInt(savedPos, 10), behavior: 'smooth' });
    }

    let syncTimer = null;

    window.addEventListener('scroll', () => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const progress = scrollHeight > 0 ? Math.min(100, Math.round((scrollTop / scrollHeight) * 100)) : 0;

        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }

        // Save scroll position to local storage
        localStorage.setItem(storageKey, Math.round(scrollTop).toString());

        // Debounce sync with server if logged in
        if (window.IS_LOGGED_IN && storyId && chapterId) {
            clearTimeout(syncTimer);
            syncTimer = setTimeout(() => {
                API.post('/user/history', {
                    story_id: storyId,
                    chapter_id: chapterId,
                    progress: progress
                }).catch(() => {});
            }, 1500);
        }
    });
}
