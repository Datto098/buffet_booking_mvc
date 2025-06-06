/**
 * Luxury Restaurant Website - Interactive Effects
 * Hiệu ứng tương tác cho website nhà hàng cao cấp
 */

document.addEventListener('DOMContentLoaded', function () {
	// Navbar scroll effect
	const navbar = document.getElementById('mainNavbar');
	let lastScrollTop = 0;

	window.addEventListener('scroll', function () {
		let scrollTop =
			window.pageYOffset || document.documentElement.scrollTop;

		if (scrollTop > 100) {
			navbar.classList.add('scrolled');
		} else {
			navbar.classList.remove('scrolled');
		}

		// Hide/show navbar on scroll
		if (scrollTop > lastScrollTop && scrollTop > 200) {
			navbar.style.transform = 'translateY(-100%)';
		} else {
			navbar.style.transform = 'translateY(0)';
		}

		lastScrollTop = scrollTop;
	});

	// Smooth scrolling for anchor links
	document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
		anchor.addEventListener('click', function (e) {
			e.preventDefault();
			const target = document.querySelector(this.getAttribute('href'));
			if (target) {
				target.scrollIntoView({
					behavior: 'smooth',
					block: 'start',
				});
			}
		});
	});

	// Parallax effect for hero section
	const heroSection = document.querySelector('.hero-section');
	if (heroSection) {
		window.addEventListener('scroll', function () {
			let scrolled = window.pageYOffset;
			let rate = scrolled * -0.5;
			heroSection.style.transform = `translateY(${rate}px)`;
		});
	}

	// Intersection Observer for animations
	const observerOptions = {
		threshold: 0.1,
		rootMargin: '0px 0px -100px 0px',
	};

	const observer = new IntersectionObserver(function (entries) {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				entry.target.classList.add('animate-in');
			}
		});
	}, observerOptions);

	// Observe elements for animation
	document
		.querySelectorAll('.card-luxury, .food-item, .section-title')
		.forEach((el) => {
			observer.observe(el);
		});

	// Card hover effects with tilt
	document.querySelectorAll('.card-luxury, .food-item').forEach((card) => {
		card.addEventListener('mouseenter', function (e) {
			this.style.transition = 'transform 0.3s ease-out';
		});

		card.addEventListener('mousemove', function (e) {
			const rect = this.getBoundingClientRect();
			const x = e.clientX - rect.left;
			const y = e.clientY - rect.top;

			const centerX = rect.width / 2;
			const centerY = rect.height / 2;

			const rotateX = (y - centerY) / 20;
			const rotateY = (centerX - x) / 20;

			this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`;
		});

		card.addEventListener('mouseleave', function () {
			this.style.transform =
				'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
		});
	});

	// Price counter animation
	function animateCounter(element, start, end, duration) {
		let startTimestamp = null;
		const step = (timestamp) => {
			if (!startTimestamp) startTimestamp = timestamp;
			const progress = Math.min(
				(timestamp - startTimestamp) / duration,
				1
			);
			const current = Math.floor(progress * (end - start) + start);
			element.textContent = current.toLocaleString('vi-VN') + 'đ';
			if (progress < 1) {
				window.requestAnimationFrame(step);
			}
		};
		window.requestAnimationFrame(step);
	}

	// Animate price counters when they come into view
	const priceObserver = new IntersectionObserver(function (entries) {
		entries.forEach((entry) => {
			if (
				entry.isIntersecting &&
				!entry.target.classList.contains('animated')
			) {
				const priceElement = entry.target;
				const price = parseInt(
					priceElement.textContent.replace(/[^\d]/g, '')
				);
				if (price > 0) {
					animateCounter(priceElement, 0, price, 1500);
					priceElement.classList.add('animated');
				}
			}
		});
	});

	document.querySelectorAll('.price-current').forEach((price) => {
		priceObserver.observe(price);
	});

	// Loading states for buttons
	document
		.querySelectorAll('.btn-luxury, .btn-outline-luxury')
		.forEach((btn) => {
			btn.addEventListener('click', function (e) {
				if (this.classList.contains('loading')) {
					e.preventDefault();
					return;
				}

				// Add loading state for form submissions
				if (this.type === 'submit' || this.form) {
					this.classList.add('loading');
					this.style.position = 'relative';

					setTimeout(() => {
						this.classList.remove('loading');
					}, 3000);
				}
			});
		});

	// Image lazy loading with fade effect
	const imageObserver = new IntersectionObserver(function (entries) {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				const img = entry.target;
				img.src = img.dataset.src;
				img.classList.add('fade-in');
				imageObserver.unobserve(img);
			}
		});
	});

	document.querySelectorAll('img[data-src]').forEach((img) => {
		imageObserver.observe(img);
	});

	// Toast notifications
	function showToast(message, type = 'success') {
		const toast = document.createElement('div');
		toast.className = `toast-notification toast-${type}`;
		toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${
					type === 'success' ? 'check-circle' : 'exclamation-circle'
				}"></i>
                <span>${message}</span>
            </div>
            <button class="toast-close">&times;</button>
        `;

		document.body.appendChild(toast);

		setTimeout(() => toast.classList.add('show'), 100);

		const closeBtn = toast.querySelector('.toast-close');
		closeBtn.addEventListener('click', () => {
			toast.classList.remove('show');
			setTimeout(() => document.body.removeChild(toast), 300);
		});

		setTimeout(() => {
			toast.classList.remove('show');
			setTimeout(() => {
				if (document.body.contains(toast)) {
					document.body.removeChild(toast);
				}
			}, 300);
		}, 5000);
	}

	// Form enhancements
	document.querySelectorAll('form').forEach((form) => {
		form.addEventListener('submit', function (e) {
			const submitBtn = form.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.classList.add('loading');
			}
		});
	});

	// Add floating elements animation
	function createFloatingElements() {
		const container = document.querySelector('.hero-section');
		if (!container) return;

		for (let i = 0; i < 6; i++) {
			const element = document.createElement('div');
			element.className = 'floating-element';
			element.style.cssText = `
                position: absolute;
                width: ${Math.random() * 6 + 4}px;
                height: ${Math.random() * 6 + 4}px;
                background: rgba(212, 175, 55, ${Math.random() * 0.3 + 0.1});
                border-radius: 50%;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: float ${Math.random() * 3 + 4}s ease-in-out infinite;
                animation-delay: ${Math.random() * 2}s;
            `;
			container.appendChild(element);
		}
	}

	createFloatingElements();

	// Search functionality enhancement
	const searchInputs = document.querySelectorAll(
		'input[type="search"], .search-input'
	);
	searchInputs.forEach((input) => {
		let searchTimeout;
		input.addEventListener('input', function () {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => {
				// Add search logic here
				console.log('Searching for:', this.value);
			}, 300);
		});
	});

	// Mobile menu enhancements
	const navbarToggler = document.querySelector('.navbar-toggler');
	const navbarCollapse = document.querySelector('.navbar-collapse');

	if (navbarToggler && navbarCollapse) {
		navbarToggler.addEventListener('click', function () {
			document.body.classList.toggle('menu-open');
		});

		// Close menu when clicking outside
		document.addEventListener('click', function (e) {
			if (
				!navbar.contains(e.target) &&
				navbarCollapse.classList.contains('show')
			) {
				document.body.classList.remove('menu-open');
			}
		});
	}

	// Add CSS for animations
	const style = document.createElement('style');
	style.textContent = `
        .animate-in {
            animation: slideInUp 0.6s ease-out forwards;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transform: translateX(400px);
            transition: transform 0.3s ease-out;
            z-index: 10000;
            min-width: 300px;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .toast-success i {
            color: var(--primary-gold);
        }

        .toast-close {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #999;
        }

        @media (max-width: 768px) {
            .toast-notification {
                right: 10px;
                left: 10px;
                min-width: auto;
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Enhanced navigation transitions */
        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .dropdown-menu {
            transition: all 0.3s ease-out !important;
        }

        .btn-booking {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Mobile menu animations */
        .navbar-collapse {
            transition: all 0.3s ease-out !important;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse.show {
                animation: slideDown 0.3s ease-out;
            }
        }
    `;
	document.head.appendChild(style);
});

