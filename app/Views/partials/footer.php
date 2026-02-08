<?php
/**
 * Brixo Unified Footer Partial
 * 
 * Includes: footer container, brixoUser globals, modals, navbar.js companion.
 * NOTE: The unified navbar (partials/navbar.php) must be included at the TOP
 * of each view's <body>, NOT here. This partial goes at the BOTTOM.
 */
?>
<div id="brixo-footer-container"></div>
<script src="/js/footer.js"></script>

<script>
    window.brixoUser = <?= json_encode(session()->get('user') ?? null) ?>;
    window.csrfTokenName = '<?= csrf_token() ?>';
    window.csrfHash = '<?= csrf_hash() ?>';
</script>

<?php
// Only include modals if the navbar partial hasn't already loaded them.
// The unified navbar no longer includes modals, so we always load them here.
?>
<?= view('partials/modals') ?>
<?= view('partials/cookie_consent') ?>
<script src="/js/navbar.js?v=<?= time() ?>"></script>
<script src="/js/password-toggle.js?v=<?= time() ?>"></script>
<script src="/js/cookie-consent.js?v=<?= time() ?>"></script>
<script src="/js/brixo-analytics.js?v=<?= time() ?>"></script>

<style>
    .hover-text-white:hover {
        color: #fff !important;
        transition: color 0.3s ease;
    }
</style>