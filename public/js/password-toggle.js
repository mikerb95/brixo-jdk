/**
 * Password Toggle Utility
 * Auto-wraps all password inputs and adds eye icon toggle
 */
(function() {
    'use strict';

    function initPasswordToggles() {
        // Find all password inputs that aren't already wrapped
        const passwordInputs = document.querySelectorAll('input[type="password"]:not(.password-toggle-initialized)');
        
        passwordInputs.forEach(input => {
            // Mark as initialized
            input.classList.add('password-toggle-initialized');
            
            // Create wrapper if not already wrapped
            if (!input.parentElement.classList.contains('password-field-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'password-field-wrapper';
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);
            }
            
            const wrapper = input.parentElement;
            
            // Create toggle button
            const toggleBtn = document.createElement('button');
            toggleBtn.type = 'button';
            toggleBtn.className = 'password-toggle-btn';
            toggleBtn.innerHTML = '<i class="far fa-eye"></i>';
            toggleBtn.setAttribute('aria-label', 'Mostrar contraseña');
            toggleBtn.setAttribute('tabindex', '-1');
            
            // Add toggle functionality
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    this.setAttribute('aria-label', 'Ocultar contraseña');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.setAttribute('aria-label', 'Mostrar contraseña');
                }
            });
            
            // Append button to wrapper
            wrapper.appendChild(toggleBtn);
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPasswordToggles);
    } else {
        initPasswordToggles();
    }

    // Re-initialize when modals are shown (for dynamically loaded content)
    document.addEventListener('shown.bs.modal', function() {
        setTimeout(initPasswordToggles, 50);
    });

    // Export function for manual initialization if needed
    window.initPasswordToggles = initPasswordToggles;
})();
