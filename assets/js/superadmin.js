/**
 * Super Admin Modern JavaScript Functions
 */

document.addEventListener('DOMContentLoaded', function () {
	initializeSuperAdmin();
});

function initializeSuperAdmin() {
	// Initialize sidebar active states
	setActiveSidebarItem();

	// Initialize modern animations
	initializeAnimations();

	// Initialize tooltips
	initializeTooltips();

	// Initialize stats refresh
	initializeStatsRefresh();

	// Initialize mobile sidebar toggle
	initializeMobileSidebar();

	// Initialize smooth scrolling
	initializeSmoothScrolling();

	console.log('🎯 Super Admin interface initialized successfully!');
}

/**
 * Set active sidebar item based on current page
 */
function setActiveSidebarItem() {
	const currentPath = window.location.pathname;
	const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');

	// Remove all active classes first
	sidebarLinks.forEach((link) => link.classList.remove('active'));

	// Set active based on current path
	sidebarLinks.forEach((link) => {
		const href = link.getAttribute('href');
		if (href && currentPath.includes(href) && href !== '/superadmin/') {
			link.classList.add('active');
		} else if (
			href === '/superadmin/' &&
			(currentPath === '/superadmin/' || currentPath === '/superadmin')
		) {
			link.classList.add('active');
		}
	});
}

/**
 * Initialize modern animations
 */
function initializeAnimations() {
	// Animate stats cards on page load
	const statsCards = document.querySelectorAll('.stats-card');
	statsCards.forEach((card, index) => {
		card.style.opacity = '0';
		card.style.transform = 'translateY(20px)';

		setTimeout(() => {
			card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
			card.style.opacity = '1';
			card.style.transform = 'translateY(0)';
		}, index * 100);
	});

	// Animate page content
	const mainContent = document.querySelector('.main-content');
	if (mainContent) {
		mainContent.style.opacity = '0';
		mainContent.style.transform = 'translateX(20px)';

		setTimeout(() => {
			mainContent.style.transition = 'all 0.5s ease-out';
			mainContent.style.opacity = '1';
			mainContent.style.transform = 'translateX(0)';
		}, 200);
	}
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
	const tooltipTriggerList = [].slice.call(
		document.querySelectorAll('[data-bs-toggle="tooltip"]')
	);
	tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});
}

/**
 * Initialize stats refresh functionality
 */
function initializeStatsRefresh() {
	window.refreshStats = function () {
		const refreshBtn = document.querySelector('[onclick="refreshStats()"]');
		if (refreshBtn) {
			const originalHTML = refreshBtn.innerHTML;
			refreshBtn.innerHTML =
				'<i class="fas fa-spinner fa-spin"></i> Refreshing...';
			refreshBtn.disabled = true;

			// Simulate refresh (replace with actual AJAX call)
			setTimeout(() => {
				refreshBtn.innerHTML = originalHTML;
				refreshBtn.disabled = false;
				showNotification(
					'Statistics refreshed successfully!',
					'success'
				);
			}, 1500);
		}
	};
}

/**
 * Initialize mobile sidebar toggle
 */
function initializeMobileSidebar() {
	// Create mobile toggle button if it doesn't exist
	if (
		window.innerWidth <= 768 &&
		!document.querySelector('.mobile-sidebar-toggle')
	) {
		const toggleBtn = document.createElement('button');
		toggleBtn.className =
			'btn btn-primary mobile-sidebar-toggle position-fixed';
		toggleBtn.style.cssText =
			'top: 20px; left: 20px; z-index: 1001; border-radius: 50%; width: 50px; height: 50px;';
		toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';

		toggleBtn.addEventListener('click', function () {
			const sidebar = document.querySelector('.sidebar');
			sidebar.classList.toggle('show');
		});

		document.body.appendChild(toggleBtn);
	}

	// Close sidebar when clicking outside on mobile
	document.addEventListener('click', function (e) {
		if (window.innerWidth <= 768) {
			const sidebar = document.querySelector('.sidebar');
			const toggleBtn = document.querySelector('.mobile-sidebar-toggle');

			if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
				sidebar.classList.remove('show');
			}
		}
	});
}

