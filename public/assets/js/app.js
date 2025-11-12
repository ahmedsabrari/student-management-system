/**
 * File: public/assets/js/app.js
 * Main JS for theme toggling, sidebar collapse, modal confirmations,
 * form spinners, and password utilities.
 * (ملف JS الرئيسي لتبديل المظهر، طي الشريط الجانبي، نوافذ التأكيد،
 * مؤشرات تحميل النماذج، وأدوات كلمة المرور)
 */

document.addEventListener("DOMContentLoaded", function () {
    'use strict';

    const THEME_KEY = 'sms_theme';
    const SIDEBAR_KEY = 'sms_sidebar_collapsed';
    const htmlEl = document.documentElement;
    const appLayout = document.getElementById('app');

    // --- 1. Theme Toggle (Light/Dark Mode) ---
    const themeToggleBtn = document.getElementById('themeToggle'); // Needs this ID in _navbar.php

    // Function to apply the theme
    function applyTheme(theme) {
        if (theme === 'dark') {
            htmlEl.dataset.theme = 'dark';
        } else {
            htmlEl.dataset.theme = 'light';
        }
    }

    // Load saved theme from localStorage or system preference
    function loadTheme() {
        const savedTheme = localStorage.getItem(THEME_KEY);
        if (savedTheme) {
            applyTheme(savedTheme);
        } else {
            // Fallback to system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(prefersDark ? 'dark' : 'light');
        }
    }

    // Attach click event for theme toggle button
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlEl.dataset.theme || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            htmlEl.dataset.theme = newTheme;
            localStorage.setItem(THEME_KEY, newTheme);
        });
    }

    // Load theme on initial page load
    loadTheme();


    // --- 2. Sidebar Collapse (Desktop) ---
    const sidebarToggleBtn = document.getElementById('sidebarToggle'); // Needs this ID in _sidebar.php

    // Load sidebar state
    function loadSidebarState() {
        const isCollapsed = localStorage.getItem(SIDEBAR_KEY) === 'true';
        if (appLayout) {
            // Add class to the main layout wrapper
            appLayout.classList.toggle('sidebar-collapsed', isCollapsed);
        }
    }

    // Attach click event for sidebar toggle
    if (sidebarToggleBtn && appLayout) {
        sidebarToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isCollapsed = appLayout.classList.toggle('sidebar-collapsed');
            localStorage.setItem(SIDEBAR_KEY, isCollapsed);
        });
    }

    // Load sidebar state on initial page load
    loadSidebarState();

    // --- 3. Mobile Sidebar (Off-canvas) ---
    // (This relies on Bootstrap 5 JS (data-bs-toggle="offcanvas"))
    // (هذا يعتمد على Bootstrap 5 JS)
    // (No custom JS needed here if using Bootstrap attributes correctly)

    // --- 4. Generic Confirmation Modal (Bootstrap 5) ---
    // (يعالج نافذة التأكيد العامة)
    const confirmModalEl = document.getElementById('confirmModal'); // Needs this ID in _confirm_modal.php
    if (confirmModalEl) {
        const confirmModalForm = confirmModalEl.querySelector('#confirmModalForm');
        const confirmModalBody = confirmModalEl.querySelector('.modal-body-text');

        // Listen for the modal 'show' event
        confirmModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            if (button) {
                // Get action URL from data-action attribute
                const actionUrl = button.dataset.action;
                // Get message from data-message attribute (or use default)
                const message = button.dataset.message || 'Are you sure you want to proceed with this action?';

                if (confirmModalForm) {
                    confirmModalForm.setAttribute('action', actionUrl);
                }
                if (confirmModalBody) {
                    confirmModalBody.textContent = message;
                }
            }
        });
    }

    // --- 5. Initialize Bootstrap Tooltips (Optional) ---
    // (لتفعيل الـ Tooltips، مفيدة للأيقونات في الشريط الجانبي المطوي)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        var tooltip = new bootstrap.Tooltip(tooltipTriggerEl);
        // Hide tooltips when sidebar is open (expanded)
        if (sidebarToggleBtn && appLayout) {
             sidebarToggleBtn.addEventListener('click', () => {
                 const isCollapsed = appLayout.classList.contains('sidebar-collapsed');
                 if(!isCollapsed) {
                     tooltip.hide();
                 }
             });
        }
        return tooltip;
    });

    // --- 6. Form Submit Loading State ---
    // (إضافة حالة التحميل عند إرسال النماذج)
    const formsToTrack = document.querySelectorAll('form[method="POST"]');

    formsToTrack.forEach(form => {
        form.addEventListener('submit', function (e) {
            // Check client-side validation first (if form has 'needs-validation')
            // (التحقق من صحة الإدخال من جانب العميل أولاً)
            if (form.classList.contains('needs-validation') && !form.checkValidity()) {
                // If form is invalid, don't show spinner, let Bootstrap handle errors
                // (إذا كان النموذج غير صالح، لا تظهر المؤشر، دع Bootstrap يعالج الأخطاء)
                form.classList.add('was-validated'); // Show validation errors
                e.preventDefault(); // Stop submission
                e.stopPropagation();
                return;
            }

            // Find the submit button(s) within this specific form
            const submitButtons = form.querySelectorAll('button[type="submit"]');

            submitButtons.forEach(button => {
                // Disable the button
                button.disabled = true;

                // Store original text
                const originalText = button.innerHTML;
                button.dataset.originalText = originalText;

                // Add spinner (Bootstrap 5 spinner)
                button.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="visually-hidden">Loading...</span>
                `;
            });
        });
    });

    // --- 7. Reusable Password Toggle Function ---
    // (دالة قابلة لإعادة الاستخدام لتبديل كلمة المرور)
    function initPasswordToggle(toggleBtnId, inputId) {
        const togglePasswordBtn = document.getElementById(toggleBtnId);
        const passwordInput = document.getElementById(inputId);

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const icon = this.querySelector('i'); // Get the icon inside the button
                if (!icon) return; // Safety check

                // Toggle the input type
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle the icon
                if (type === 'password') {
                    icon.classList.remove('bi-eye-fill');
                    icon.classList.add('bi-eye-slash');
                    this.setAttribute('aria-label', 'Show password');
                } else {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye-fill');
                    this.setAttribute('aria-label', 'Hide password');
                }
            });
        }
    }

    // --- 8. Initialize Password Toggles ---
    // (تفعيل الأزرار لصفحتي الدخول والتسجيل)
    initPasswordToggle('togglePassword', 'password'); // For Login & Register
    initPasswordToggle('togglePasswordConfirm', 'password_confirmation'); // For Register

    // --- 9. Password Strength Meter ---
    // (مؤشر قوة كلمة المرور)
    const passwordInputForStrength = document.getElementById('password'); // The main password field
    const strengthBar = document.getElementById('password-strength-meter')?.querySelector('.progress-bar');
    const strengthText = document.getElementById('password-strength-text');

    if (passwordInputForStrength && strengthBar && strengthText) {
        passwordInputForStrength.addEventListener('input', () => {
            const pass = passwordInputForStrength.value;
            let score = 0;
            let text = '';
            let barClass = '';

            if (pass.length === 0) {
                score = -1; // Special case for empty
            } else {
                if (pass.length >= 8) score++;      // Length
                if (pass.match(/[a-z]/)) score++;   // Lowercase
                if (pass.match(/[A-Z]/)) score++;   // Uppercase
                if (pass.match(/[0-9]/)) score++;   // Numbers
                if (pass.match(/[^a-zA-Z0-9]/)) score++; // Special chars
            }

            // Remove old classes
            strengthBar.classList.remove('strength-very-weak', 'strength-weak', 'strength-medium', 'strength-strong', 'bg-danger', 'bg-warning', 'bg-info', 'bg-success');
            strengthText.classList.remove('strength-very-weak', 'strength-weak', 'strength-medium', 'strength-strong', 'text-danger', 'text-warning', 'text-info', 'text-success');

            switch (score) {
                case -1: // Empty
                    text = '';
                    barClass = '';
                    strengthBar.style.width = '0%';
                    break;
                case 1:
                case 2:
                    text = 'Weak';
                    barClass = 'strength-very-weak';
                    strengthBar.style.width = '25%';
                    strengthBar.classList.add('bg-danger'); // Use Bootstrap class
                    strengthText.classList.add('text-danger');
                    break;
                case 3:
                    text = 'Medium';
                    barClass = 'strength-weak';
                    strengthBar.style.width = '50%';
                    strengthBar.classList.add('bg-warning'); // Use Bootstrap class
                    strengthText.classList.add('text-warning');
                    break;
                case 4:
                    text = 'Good';
                    barClass = 'strength-medium';
                    strengthBar.style.width = '75%';
                    strengthBar.classList.add('bg-info'); // Use Bootstrap class
                    strengthText.classList.add('text-info');
                    break;
                case 5:
                    text = 'Strong';
                    barClass = 'strength-strong';
                    strengthBar.style.width = '100%';
                    strengthBar.classList.add('bg-success'); // Use Bootstrap class
                    strengthText.classList.add('text-success');
                    break;
                default: // Score 0
                    text = 'Very Weak';
                    barClass = 'strength-very-weak';
                    strengthBar.style.width = '10%';
                    strengthBar.classList.add('bg-danger');
                    strengthText.classList.add('text-danger');
            }

            if (score >= 0) {
                // strengthBar.classList.add(barClass); // CSS classes are set above
                strengthText.textContent = text;
                // strengthText.classList.add(barClass);
            } else {
                strengthText.textContent = '';
                strengthBar.style.width = '0%';
            }
        });
    }

}); // End of DOMContentLoaded