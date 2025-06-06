/**
 * Luxury Smooth Scrolling and Debounce Effects
 * Enhanced scrolling experience for the luxury buffet restaurant website
 */

(function () {
	'use strict';

	// Debounce function to limit scroll event frequency
	function debounce(func, wait) {
		let timeout;
		return function executedFunction(...args) {
			const later = () => {
				clearTimeout(timeout);
				func(...args);
			};
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
		};
	}

	// Throttle function for more frequent updates (like scroll progress)
	function throttle(func, limit) {
		let inThrottle;
		return function (...args) {
			if (!inThrottle) {
				func.apply(this, args);
				inThrottle = true;
				setTimeout(() => (inThrottle = false), limit);
			}
		};
	}

	// Initialize scroll progress indicator
	function initScrollProgress() {
		// Create scroll progress bar if it doesn't exist
		if (!document.querySelector('.scroll-indicator')) {
			const scrollIndicator = document.createElement('div');
			scrollIndicator.className = 'scroll-indicator';
			scrollIndicator.innerHTML = '<div class="scroll-progress"></div>';
			document.body.prepend(scrollIndicator);
		}

		const scrollProgress = document.querySelector('.scroll-progress');

		// Update scroll progress
		const updateScrollProgress = throttle(() => {
			const scrollTop =
				window.pageYOffset || document.documentElement.scrollTop;
			const scrollHeight =
				document.documentElement.scrollHeight - window.innerHeight;
			const progress = (scrollTop / scrollHeight) * 100;

			if (scrollProgress) {
				scrollProgress.style.width = `${Math.min(progress, 100)}%`;
			}
		}, 16); // ~60fps

		window.addEventListener('scroll', updateScrollProgress);
	}

	// Enhanced smooth scrolling for anchor links
	function initSmoothScrolling() {
		// Handle all anchor links with smooth scrolling
		document.addEventListener('click', function (e) {
			const target = e.target.closest('a[href^="#"]');
			if (!target) return;

			const href = target.getAttribute('href');
			if (href === '#' || href === '#!') return;

			const targetElement = document.querySelector(href);
			if (!targetElement) return;

			e.preventDefault();

			// Calculate offset for fixed navbar
			const navbarHeight =
				document.querySelector('.navbar')?.offsetHeight || 80;
			const targetPosition = targetElement.offsetTop - navbarHeight - 20;

			// Smooth scroll with easing
			smoothScrollTo(targetPosition, 800);
		});
	}

	// Custom smooth scroll function with easing
	function smoothScrollTo(targetPosition, duration = 800) {
		const startPosition = window.pageYOffset;
		const distance = targetPosition - startPosition;
		let startTime = null;

		function ease(t, b, c, d) {
			// Ease-in-out cubic
			t /= d / 2;
			if (t < 1) return (c / 2) * t * t * t + b;
			t -= 2;
			return (c / 2) * (t * t * t + 2) + b;
		}

		function animation(currentTime) {
			if (startTime === null) startTime = currentTime;
			const timeElapsed = currentTime - startTime;
			const run = ease(timeElapsed, startPosition, distance, duration);

			window.scrollTo(0, run);

			if (timeElapsed < duration) {
				requestAnimationFrame(animation);
			}
		}

		requestAnimationFrame(animation);
	}

	// Scroll-triggered animations with debounce
	function initScrollAnimations() {
		const observerOptions = {
			threshold: 0.1,
			rootMargin: '0px 0px -50px 0px',
		};

		// Create intersection observer for fade-in animations
		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('fade-in-active');
				}
			});
		}, observerOptions);

		// Observe elements with fade-in classes
		const fadeElements = document.querySelectorAll(
			'.fade-in-up, .fade-in-left, .fade-in-right, .fade-in'
		);
		fadeElements.forEach((el) => observer.observe(el));
	}

	// Navbar scroll effects with debounce
	function initNavbarScrollEffects() {
		const navbar = document.querySelector('.navbar');
		if (!navbar) return;

		const debouncedScrollHandler = debounce(() => {
			const scrolled = window.pageYOffset > 50;
			navbar.classList.toggle('scrolled', scrolled);
		}, 10);

		window.addEventListener('scroll', debouncedScrollHandler);
	}

	// Parallax effects for backgrounds (light debounce)
	function initParallaxEffects() {
		const parallaxElements = document.querySelectorAll('[data-parallax]');
		if (parallaxElements.length === 0) return;

		const parallaxHandler = throttle(() => {
			const scrolled = window.pageYOffset;

			parallaxElements.forEach((element) => {
				const speed = element.dataset.parallax || 0.5;
				const yPos = -(scrolled * speed);
				element.style.transform = `translateY(${yPos}px)`;
			});
		}, 16);

		window.addEventListener('scroll', parallaxHandler);
	}

	// Floating elements animation
	function initFloatingAnimations() {
		const floatingElements = document.querySelectorAll(
			'.floating-badge, .promotion-highlight'
		);

		floatingElements.forEach((element, index) => {
			// Add slight delay and variation to each floating element
			element.style.animationDelay = `${index * 0.5}s`;
			element.style.animationDuration = `${3 + (index % 3)}s`;
		});
	}

	// Smooth page transitions
	function initPageTransitions() {
		// Add fade-in effect to page load
		document.body.style.opacity = '0';
		document.body.style.transition = 'opacity 0.5s ease-in-out';

		window.addEventListener('load', () => {
			document.body.style.opacity = '1';
		});

		// Handle page visibility changes
		document.addEventListener('visibilitychange', () => {
			if (!document.hidden) {
				// Re-trigger animations when page becomes visible
				document.body.classList.add('page-visible');
			}
		});
	}

	// Back to top button with smooth scrolling
	function initBackToTop() {
		// Create back to top button if it doesn't exist
		if (!document.querySelector('.back-to-top')) {
			const backToTop = document.createElement('button');
			backToTop.className = 'back-to-top btn-luxury-circle';
			backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
			backToTop.style.cssText = `
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--primary-gold), var(--accent-copper));
                color: white;
                border: none;
                font-size: 1.2rem;
                cursor: pointer;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                z-index: 1000;
                box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
            `;
			document.body.appendChild(backToTop);

			// Show/hide based on scroll position
			const toggleBackToTop = debounce(() => {
				const scrolled = window.pageYOffset > 500;
				backToTop.style.opacity = scrolled ? '1' : '0';
				backToTop.style.visibility = scrolled ? 'visible' : 'hidden';
			}, 100);

			window.addEventListener('scroll', toggleBackToTop);

			// Smooth scroll to top on click
			backToTop.addEventListener('click', (e) => {
				e.preventDefault();
				smoothScrollTo(0, 600);
			});
		}
	}

	// Enhanced scroll performance
	function optimizeScrollPerformance() {
		// Use passive listeners for better performance
		const passiveEvents = ['scroll', 'touchstart', 'touchmove'];
		passiveEvents.forEach((event) => {
			window.addEventListener(event, () => {}, { passive: true });
		});

		// Reduce motion for users who prefer it
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			document.documentElement.style.scrollBehavior = 'auto';
			document.querySelectorAll('.smooth-scroll').forEach((el) => {
				el.style.transition = 'none';
			});
		}
	}

	// Initialize all scroll effects when DOM is ready
	function init() {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', init);
			return;
		}

		// Initialize all scroll-related features
		initScrollProgress();
		initSmoothScrolling();
		initScrollAnimations();
		initNavbarScrollEffects();
		initParallaxEffects();
		initFloatingAnimations();
		initPageTransitions();
		initBackToTop();
		optimizeScrollPerformance();

		console.log('🎯 Luxury smooth scrolling initialized');
	}

	// Start initialization
	init();

	// Export for potential external use
	window.LuxuryScroll = {
		smoothScrollTo,
		debounce,
		throttle,
	};
})();