/**
 * Initialize smooth scrolling
 */
function initializeSmoothScrolling() {
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
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
	// Remove existing notifications
	const existingNotifications = document.querySelectorAll(
		'.super-admin-notification'
	);
	existingNotifications.forEach((notification) => notification.remove());

	// Create new notification
	const notification = document.createElement('div');
	notification.className = `alert alert-${type} super-admin-notification position-fixed fade-in`;
	notification.style.cssText =
		'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
	notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${getNotificationIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

	document.body.appendChild(notification);

	// Auto remove after 5 seconds
	setTimeout(() => {
		if (notification.parentElement) {
			notification.remove();
		}
	}, 5000);
}

/**
 * Get notification icon based on type
 */
function getNotificationIcon(type) {
	const icons = {
		success: 'check-circle',
		error: 'exclamation-circle',
		warning: 'exclamation-triangle',
		info: 'info-circle',
	};
	return icons[type] || 'info-circle';
}

/**
 * Enhanced table management functions
 */
// showAddTableModal function is implemented in specific table management pages

function showAddPromotionModal() {
	// Implementation for showing add promotion modal
	showNotification('Add Promotion modal would open here', 'info');
}

function toggleEditMode() {
	const form = document.getElementById('restaurantForm');
	const inputs = form.querySelectorAll('input, textarea, select');
	const toggleText = document.getElementById('editToggleText');

	const isReadonly = inputs[0].hasAttribute('readonly');

	inputs.forEach((input) => {
		if (isReadonly) {
			input.removeAttribute('readonly');
			input.removeAttribute('disabled');
		} else {
			input.setAttribute('readonly', true);
		}
	});

	toggleText.textContent = isReadonly ? 'Cancel' : 'Edit';

	if (isReadonly) {
		const saveBtn = document.createElement('button');
		saveBtn.type = 'submit';
		saveBtn.className = 'btn btn-success me-2';
		saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
		saveBtn.id = 'saveBtn';

		toggleText.parentElement.parentElement.insertBefore(
			saveBtn,
			toggleText.parentElement
		);
	} else {
		const saveBtn = document.getElementById('saveBtn');
		if (saveBtn) saveBtn.remove();
	}
}

/**
 * Export report functionality
 */
function exportReport() {
	showNotification('Preparing report for export...', 'info');

	setTimeout(() => {
		showNotification('Report exported successfully!', 'success');
	}, 2000);
}

/**
 * Refresh functions for different pages
 */
function refreshOrders() {
	showNotification('Refreshing order data...', 'info');
	setTimeout(() => {
		showNotification('Order data refreshed!', 'success');
	}, 1500);
}

function refreshBookings() {
	showNotification('Refreshing booking data...', 'info');
	setTimeout(() => {
		showNotification('Booking data refreshed!', 'success');
	}, 1500);
}

/**
 * Add visual feedback to buttons
 */
document.addEventListener('click', function (e) {
	if (e.target.matches('button') || e.target.closest('button')) {
		const button = e.target.matches('button')
			? e.target
			: e.target.closest('button');

		// Add ripple effect
		const ripple = document.createElement('span');
		ripple.className = 'ripple';
		ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;

		button.style.position = 'relative';
		button.style.overflow = 'hidden';
		button.appendChild(ripple);

		setTimeout(() => ripple.remove(), 600);
	}
});

// Add CSS for ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .sidebar .nav-link {
        position: relative;
        overflow: hidden;
    }

    .sidebar .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .sidebar .nav-link:hover::before {
        left: 100%;
    }

    .stats-card {
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(45deg);
        transition: transform 0.6s;
        pointer-events: none;
    }

    .stats-card:hover::before {
        transform: rotate(45deg) translate(50%, 50%);
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }
    }
`;
document.head.appendChild(style);
