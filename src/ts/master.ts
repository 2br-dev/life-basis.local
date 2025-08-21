import Lazy from 'vanilla-lazyload';
import * as M from 'materialize-css';
import TinyParallax from './lib/tinyparallax';
import Swiper from 'swiper';
import {Navigation, Pagination} from 'swiper/modules';

declare var ymaps: any;

let sectionObserver:IntersectionObserver;
let threshold:number = window.innerWidth <= 600 ? .1 : .35;

(() => {

	const lazy = new Lazy({}, document.querySelectorAll('.lazy'));
	const mapEl = document.querySelector('#map') as HTMLElement;
	const heroParallax = new TinyParallax('.main-parallax', 350);
	const parallax = new TinyParallax('.parallax');
	const logisticsParallax = new TinyParallax('.logistics-parallax');

	window.onresize = () => {

		document.querySelectorAll('section').forEach(el => sectionObserver.unobserve(el));

		sectionObserver = new IntersectionObserver(sectionInSight, {
			threshold: threshold
		})

		document.querySelectorAll('section').forEach(el => sectionObserver.observe(el));
	}

	window.onbeforeunload = () => {
		cancelAnimationFrame(heroParallax.processId);
		cancelAnimationFrame(parallax.processId);
		cancelAnimationFrame(logisticsParallax.processId);
	}

	const sidenav = M.Sidenav.init(document.querySelectorAll('.sidenav'), {
		edge: "right"
	})

	document.querySelectorAll('.scroll-link').forEach(el => el.addEventListener('click', (e:MouseEvent) => {
		e.preventDefault();
		const link = e.currentTarget as HTMLLinkElement;
		const url = new URL(link.href);
		const hash = url.hash;
		const target = document.querySelector(hash) as HTMLElement;
		const targetTop = target.offsetTop
		document.documentElement.scrollTop = targetTop;
	}))

	initCrystalSlider();

	if(document.querySelectorAll('header.hero').length) updateHeroHeader();

	const productionSlider = document.querySelectorAll('#production-slider').length ? new Swiper('#production-slider', {
		loop: true,
		spaceBetween: 20,
		modules: [Pagination, Navigation],
		autoHeight: true,
		pagination: {
			type: 'bullets',
			el: '#production-pagination',
			clickable: true,
			dynamicBullets: true,
			dynamicMainBullets: 3
		},
		navigation: {
			prevEl: '.meet-prev',
			nextEl: '.meet-next'
		},
		breakpoints: {
			300: {
				slidesPerView: 1
			},
			800: {
				slidesPerView: 2,
			},
			1200: {
				slidesPerView: 3
			},
			1800: {
				slidesPerView: 4
			}
		}
	}) : null;

	const productSlider = document.querySelectorAll('#product-slider').length ? new Swiper('#product-slider', {
		modules: [Pagination, Navigation],
		spaceBetween: 20,
		loop: true,
		pagination: {
			el: '#product-pagination',
			type: 'bullets',
			clickable: true,
			dynamicBullets: true,
			dynamicMainBullets: 3
		},
		navigation: {
			prevEl: '.production-prev',
			nextEl: '.production-next'
		},
		breakpoints: {
			300: {
				slidesPerView: 1
			},
			600: {
				slidesPerView: 2
			},
			900: {
				slidesPerView: 3
			},
			1400: {
				slidesPerView: 4
			},
			1800: {
				slidesPerView: 5
			}
		}
	}) : null;

	if(mapEl){

		let latitude = parseFloat(mapEl.dataset['lat']);
		let longitude = parseFloat(mapEl.dataset['lon']);
		let zoom = parseInt(mapEl.dataset['zoom']);
		let link = mapEl.dataset['link'];
		let linkText = mapEl.dataset['linktext'];

		loadScript("https://api-maps.yandex.ru/2.1/?lang=ru_RU", () => {
			ymaps.ready(() => {
				const map = new ymaps.Map("map", {
					center: [latitude, longitude],
					zoom: zoom,
				});

				const marker = new ymaps.Placemark(
					[latitude, longitude],
					{ hintContent: linkText },
					{ preset: "islands#orangeStretchyIcon", hasHint: true }
				);

				marker.events.add(["click"], function (e: MouseEvent) {
					e.preventDefault();
					window.open(link, "_blank");
				});

				map.behaviors.disable("scrollZoom");

				map.geoObjects.add(marker);
			});
		});
	}

	sectionObserver = new IntersectionObserver(sectionInSight, {
		threshold: threshold
	})

	document.querySelectorAll('section').forEach((s:HTMLElement) => {
		sectionObserver.observe(s);
	})

})()

function sectionInSight(entries: IntersectionObserverEntry[], observer:IntersectionObserver){
	entries.forEach((e:IntersectionObserverEntry) => {
		if(e.isIntersecting){
			(e.target as HTMLElement).classList.add("in-sight");
		}
	})
}

function initCrystalSlider()
{

	if(!document.querySelectorAll('.crystal-slide').length) return;

	// Определяем самый длинный текстовой блок
	const longest = () => {
		let longest = 0;
		let longestContent = "";
		document.querySelectorAll('.crystal-slide .content-wrapper').forEach(el => {
			if(el.textContent.length > longest){
				let content = el.textContent.trim().replaceAll("\t", "");
				longest = el.textContent.length;
				longestContent = content;
			}
		})

		return longestContent.trim();
	}

	const longestContent = longest();
	const shadow = document.createElement("p");
	shadow.textContent = longestContent;
	shadow.classList.add("shadow");

	document.querySelector('.crystal-text-wrapper')?.append(shadow);


	document.querySelectorAll('.slide-trigger').forEach(el => {
		el.addEventListener('click', (e:MouseEvent) => {
			e.preventDefault();
			const trigger = e.currentTarget as HTMLElement;
			const slideNum = trigger.dataset['slide'];

			document.querySelectorAll('.slide-trigger').forEach(el => el.classList.remove('active'));
			trigger.classList.add('active');

			// Скрываем все слайды
			document.querySelectorAll('.crystal-slide.active').forEach(el => {
				el.classList.remove('active');
			});

			// Отображаем активный слайд
			document.querySelectorAll('[data-slide="'+slideNum+'"]').forEach(activeSlide => {
				activeSlide.classList.add('active');
			})

			// Заменяем теневой текст
			const activeContent = document.querySelector('.crystal-slide.active .content-wrapper')?.textContent.trim().replaceAll("\t", "");
			document.querySelector('.crystal-text-wrapper .shadow').textContent = activeContent;
		})
	})
}

function loadScript(url: string, callback: () => void) {
	var script = <any>document.createElement("script");
	script.type = "text/javascript";

	if (script.readyState) {
		//IE
		script.onreadystatechange = function () {
			if (
				script.readyState == "loaded" ||
				script.readyState == "complete"
			) {
				script.onreadystatechange = null;
				callback();
			}
		};
	} else {
		//Others
		script.onload = function () {
			callback();
		};
	}

	script.src = url;
	document.getElementsByTagName("head")[0].appendChild(script);
}

function updateHeroHeader()
{
	const hero = document.querySelector('section#hero');
	const heroHeight = hero?.clientHeight;
	const scrollTop = document.documentElement.scrollTop;
	const offset = heroHeight / 5;
	const header = document.querySelector('header:not(#header)');

	if(scrollTop >= (offset * 4)){
		header?.classList.add('w');
	}else{
		header?.classList.remove('w');
	}

	requestAnimationFrame(updateHeroHeader);
}
