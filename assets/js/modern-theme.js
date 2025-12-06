/**
 * Modern Theme for osTicket
 * Vanilla JavaScript functionality
 *
 * Features:
 * - Dark/Light theme toggle with system preference detection
 * - Smooth animations and transitions
 * - Enhanced form interactions
 * - Accessibility improvements
 */

(function() {
    'use strict';

    // Theme namespace
    window.ModernTheme = window.ModernTheme || {};

    // Configuration defaults
    const defaultConfig = {
        themeMode: 'auto',
        animations: true,
        glassmorphism: true
    };

    let config = { ...defaultConfig };
    let currentTheme = 'light';

    /**
     * Initialize the Modern Theme
     * @param {Object} userConfig - Configuration from PHP plugin
     */
    ModernTheme.init = function(userConfig) {
        config = { ...defaultConfig, ...userConfig };

        // Apply modern-theme class to body
        document.body.classList.add('modern-theme');

        // Initialize theme
        initTheme();

        // Initialize animations
        if (config.animations) {
            initAnimations();
        }

        // Initialize glassmorphism
        if (config.glassmorphism) {
            initGlassmorphism();
        }

        // Initialize UI enhancements
        initFormEnhancements();
        initTableEnhancements();
        initAccessibility();
        initThemeToggle();

        // Emit ready event
        document.dispatchEvent(new CustomEvent('moderntheme:ready', { detail: config }));

        console.log('Modern Theme initialized', config);
    };

    /**
     * Initialize theme (dark/light/auto)
     */
    function initTheme() {
        // Check for saved preference
        const savedTheme = localStorage.getItem('mt-theme');

        if (config.themeMode === 'auto') {
            if (savedTheme && ['light', 'dark'].includes(savedTheme)) {
                currentTheme = savedTheme;
            } else {
                // Detect system preference
                currentTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('mt-theme')) {
                    setTheme(e.matches ? 'dark' : 'light');
                }
            });
        } else {
            currentTheme = config.themeMode;
        }

        setTheme(currentTheme);
    }

    /**
     * Set the current theme
     * @param {string} theme - 'light' or 'dark'
     */
    function setTheme(theme) {
        currentTheme = theme;
        document.documentElement.setAttribute('data-mt-theme', theme);
        document.body.setAttribute('data-mt-theme', theme);

        // Update theme toggle button if exists
        updateThemeToggleIcon();

        // Emit theme change event
        document.dispatchEvent(new CustomEvent('moderntheme:themechange', { detail: { theme } }));
    }

    /**
     * Toggle between light and dark theme
     */
    ModernTheme.toggleTheme = function() {
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        setTheme(newTheme);
        localStorage.setItem('mt-theme', newTheme);
    };

    /**
     * Get current theme
     * @returns {string}
     */
    ModernTheme.getTheme = function() {
        return currentTheme;
    };

    /**
     * Initialize theme toggle button
     */
    function initThemeToggle() {
        // Only create toggle if theme mode is auto (user can switch)
        if (config.themeMode !== 'auto') {
            return;
        }

        // Check if toggle already exists
        if (document.querySelector('.mt-theme-toggle')) {
            return;
        }

        // Create toggle button
        const toggle = document.createElement('button');
        toggle.className = 'mt-theme-toggle';
        toggle.setAttribute('aria-label', 'Toggle dark mode');
        toggle.setAttribute('title', 'Toggle dark mode');
        toggle.innerHTML = `
            <i class="bi bi-sun-fill icon-sun"></i>
            <i class="bi bi-moon-fill icon-moon"></i>
        `;

        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            ModernTheme.toggleTheme();

            // Add a little bounce animation
            this.style.transform = 'scale(0.9)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });

        document.body.appendChild(toggle);
        updateThemeToggleIcon();
    }

    /**
     * Update theme toggle icon visibility
     */
    function updateThemeToggleIcon() {
        const toggle = document.querySelector('.mt-theme-toggle');
        if (!toggle) return;

        const sunIcon = toggle.querySelector('.icon-sun');
        const moonIcon = toggle.querySelector('.icon-moon');

        if (sunIcon && moonIcon) {
            if (currentTheme === 'dark') {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            } else {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            }
        }
    }

    /**
     * Initialize animations
     */
    function initAnimations() {
        document.documentElement.setAttribute('data-mt-animate', 'true');

        // Intersection Observer for scroll-triggered animations
        if ('IntersectionObserver' in window) {
            const animateOnScroll = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('mt-animate-in');
                        animateOnScroll.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe cards and panels
            document.querySelectorAll('.card, .panel, .widget, .thread-entry').forEach(el => {
                el.classList.add('mt-animate-target');
                animateOnScroll.observe(el);
            });
        }
    }

    /**
     * Initialize glassmorphism effects
     */
    function initGlassmorphism() {
        document.documentElement.setAttribute('data-mt-glass', 'true');

        // Add glass class to appropriate elements
        document.querySelectorAll('.modal, .dialog, .dropdown-menu').forEach(el => {
            el.classList.add('mt-glass');
        });
    }

    /**
     * Initialize form enhancements
     */
    function initFormEnhancements() {
        // Floating label effect
        document.querySelectorAll('input, textarea, select').forEach(input => {
            // Add focus/blur handlers for visual feedback
            input.addEventListener('focus', function() {
                this.parentElement?.classList.add('mt-input-focused');
            });

            input.addEventListener('blur', function() {
                this.parentElement?.classList.remove('mt-input-focused');
                if (this.value) {
                    this.parentElement?.classList.add('mt-input-filled');
                } else {
                    this.parentElement?.classList.remove('mt-input-filled');
                }
            });

            // Initial state
            if (input.value) {
                input.parentElement?.classList.add('mt-input-filled');
            }
        });

        // Enhanced form validation feedback
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const invalidInputs = this.querySelectorAll(':invalid');
                if (invalidInputs.length > 0) {
                    invalidInputs.forEach(input => {
                        input.classList.add('mt-shake');
                        setTimeout(() => input.classList.remove('mt-shake'), 500);
                    });
                }
            });
        });

        // Character counter for textareas
        document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
            const maxLength = textarea.getAttribute('maxlength');
            const counter = document.createElement('div');
            counter.className = 'mt-char-counter';
            counter.style.cssText = 'text-align: right; font-size: 0.75rem; color: var(--mt-text-muted); margin-top: 0.25rem;';

            const updateCounter = () => {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = `${textarea.value.length} / ${maxLength}`;
                counter.style.color = remaining < 20 ? 'var(--mt-warning)' : 'var(--mt-text-muted)';
                if (remaining < 0) {
                    counter.style.color = 'var(--mt-danger)';
                }
            };

            textarea.addEventListener('input', updateCounter);
            textarea.parentElement?.appendChild(counter);
            updateCounter();
        });
    }

    /**
     * Initialize table enhancements
     */
    function initTableEnhancements() {
        document.querySelectorAll('table').forEach(table => {
            // Make tables responsive
            if (!table.parentElement?.classList.contains('table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                wrapper.style.overflowX = 'auto';
                table.parentElement?.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }

            // Add hover effect class
            table.classList.add('table-hover');

            // Sortable columns (visual indicator only - actual sorting needs backend)
            table.querySelectorAll('th[data-sort]').forEach(th => {
                th.style.cursor = 'pointer';
                th.setAttribute('role', 'button');

                const icon = document.createElement('i');
                icon.className = 'bi bi-arrow-down-up ms-1';
                icon.style.opacity = '0.5';
                th.appendChild(icon);
            });
        });

        // Clickable rows
        document.querySelectorAll('tr[data-href], .ticket-row').forEach(row => {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function(e) {
                // Don't navigate if clicking a link or button
                if (e.target.closest('a, button, input')) return;

                const href = this.dataset.href || this.querySelector('a')?.href;
                if (href) {
                    window.location.href = href;
                }
            });
        });
    }

    /**
     * Initialize accessibility improvements
     */
    function initAccessibility() {
        // Skip link
        if (!document.querySelector('.mt-skip-link')) {
            const skipLink = document.createElement('a');
            skipLink.className = 'mt-skip-link';
            skipLink.href = '#content';
            skipLink.textContent = 'Skip to main content';
            skipLink.style.cssText = `
                position: absolute;
                top: -40px;
                left: 0;
                background: var(--mt-primary);
                color: white;
                padding: 8px 16px;
                z-index: 10000;
                transition: top 0.2s;
            `;
            skipLink.addEventListener('focus', () => skipLink.style.top = '0');
            skipLink.addEventListener('blur', () => skipLink.style.top = '-40px');
            document.body.insertBefore(skipLink, document.body.firstChild);
        }

        // Focus visible outline
        const style = document.createElement('style');
        style.textContent = `
            .modern-theme *:focus-visible {
                outline: 2px solid var(--mt-primary) !important;
                outline-offset: 2px !important;
            }
            .modern-theme .mt-shake {
                animation: mt-shake 0.5s ease-in-out;
            }
            @keyframes mt-shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
            .modern-theme .mt-animate-target {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.5s ease, transform 0.5s ease;
            }
            .modern-theme .mt-animate-in {
                opacity: 1;
                transform: translateY(0);
            }
        `;
        document.head.appendChild(style);

        // Improve focus management for modals
        document.addEventListener('click', function(e) {
            const modal = e.target.closest('.modal, .dialog');
            if (modal) {
                const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusable.length) {
                    focusable[0].focus();
                }
            }
        });

        // Announce dynamic content changes
        if (!document.querySelector('.mt-live-region')) {
            const liveRegion = document.createElement('div');
            liveRegion.className = 'mt-live-region';
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            liveRegion.style.cssText = 'position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden;';
            document.body.appendChild(liveRegion);
        }
    }

    /**
     * Announce message to screen readers
     * @param {string} message
     */
    ModernTheme.announce = function(message) {
        const liveRegion = document.querySelector('.mt-live-region');
        if (liveRegion) {
            liveRegion.textContent = message;
        }
    };

    /**
     * Show a toast notification
     * @param {string} message
     * @param {string} type - 'success', 'error', 'warning', 'info'
     * @param {number} duration - Duration in ms
     */
    ModernTheme.toast = function(message, type = 'info', duration = 4000) {
        // Create toast container if not exists
        let container = document.querySelector('.mt-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'mt-toast-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                display: flex;
                flex-direction: column;
                gap: 10px;
            `;
            document.body.appendChild(container);
        }

        // Create toast
        const toast = document.createElement('div');
        toast.className = `mt-toast mt-toast-${type}`;

        const icons = {
            success: 'check-circle-fill',
            error: 'x-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };

        const colors = {
            success: 'var(--mt-success)',
            error: 'var(--mt-danger)',
            warning: 'var(--mt-warning)',
            info: 'var(--mt-info)'
        };

        toast.style.cssText = `
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            background: var(--mt-bg-surface);
            border-left: 4px solid ${colors[type]};
            border-radius: var(--mt-border-radius);
            box-shadow: var(--mt-shadow-lg);
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-width: 400px;
        `;

        toast.innerHTML = `
            <i class="bi bi-${icons[type]}" style="color: ${colors[type]}; font-size: 1.25rem;"></i>
            <span style="flex: 1; color: var(--mt-text-primary);">${message}</span>
            <button style="background: none; border: none; color: var(--mt-text-muted); cursor: pointer; padding: 0; font-size: 1.25rem;">
                <i class="bi bi-x"></i>
            </button>
        `;

        const closeBtn = toast.querySelector('button');
        closeBtn.addEventListener('click', () => removeToast(toast));

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
        });

        // Auto remove
        if (duration > 0) {
            setTimeout(() => removeToast(toast), duration);
        }

        // Announce to screen readers
        ModernTheme.announce(message);

        return toast;
    };

    function removeToast(toast) {
        toast.style.transform = 'translateX(120%)';
        setTimeout(() => toast.remove(), 300);
    }

    /**
     * Format date relative to now
     * @param {Date|string} date
     * @returns {string}
     */
    ModernTheme.formatRelativeTime = function(date) {
        const now = new Date();
        const then = new Date(date);
        const diffMs = now - then;
        const diffSecs = Math.floor(diffMs / 1000);
        const diffMins = Math.floor(diffSecs / 60);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffSecs < 60) return 'Just now';
        if (diffMins < 60) return `${diffMins} minute${diffMins !== 1 ? 's' : ''} ago`;
        if (diffHours < 24) return `${diffHours} hour${diffHours !== 1 ? 's' : ''} ago`;
        if (diffDays < 7) return `${diffDays} day${diffDays !== 1 ? 's' : ''} ago`;

        return then.toLocaleDateString();
    };

    /**
     * Copy text to clipboard
     * @param {string} text
     * @returns {Promise<boolean>}
     */
    ModernTheme.copyToClipboard = async function(text) {
        try {
            await navigator.clipboard.writeText(text);
            ModernTheme.toast('Copied to clipboard', 'success', 2000);
            return true;
        } catch (err) {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.cssText = 'position: fixed; left: -9999px;';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                ModernTheme.toast('Copied to clipboard', 'success', 2000);
                return true;
            } catch (e) {
                ModernTheme.toast('Failed to copy', 'error', 2000);
                return false;
            } finally {
                textarea.remove();
            }
        }
    };

    /**
     * Initialize copy buttons for code blocks
     */
    function initCopyButtons() {
        document.querySelectorAll('pre, code, .ticket-number').forEach(el => {
            if (el.querySelector('.mt-copy-btn')) return;

            const btn = document.createElement('button');
            btn.className = 'mt-copy-btn';
            btn.innerHTML = '<i class="bi bi-clipboard"></i>';
            btn.style.cssText = `
                position: absolute;
                top: 8px;
                right: 8px;
                padding: 4px 8px;
                background: var(--mt-bg-muted);
                border: 1px solid var(--mt-border-default);
                border-radius: var(--mt-radius-sm);
                cursor: pointer;
                opacity: 0;
                transition: opacity 0.2s;
            `;

            el.style.position = 'relative';
            el.appendChild(btn);

            el.addEventListener('mouseenter', () => btn.style.opacity = '1');
            el.addEventListener('mouseleave', () => btn.style.opacity = '0');

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const text = el.textContent.replace(btn.textContent, '').trim();
                ModernTheme.copyToClipboard(text);
                btn.innerHTML = '<i class="bi bi-check"></i>';
                setTimeout(() => btn.innerHTML = '<i class="bi bi-clipboard"></i>', 1500);
            });
        });
    }

    // Auto-initialize on DOMContentLoaded if not manually initialized
    document.addEventListener('DOMContentLoaded', function() {
        // Give manual init a chance
        setTimeout(() => {
            if (!document.body.classList.contains('modern-theme')) {
                ModernTheme.init({});
            }
            initCopyButtons();
        }, 100);
    });

    // Re-run enhancements after AJAX content loads (for osTicket's PJAX)
    document.addEventListener('pjax:complete', function() {
        initFormEnhancements();
        initTableEnhancements();
        initCopyButtons();
        if (config.animations) {
            initAnimations();
        }
    });

})();
