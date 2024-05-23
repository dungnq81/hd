import { nanoid } from 'nanoid';
import { random, isEmpty, toString } from 'lodash';
//import device from 'current-device';
import Swiper from 'swiper/bundle';

// Determine device type
// const is_mobile = () => device.mobile();
// const is_tablet = () => device.tablet();
// const is_desktop = () => device.desktop();

// Initialize Swiper instances
const initializeSwiper = (el, options) => {
    const swiper = new Swiper(el, options);

    el.addEventListener('mouseover', () => {
        swiper.autoplay.stop();
    });

    el.addEventListener('mouseout', () => {
        if (options.autoplay) {
            swiper.autoplay.start();
        }
    });

    return swiper;
};

// Generate unique class names
const generateClasses = () => {
    const rand = nanoid(12);
    return {
        swiperClass: 'swiper-' + rand,
        nextClass: 'next-' + rand,
        prevClass: 'prev-' + rand,
        paginationClass: 'pagination-' + rand,
        scrollbarClass: 'scrollbar-' + rand,
    };
};

// Default Swiper options
const getDefaultOptions = () => ({
    grabCursor: !0,
    allowTouchMove: !0,
    threshold: 5,
    hashNavigation: !1,
    mousewheel: !1,
    wrapperClass: 'swiper-wrapper',
    slideClass: 'swiper-slide',
    slideActiveClass: 'swiper-slide-active',
});

const initializeSwipers = () => {
    const swiperElements = [...document.querySelectorAll('.w-swiper')];

    swiperElements.forEach((el) => {
        const classes = generateClasses();
        el.classList.add(classes.swiperClass);

        let controls = el.closest('.swiper-section')?.querySelector('.swiper-controls');
        if (!controls) {
            controls = document.createElement('div');
            controls.classList.add('swiper-controls');
            el.after(controls);
        }

        const swiperWrapper = el.querySelector('.swiper-wrapper');
        let options = JSON.parse(swiperWrapper.dataset.options) || {};

        if (isEmpty(options)) {
            options = {
                autoview: !0,
                autoplay: !0,
                navigation: !0,
            };
        }

        const swiperOptions = { ...getDefaultOptions() };

        if (options.autoview) {
            swiperOptions.slidesPerView = 'auto';
            if (options.gap) {
                swiperOptions.spaceBetween = 20;
                swiperOptions.breakpoints = {
                    640: { spaceBetween: 30 },
                };
            }
        } else {
            swiperOptions.breakpoints = {
                0: options.mobile || {},
                640: options.tablet || {},
                1024: options.desktop || {},
            };
        }

        if (options.observer) {
            swiperOptions.observer = !0;
            swiperOptions.observeParents = !0;
        }

        if (options.effect) {
            swiperOptions.effect = toString(options.effect);
            if (swiperOptions.effect === 'fade') {
                swiperOptions.fadeEffect = { crossFade: !0 };
            }
        }

        if (options.autoheight) swiperOptions.autoHeight = !0;
        if (options.loop) swiperOptions.loop = !0;
        if (options.parallax) swiperOptions.parallax = !0;
        if (options.direction) swiperOptions.direction = toString(options.direction);
        if (options.centered) swiperOptions.centeredSlides = !0;
        if (options.freemode) swiperOptions.freeMode = !0;
        if (options.cssmode) swiperOptions.cssMode = !0;

        swiperOptions.speed = options.speed ? parseInt(options.speed) : random(300, 900);

        if (options.autoplay) {
            swiperOptions.autoplay = {
                disableOnInteraction: !1,
                delay: options.delay ? parseInt(options.delay) : random(3000, 6000),
            };
            if (options.reverse) swiperOptions.reverseDirection = !0;
        }

        // Navigation
        if (options.navigation) {
            const section = el.closest('.swiper-section');
            let btnPrev = section.querySelector('.swiper-button-prev');
            let btnNext = section.querySelector('.swiper-button-next');

            if (btnPrev && btnNext) {
                btnPrev.classList.add(classes.prevClass);
                btnNext.classList.add(classes.nextClass);
            } else {
                btnPrev = document.createElement('div');
                btnNext = document.createElement('div');
                btnPrev.classList.add('swiper-button', 'swiper-button-prev', classes.prevClass);
                btnNext.classList.add('swiper-button', 'swiper-button-next', classes.nextClass);
                controls.append(btnPrev, btnNext);

                btnPrev.setAttribute('data-glyph', '');
                btnNext.setAttribute('data-glyph', '');
            }

            swiperOptions.navigation = {
                nextEl: '.' + classes.nextClass,
                prevEl: '.' + classes.prevClass,
            };
        }

        // Pagination
        if (options.pagination) {
            const section = el.closest('.swiper-section');
            let pagination = section.querySelector('.swiper-pagination');
            if (pagination) {
                pagination.classList.add(classes.paginationClass);
            } else {
                pagination = document.createElement('div');
                pagination.classList.add('swiper-pagination', classes.paginationClass);
                controls.appendChild(pagination);
            }

            const paginationType = options.pagination;
            swiperOptions.pagination = {
                el: '.' + classes.paginationClass,
                clickable: !0,
                ...(paginationType === 'bullets' && { dynamicBullets: !0, type: 'bullets' }),
                ...(paginationType === 'fraction' && { type: 'fraction' }),
                ...(paginationType === 'progressbar' && { type: 'progressbar' }),
                ...(paginationType === 'custom' && {
                    renderBullet: (index, className) => `<span class="${className}">${index + 1}</span>`,
                }),
            };
        }

        // Scrollbar
        if (options.scrollbar) {
            const section = el.closest('.swiper-section');
            let scrollbar = section.querySelector('.swiper-scrollbar');
            if (scrollbar) {
                scrollbar.classList.add(classes.scrollbarClass);
            } else {
                scrollbar = document.createElement('div');
                scrollbar.classList.add('swiper-scrollbar', classes.scrollbarClass);
                controls.appendChild(scrollbar);
            }

            swiperOptions.scrollbar = {
                el: '.' + classes.scrollbarClass,
                hide: !0,
                draggable: !0,
            };
        }

        // Marquee
        if (options.marquee) {
            swiperOptions.centeredSlides = !1;
            swiperOptions.autoplay = { delay: 1, disableOnInteraction: !0 };
            swiperOptions.loop = !0;
            swiperOptions.allowTouchMove = !0;
        }

        initializeSwiper('.' + classes.swiperClass, swiperOptions);
    });
};

document.addEventListener('DOMContentLoaded', initializeSwipers);