// Global functions
window.showToast = function (message, type = 'success') {
	const event = new CustomEvent('showToast', { detail: { message, type } });
	document.dispatchEvent(event);
};

/**
 * Enhanced Navigation Effects
 * Hiệu ứng navigation nâng cao
 */

// Enhanced navbar scroll effects
function initNavbarEffects() {
	const navbar = document.getElementById('mainNavbar');
	let lastScrollTop = 0;
	let ticking = false;

	function updateNavbar() {
		let scrollTop =
			window.pageYOffset || document.documentElement.scrollTop;

		// Add scrolled class
		if (scrollTop > 50) {
			navbar.classList.add('scrolled');
		} else {
			navbar.classList.remove('scrolled');
		}

		// Auto hide/show navbar
		if (scrollTop > lastScrollTop && scrollTop > 150) {
			navbar.style.transform = 'translateY(-100%)';
			navbar.style.transition = 'transform 0.3s ease-in-out';
		} else {
			navbar.style.transform = 'translateY(0)';
			navbar.style.transition = 'transform 0.3s ease-in-out';
		}

		lastScrollTop = scrollTop;
		ticking = false;
	}

	window.addEventListener('scroll', function () {
		if (!ticking) {
			requestAnimationFrame(updateNavbar);
			ticking = true;
		}
	});
}

