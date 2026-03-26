/* eslint-disable no-unused-vars */
import '@images/favicon.ico';
import '@styles/tailwind.css'; // Tailwind v3: PostCSS only, no sass-loader
import '@styles/theme';
import './pages/*.js';
import './ajax-search.js';
import './micro-interactions.js';

import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Swup from 'swup';
import Swiper from 'swiper';

import { initAnimations, setupGsap404 } from './components/animations.js';
import { initHeaderScroll }                            from './components/header.js';
import { initMobileMenu }                              from './components/mobile-menu.js';
import { initContactPage }                             from './pages/contact.js';

gsap.registerPlugin( ScrollTrigger );

// ─── Device check ────────────────────────────────────────────────────────────
const isMobile = window.matchMedia && window.matchMedia( '(max-width: 768px)' ).matches;

// ─── Init tất cả features của một page ───────────────────────────────────────
function initPageFeatures() {
	if ( ! isMobile ) {
		setupGsap404();
		initAnimations();
	}

	initHeaderScroll();
	initMobileMenu();
	initContactPage();

	setTimeout( () => ScrollTrigger.refresh(), 500 );
}

// ─── Bootstrap ───────────────────────────────────────────────────────────────
document.addEventListener( 'DOMContentLoaded', () => {
	const swup = new Swup();

	initPageFeatures();

	// Swup: re-init sau mỗi lần navigate (không show loader)
	swup.hooks.on( 'content:replace', initPageFeatures );
} );
