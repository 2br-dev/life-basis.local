/**
 * Инициализирует работу отзывов
 */
new class Comments extends RsJsCore.classes.component {

    constructor(settings) {
        super();

        let defaults = {
            context: '.rs-comments',
            stars: '.rs-stars li',
            rate: '.rs-rate',
            inputRate: '.inp_rate',
            rateDescr: '.rs-rate-descr',
            ratingFilter: '.rs-comment-filter',
            activeClass: 'active',
            loadingClass: 'rs-in-loading',
            rateText: [
                lang.t('нет оценки'),
                lang.t('ужасно'),
                lang.t('плохо'),
                lang.t('нормально'),
                lang.t('хорошо'),
                lang.t('отлично')
            ]
        };

        this.settings = {...defaults, ...this.getExtendsSettings(), ...settings};
    }

    /**
     * Инициаизирует выбор оценки для отзыва
     *
     * @param event
     */
    initStarsSelect(event) {
        let context = event.target.querySelector(this.settings.context);

        if (context) {
            this.context = context;
            this.context.querySelectorAll(this.settings.stars).forEach((element) => {
                element.addEventListener('mouseover', event => this.overStar(event));
                element.addEventListener('mouseout', event => this.restoreStars(event));
                element.addEventListener('click', event => this.setMark(event));
            });

            this.restoreStars();
        }
    }

    /**
     * Возвращает порядковый номер элемента среди братьев в DOM
     *
     * @param element
     * @returns {number}
     */
    getNodeIndex(element) {
        return [...element.parentNode.children].indexOf(element);
    }

    /**
     * Обработчик наведения мыши на звезду
     *
     * @param event
     */
    overStar(event) {
        this.selectStars(this.getNodeIndex(event.target)+1);
    }

    /**
     * Подсвечивает нужную оценку на звездах
     *
     * @param index
     */
    selectStars(index) {
        let allStars = this.context.querySelectorAll(this.settings.stars);
        allStars.forEach(element => {
            element.classList.remove(this.settings.activeClass);
        });

        for(let i=0; i <= index - 1; i++) {
            allStars[i].classList.add(this.settings.activeClass);
        }

        let description = this.context.querySelector(this.settings.rateDescr);
        description && (description.innerText = this.settings.rateText[index]);
    }

    /**
     * Восстанавливает визуальное отображение оценки к предыдущему закрепленному значению
     */
    restoreStars() {
        let inputRate = this.context.querySelector(this.settings.inputRate);
        if (inputRate) {
            this.selectStars(inputRate.value);
        }
    }

    /**
     * Фиксирует новую оценку
     *
     * @param event
     */
    setMark(event) {
        this.context.querySelector(this.settings.inputRate).value = this.getNodeIndex(event.target) + 1;
    }

    /**
     * Инициализирует фильтр по оценкам.
     * Клик по оценке будет оставлять комментарии только с выбранной оценкой.
     *
     * @param event
     */
    initRatingFilter(event) {
        let context = event.target.querySelector(this.settings.context);
        if (context) {
            let parent = context.parentNode;
            let baseUrl = context.dataset.refreshUrl;
            context
                .querySelectorAll(this.settings.ratingFilter).forEach(it => {
                it.addEventListener('click', (event) => {
                    let filterValue = event.target.value;
                    let url = new URL(baseUrl);
                    url.searchParams.append('rating_filter', filterValue);

                    context.classList.add(this.settings.loadingClass);
                    RsJsCore.utils.fetchJSON(url).then((response) => {
                        context.insertAdjacentHTML('afterend', response.html);
                        context.remove();
                        parent.dispatchEvent(new CustomEvent('new-content', {bubbles: true}));
                    });
                });
            });
        }
    }

    /**
     * Инициализирует открытие окна с просмотром фотографий
     *
     * @param event
     */
    initGLightbox(event) {
        if (typeof GLightbox !== 'undefined') {
            this.lightbox = new GLightbox({
                selector: '#photo-list a',
            });
        }
    }

    /**
     * Выполняет процесс инициализации свайперов для видимого элемента
     *
     * @param parent_swiper
     */
    initSwipers(parent_swiper) {
        parent_swiper.querySelectorAll('.photo-container.swiper-container').forEach(element => {
            const swiper = new Swiper(element, {
                // Параметры Swiper
                slidesPerView: 2,
                spaceBetween: 1,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    900: {
                        slidesPerView: 4,
                    },
                    767: {
                        slidesPerView: 3,
                    },
                    700: {
                        slidesPerView: 5,
                    },
                    550: {
                        slidesPerView: 4,
                    },
                    400: {
                        slidesPerView: 3,
                    }
                }
            });
        });
    }

    /**
     * Инициализирует блоки Swiper для прокручивания фотографий комментариев
     *
     * @param event
     */
    initPhotoSwipers(event) {
        let swiper = document.querySelector('.photo-container.swiper-container')
        if (swiper) {
            let parent_swiper = swiper.closest('.tab-pane');
            if (window.getComputedStyle(parent_swiper).display !== 'none' && window.getComputedStyle(parent_swiper).visibility !== 'hidden') {
                this.initSwipers(parent_swiper);
            } else {
                let tab_comment = document.querySelector('[data-tab-id="comments"]');
                let nav_item_comment = tab_comment.closest('.nav-item');
                let self = this;
                nav_item_comment.addEventListener('shown.bs.tab', function () {
                    self.initSwipers(parent_swiper);
                });
            }
        }
    }

    /**
     * Обработчик события нового контента на странице
     *
     * @param event
     */
    onContentReady(event) {
        this.initStarsSelect(event);
        this.initRatingFilter(event);
        this.initPhotoSwipers(event);
        this.initGLightbox(event);
        this.plugins.ajaxPaginator.init('.rs-ajax-paginator');
    }
};