// Dropdown menu enhanced animations
function initDropdownEffects() {
	const dropdowns = document.querySelectorAll('.dropdown');

	dropdowns.forEach((dropdown) => {
		const toggle = dropdown.querySelector('.dropdown-toggle');
		const menu = dropdown.querySelector('.dropdown-menu');

		if (toggle && menu) {
			// Add smooth slide animation
			toggle.addEventListener('click', function () {
				setTimeout(() => {
					if (menu.classList.contains('show')) {
						menu.style.animation = 'slideDown 0.3s ease-out';
					}
				}, 10);
			});

			// Enhanced hover effects
			dropdown.addEventListener('mouseenter', function () {
				if (window.innerWidth >= 992) {
					// Desktop only
					toggle.classList.add('show');
					menu.classList.add('show');
					menu.style.animation = 'slideDown 0.3s ease-out';
				}
			});

			dropdown.addEventListener('mouseleave', function () {
				if (window.innerWidth >= 992) {
					toggle.classList.remove('show');
					menu.classList.remove('show');
					menu.style.animation = 'slideUp 0.3s ease-out';
				}
			});
		}
	});
}

// Cart badge animation
function initCartEffects() {
	const cartBadge = document.getElementById('cart-count');
	const cartLink = document.querySelector('a[href*="cart"]');

	if (cartBadge && cartLink) {
		cartLink.addEventListener('mouseenter', function () {
			cartBadge.style.transform = 'scale(1.2)';
			cartBadge.style.transition = 'transform 0.2s ease-out';
		});

		cartLink.addEventListener('mouseleave', function () {
			cartBadge.style.transform = 'scale(1)';
		});
	}
}

// Search modal enhanced effects
function initSearchEffects() {
	const searchModal = document.getElementById('searchModal');
	const searchInput = document.getElementById('searchInput');
	const searchBtn = document.querySelector('.btn-search');

	if (searchModal && searchInput) {
		searchModal.addEventListener('shown.bs.modal', function () {
			searchInput.focus();
			// Add typing animation to placeholder
			animatePlaceholder(searchInput);
		});

		// Enhanced search button animation
		if (searchBtn) {
			searchBtn.addEventListener('click', function () {
				this.style.transform = 'scale(0.95)';
				setTimeout(() => {
					this.style.transform = 'scale(1)';
				}, 150);
			});
		}
	}
}

