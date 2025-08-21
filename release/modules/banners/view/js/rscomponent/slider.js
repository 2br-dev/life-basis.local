/**
 * Инициализирует работу баннерных слайдеров
 * Зависит от swiper.js
 */
new class Slider extends RsJsCore.classes.component {
    onDocumentReady() {
            document.querySelectorAll('.swiper-banner').forEach((sliderElement) => {
                const delay = +(sliderElement.dataset && sliderElement.dataset.autoplayDelay);
                new Swiper(sliderElement, {
                    slidesPerView: 1,
                    spaceBetween: 16,
                    autoplay: (delay > 0 ? { delay: delay * 1000 } : false),
                    pagination: {
                        el: sliderElement.querySelector('.swiper-pagination'),
                        dynamicBullets: true,
                        clickable: true,
                    },
                    preloadImages: true,
                    lazy: true,
                    navigation: {
                        nextEl: sliderElement.querySelector('.swiper-button-next'),
                        prevEl: sliderElement.querySelector('.swiper-button-prev'),
                    },
                });
            });
        }
};