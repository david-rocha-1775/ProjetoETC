function inicializarCarrosselLogin() {
    var carousels = document.querySelectorAll('[data-auth-login-carousel]');

    carousels.forEach(function (carousel) {
        var slides = carousel.querySelectorAll('[data-auth-login-slide]');
        if (slides.length < 2) {
            return;
        }

        var activeIndex = 0;
        slides.forEach(function (slide, index) {
            if (slide.classList.contains('is-active')) {
                activeIndex = index;
            }
        });

        window.setInterval(function () {
            slides[activeIndex].classList.remove('is-active');
            activeIndex = (activeIndex + 1) % slides.length;
            slides[activeIndex].classList.add('is-active');
        }, 4500);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarCarrosselLogin);
} else {
    inicializarCarrosselLogin();
}