// Animated placeholder text
function animatePlaceholder(input) {
	const texts = [
		'Nhập tên món ăn...',
		'Tìm pizza, burger...',
		'Tìm theo danh mục...',
		'Nhập từ khóa...',
	];
	let currentIndex = 0;

	setInterval(() => {
		input.placeholder = texts[currentIndex];
		currentIndex = (currentIndex + 1) % texts.length;
	}, 3000);
}

// Booking button special effects
function initBookingEffects() {
	const bookingBtn = document.querySelector('.btn-booking');

	if (bookingBtn) {
		// Pulse effect on page load
		setTimeout(() => {
			bookingBtn.style.animation = 'pulse 2s infinite';
		}, 2000);

		// Enhanced click effect
		bookingBtn.addEventListener('click', function (e) {
			// Create ripple effect
			createRipple(e, this);

			// Stop pulse animation
			this.style.animation = 'none';

			// Add success feedback
			const originalText = this.innerHTML;
			this.innerHTML =
				'<i class="fas fa-check me-1"></i><span>Đang xử lý...</span>';
			this.style.background = '#28a745';

			setTimeout(() => {
				this.innerHTML = originalText;
				this.style.background = '';
			}, 2000);
		});
	}
}

// Ripple effect function
function createRipple(event, element) {
	const ripple = document.createElement('span');
	const rect = element.getBoundingClientRect();
	const size = Math.max(rect.width, rect.height);
	const x = event.clientX - rect.left - size / 2;
	const y = event.clientY - rect.top - size / 2;

	ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    `;

	element.style.position = 'relative';
	element.style.overflow = 'hidden';
	element.appendChild(ripple);

	setTimeout(() => {
		ripple.remove();
	}, 600);
}

// Navigation active state management
function initActiveStates() {
	const navLinks = document.querySelectorAll('.nav-link');
	const currentPath = window.location.pathname;

	navLinks.forEach((link) => {
		const href = link.getAttribute('href');
		if (href && (href === currentPath || currentPath.includes(href))) {
			link.classList.add('active');
		}

		// Enhanced hover animations
		link.addEventListener('mouseenter', function () {
			if (!this.classList.contains('dropdown-toggle')) {
				this.style.transform = 'translateY(-2px)';
			}
		});

		link.addEventListener('mouseleave', function () {
			if (!this.classList.contains('dropdown-toggle')) {
				this.style.transform = 'translateY(0)';
			}
		});
	});
}

// Mobile menu enhancements
function initMobileMenuEffects() {
	const navbarToggler = document.querySelector('.navbar-toggler');
	const navbarCollapse = document.querySelector('.navbar-collapse');

	if (navbarToggler && navbarCollapse) {
		navbarToggler.addEventListener('click', function () {
			// Animate hamburger icon
			this.style.transform = 'rotate(90deg)';
			setTimeout(() => {
				this.style.transform = 'rotate(0deg)';
			}, 300);
		});

		// Close mobile menu when clicking outside
		document.addEventListener('click', function (e) {
			if (
				window.innerWidth < 992 &&
				navbarCollapse.classList.contains('show') &&
				!navbarCollapse.contains(e.target) &&
				!navbarToggler.contains(e.target)
			) {
				const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
					hide: true,
				});
			}
		});
	}
}

// Add CSS animations
function addCustomAnimations() {
	const style = document.createElement('style');
	style.textContent = `
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Enhanced navigation transitions */
        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .dropdown-menu {
            transition: all 0.3s ease-out !important;
        }

        .btn-booking {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Mobile menu animations */
        .navbar-collapse {
            transition: all 0.3s ease-out !important;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse.show {
                animation: slideDown 0.3s ease-out;
            }
        }
    `;
	document.head.appendChild(style);
}

// Initialize all navigation effects
function initNavigationEffects() {
	initNavbarEffects();
	initDropdownEffects();
	initCartEffects();
	initSearchEffects();
	initBookingEffects();
	initActiveStates();
	initMobileMenuEffects();
	addCustomAnimations();

	console.log('✨ Luxury navigation effects initialized!');
}

// Call initialization when DOM is ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initNavigationEffects);
} else {
	initNavigationEffects();
}
