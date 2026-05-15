document.addEventListener('DOMContentLoaded', function () {
    // Toggle password visibility
    var toggles = document.querySelectorAll('[data-toggle-password]');
    var iconVisible = 'assets/fonts/material-symbols/visibility.svg';
    var iconHidden = 'assets/fonts/material-symbols/visibility_off.svg';

    toggles.forEach(function (toggleButton) {
        toggleButton.addEventListener('click', function () {
            var targetId = toggleButton.getAttribute('data-toggle-password');
            var targetInput = document.getElementById(targetId);

            if (!targetInput) {
                return;
            }

            var isPassword = targetInput.getAttribute('type') === 'password';
            targetInput.setAttribute('type', isPassword ? 'text' : 'password');

            var icon = toggleButton.querySelector('img');
            if (icon) {
                icon.setAttribute('src', isPassword ? iconHidden : iconVisible);
            }

            var hiddenText = toggleButton.querySelector('.visually-hidden');
            var targetLabel = targetId === 'confirmacao_senha' ? 'confirmação de senha' : 'senha';
            var actionText = isPassword ? 'Ocultar ' + targetLabel : 'Mostrar ' + targetLabel;

            if (hiddenText) {
                hiddenText.textContent = actionText;
            }

            toggleButton.setAttribute('aria-label', actionText);
            toggleButton.setAttribute('title', actionText);
        });
    });

    // Handle Terms of Use link (prevent default navigation)
    var termsLink = document.getElementById('termsLink');
    if (termsLink) {
        termsLink.addEventListener('click', function (e) {
            e.preventDefault();
            // Bootstrap Modal API handles the rest via data-bs-toggle and data-bs-target
        });
    }
});
