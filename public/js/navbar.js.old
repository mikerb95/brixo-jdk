/**
 * Brixo Navbar — JS companion (v2)
 * 
 * The navbar is now rendered server-side by partials/navbar.php.
 * This file only handles minor client-side enhancements:
 *  - Close mobile menu on link click
 *  - Close dropdown on outside click
 */
document.addEventListener('DOMContentLoaded', function () {
    const nav = document.getElementById('brixoUnifiedNav');
    if (!nav) return;

    // Close mobile collapse when a link is clicked
    const collapse = nav.querySelector('.navbar-collapse');
    if (collapse) {
        nav.querySelectorAll('.brixo-nav__links a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992 && collapse.classList.contains('show')) {
                    var bsCollapse = bootstrap.Collapse.getInstance(collapse);
                    if (bsCollapse) bsCollapse.hide();
                }
            });
        });
    }

    // Dropdown: toggle .show on click (desktop already handled by CSS :hover)
    var dropdown = nav.querySelector('.brixo-nav__dropdown');
    if (dropdown) {
        var toggle = dropdown.querySelector('.brixo-nav__dropdown-toggle');
        if (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                dropdown.classList.toggle('show');
            });
        }
        // Close dropdown on click outside
        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }
});
