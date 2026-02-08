/**
 * ── Brixo Navbar v3 — Interactions ──
 *
 * Responsibilities:
 *   1. Hamburger ↔ mobile drawer toggle
 *   2. User dropdown toggle
 *   3. Click-outside close
 *   4. Escape key close
 *   5. Overlay scroll → .bn--scrolled class
 *   6. Close mobile drawer on link click
 *
 * No external dependencies.
 */

(function () {
    'use strict';

    /* ── DOM refs ── */
    const nav      = document.getElementById('brixoNav');
    const burger   = document.getElementById('bnBurger');
    const mobile   = document.getElementById('bnMobile');
    const userWrap = document.getElementById('bnUser');
    const userBtn  = document.getElementById('bnUserToggle');

    if (!nav) return;

    /* ────────────────────────────────────
       1. Hamburger / Mobile drawer
    ──────────────────────────────────── */
    if (burger && mobile) {
        burger.addEventListener('click', function () {
            const isOpen = burger.classList.toggle('open');
            mobile.classList.toggle('open', isOpen);
            burger.setAttribute('aria-expanded', isOpen);
            mobile.setAttribute('aria-hidden', !isOpen);
        });

        /* Close drawer when any link inside is clicked */
        mobile.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                closeMobile();
            });
        });
    }

    function closeMobile() {
        if (!burger || !mobile) return;
        burger.classList.remove('open');
        mobile.classList.remove('open');
        burger.setAttribute('aria-expanded', 'false');
        mobile.setAttribute('aria-hidden', 'true');
    }

    /* ────────────────────────────────────
       2. User dropdown
    ──────────────────────────────────── */
    if (userBtn && userWrap) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = userWrap.classList.toggle('open');
            userBtn.setAttribute('aria-expanded', isOpen);
        });
    }

    function closeDropdown() {
        if (!userWrap || !userBtn) return;
        userWrap.classList.remove('open');
        userBtn.setAttribute('aria-expanded', 'false');
    }

    /* ────────────────────────────────────
       3. Click-outside close
    ──────────────────────────────────── */
    document.addEventListener('click', function (e) {
        /* Dropdown */
        if (userWrap && !userWrap.contains(e.target)) {
            closeDropdown();
        }
        /* Mobile drawer */
        if (mobile && mobile.classList.contains('open') && !mobile.contains(e.target) && !burger.contains(e.target)) {
            closeMobile();
        }
    });

    /* ────────────────────────────────────
       4. Escape key
    ──────────────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeDropdown();
            closeMobile();
        }
    });

    /* ────────────────────────────────────
       5. Overlay scroll → bn--scrolled
    ──────────────────────────────────── */
    if (nav.classList.contains('bn--overlay')) {
        var SCROLL_THRESHOLD = 30;
        var ticking = false;

        function onScroll() {
            if (window.scrollY > SCROLL_THRESHOLD) {
                nav.classList.add('bn--scrolled');
            } else {
                nav.classList.remove('bn--scrolled');
            }
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(onScroll);
                ticking = true;
            }
        }, { passive: true });

        /* Run once on load in case page is already scrolled */
        onScroll();
    }

})();
