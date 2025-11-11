/**
 * File: public/assets/js/app.js
 * Main JS for theme toggling, sidebar collapse, and modal confirmations.
 * (ملف JS الرئيسي لتبديل المظهر، طي الشريط الجانبي، ونوافذ التأكيد)
 */

document.addEventListener("DOMContentLoaded", function() {

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
    // (This example relies on Bootstrap 5 JS (data-bs-toggle="offcanvas")
    // (هذا المثال يعتمد على Bootstrap 5 JS لتشغيل القائمة الجانبية للموبايل)
    // If _sidebar.php uses data-bs-toggle="offcanvas" and an ID, Bootstrap handles this.
    // If using a custom toggle, we'd add logic for the hamburger menu here.
    
    
    // --- 4. Generic Confirmation Modal (Bootstrap 5) ---
    // (يعالج نافذة التأكيد العامة)
    const confirmModalEl = document.getElementById('confirmModal'); // Needs this ID in _confirm_modal.php
    if (confirmModalEl) {
        const confirmModalForm = confirmModalEl.querySelector('#confirmModalForm');
        const confirmModalBody = confirmModalEl.querySelector('.modal-body-text');
        
        // Listen for the modal 'show' event
        confirmModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            
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
        });
    }
    
    // --- 5. Initialize Bootstrap Tooltips (Optional) ---
    // (لتفعيل الـ Tooltips، مفيدة للأيقونات في الشريط الجانبي المطوي)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

});