/**
 * Инициализирует работу блока со слайдером товаров
 */
new class ProductsSlider extends RsJsCore.classes.component {

    initSwipers() {
        if (document.querySelector('.swiper-products')) {
            document.querySelectorAll('.swiper-products').forEach(element => {
                let isSmall = element.classList.contains('swiper-products_sm');
                const swiper = new Swiper(element, {
                    slidesPerView: 2,
                    spaceBetween: 0,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        1400: {
                            slidesPerView: isSmall ? 4 : 5,
                            spaceBetween: 24
                        },
                        1200: {
                            slidesPerView: isSmall ? 3 : 4,
                            spaceBetween: 24
                        },
                        768: {
                            slidesPerView: isSmall ? 2 : 3,
                            spaceBetween: 24
                        },
                    }
                });
            });
        }
    }

    onDocumentReady() {
        this.initSwipers();
    }
};