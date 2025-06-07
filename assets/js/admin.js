// Admin Panel JavaScript Functions

document.addEventListener('DOMContentLoaded', function () {
	// Initialize admin functionality
	initializeAdmin();

	// Handle sidebar toggle on mobile
	handleSidebarToggle();

	// Initialize any DataTables
	initializeDataTables();
});

function initializeAdmin() {
	// Auto-hide alerts
	autoHideAlerts();

	// Initialize tooltips
	initializeTooltips();

	// Initialize status updates
	initializeStatusUpdates();

	// Initialize form validation
	initializeFormValidation();

	// Initialize image previews
	initializeImagePreviews();

	// Set active nav links
	setActiveNavLinks();
}

// Handle sidebar toggle on mobile devices
function handleSidebarToggle() {
	const sidebarToggle = document.querySelector('.navbar-toggler');
	if (sidebarToggle) {
		sidebarToggle.addEventListener('click', function () {
			document.body.classList.toggle('sidebar-toggled');
			const sidebar = document.querySelector('.sidebar');
			if (sidebar) {
				sidebar.classList.toggle('toggled');
			}
		});
	}

	// Close sidebar when screen size is smaller on resize
	window.addEventListener('resize', function () {
		if (window.innerWidth < 768) {
			const sidebar = document.querySelector('.sidebar.show');
			if (sidebar) {
				const bsCollapse = new bootstrap.Collapse(sidebar);
				bsCollapse.hide();
			}
		}
	});
}

// Auto-hide alert messages
function autoHideAlerts() {
	const alerts = document.querySelectorAll('.alert');
	alerts.forEach(function (alert) {
		setTimeout(function () {
			if (alert && alert.parentNode) {
				const bsAlert = new bootstrap.Alert(alert);
				bsAlert.close();
			}
		}, 5000);
	});
}

// Initialize Bootstrap tooltips
function initializeTooltips() {
	const tooltipTriggerList = [].slice.call(
		document.querySelectorAll('[data-bs-toggle="tooltip"]')
	);
	tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});
}

// Initialize DataTables
function initializeDataTables() {
	if ($.fn.DataTable) {
		const tables = document.querySelectorAll(
			'table.dataTable, table.table-datatable'
		);
		tables.forEach(function (table) {
			$(table).DataTable({
				responsive: true,
				language: {
					search: '_INPUT_',
					searchPlaceholder: 'Tìm kiếm...',
					lengthMenu: 'Hiển thị _MENU_ mục',
					info: 'Hiển thị _START_ đến _END_ của _TOTAL_ mục',
					infoEmpty: 'Hiển thị 0 đến 0 của 0 mục',
					infoFiltered: '(lọc từ _MAX_ mục)',
					paginate: {
						first: 'Đầu',
						last: 'Cuối',
						next: 'Sau',
						previous: 'Trước',
					},
				},
			});
		});
	}
}

// Set active nav links
function setActiveNavLinks() {
	const currentPath = window.location.pathname;
	const navLinks = document.querySelectorAll('.sidebar .nav-link');

	navLinks.forEach(function (link) {
		const href = link.getAttribute('href');
		if (href && currentPath.includes(href) && href !== '/admin') {
			link.classList.add('active');
		} else if (
			href === '/admin' &&
			(currentPath === '/admin' || currentPath === '/admin/')
		) {
			link.classList.add('active');
		}
	});
}

// Initialize status update functionality
function initializeStatusUpdates() {
	const statusSelects = document.querySelectorAll('.status-select');

	statusSelects.forEach((select) => {
		select.addEventListener('change', function () {
			const orderId = this.dataset.orderId || this.dataset.bookingId;
			const newStatus = this.value;
			const currentStatus = this.dataset.currentStatus;
			const type = this.dataset.orderId ? 'order' : 'booking';

			if (newStatus !== currentStatus) {
				updateStatus(orderId, newStatus, this, type);
			}
		});
	});
}

// Update order or booking status
function updateStatus(id, status, selectElement, type) {
	const originalStatus = selectElement.dataset.currentStatus;
	const endpoint =
		type === 'order'
			? '/admin/orders/update-status/'
			: '/admin/bookings/update-status/';

	// Show loading state
	selectElement.classList.add('loading');

	fetch(endpoint + id, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body:
			'csrf_token=' +
			encodeURIComponent(window.csrfToken) +
			'&status=' +
			encodeURIComponent(status),
	})
		.then((response) => response.json())
		.then((data) => {
			selectElement.classList.remove('loading');

			if (data.success) {
				selectElement.dataset.currentStatus = status;
				showAlert('success', data.message);

				// Update any status badges or indicators
				updateStatusIndicators(selectElement, status);
			} else {
				selectElement.value = originalStatus;
				showAlert('error', data.message);
			}
		})
		.catch((error) => {
			selectElement.classList.remove('loading');
			selectElement.value = originalStatus;
			showAlert('error', 'Failed to update ' + type + ' status');
			console.error('Error:', error);
		});
}

// Update status indicators
function updateStatusIndicators(selectElement, status) {
	const row = selectElement.closest('tr');
	if (row) {
		const statusBadges = row.querySelectorAll('.badge');
		statusBadges.forEach((badge) => {
			if (badge.textContent.toLowerCase().includes(status)) {
				badge.classList.remove(
					'bg-warning',
					'bg-info',
					'bg-primary',
					'bg-success',
					'bg-danger'
				);

				const statusColors = {
					pending: 'bg-warning',
					confirmed: 'bg-info',
					preparing: 'bg-primary',
					ready: 'bg-success',
					delivered: 'bg-success',
					cancelled: 'bg-danger',
				};

				badge.classList.add(statusColors[status] || 'bg-secondary');
			}
		});
	}
}

// Initialize form validation
function initializeFormValidation() {
	const forms = document.querySelectorAll('.needs-validation');

	Array.prototype.slice.call(forms).forEach(function (form) {
		form.addEventListener(
			'submit',
			function (event) {
				if (!form.checkValidity()) {
					event.preventDefault();
					event.stopPropagation();
				}

				form.classList.add('was-validated');
			},
			false
		);
	});

	// Password confirmation validation
	const passwordFields = document.querySelectorAll('input[name="password"]');
	const confirmPasswordFields = document.querySelectorAll(
		'input[name="confirm_password"]'
	);

	if (passwordFields.length && confirmPasswordFields.length) {
		function validatePasswords() {
			const password = passwordFields[0].value;
			const confirmPassword = confirmPasswordFields[0].value;

			if (password !== confirmPassword) {
				confirmPasswordFields[0].setCustomValidity(
					'Passwords do not match'
				);
				confirmPasswordFields[0].classList.add('is-invalid');
			} else {
				confirmPasswordFields[0].setCustomValidity('');
				confirmPasswordFields[0].classList.remove('is-invalid');
			}
		}

		passwordFields[0].addEventListener('input', validatePasswords);
		confirmPasswordFields[0].addEventListener('input', validatePasswords);
	}
}

// Initialize image preview functionality
function initializeImagePreviews() {
	const imageInputs = document.querySelectorAll(
		'input[type="file"][accept*="image"]'
	);

	imageInputs.forEach((input) => {
		input.addEventListener('change', function (e) {
			const file = e.target.files[0];
			if (file) {
				const reader = new FileReader();
				reader.onload = function (e) {
					let preview = document.getElementById('image-preview');
					if (!preview) {
						preview = document.createElement('img');
						preview.id = 'image-preview';
						preview.className = 'img-thumbnail mt-2';
						preview.style.maxWidth = '200px';
						preview.style.maxHeight = '200px';
						input.parentNode.appendChild(preview);
					}
					preview.src = e.target.result;
				};
				reader.readAsDataURL(file);
			}
		});
	});
}

// Show alert messages
function showAlert(type, message, duration = 5000) {
	const alertDiv = document.createElement('div');
	alertDiv.className = `alert alert-${
		type === 'error' ? 'danger' : type
	} alert-dismissible fade show`;
	alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

	const main = document.querySelector('main');
	if (main) {
		main.insertBefore(alertDiv, main.firstElementChild);

		// Auto-hide after duration
		setTimeout(() => {
			if (alertDiv.parentNode) {
				const bsAlert = new bootstrap.Alert(alertDiv);
				bsAlert.close();
			}
		}, duration);
	}
}

// Confirm delete actions
document.addEventListener('click', function (e) {
	if (
		e.target.classList.contains('btn-delete') ||
		e.target.closest('.btn-delete')
	) {
		const itemType = e.target.dataset.itemType || 'item';
		const confirmMessage = `Are you sure you want to delete this ${itemType}? This action cannot be undone.`;

		if (!confirm(confirmMessage)) {
			e.preventDefault();
			return false;
		}
	}
});

// Data table functionality
function initializeDataTable(tableId) {
	const table = document.getElementById(tableId);
	if (!table) return;

	// Add search functionality
	const searchInput = document.createElement('input');
	searchInput.type = 'text';
	searchInput.className = 'form-control mb-3';
	searchInput.placeholder = 'Search...';

	table.parentNode.insertBefore(searchInput, table);

	searchInput.addEventListener('input', function () {
		const searchTerm = this.value.toLowerCase();
		const rows = table.querySelectorAll('tbody tr');

		rows.forEach((row) => {
			const text = row.textContent.toLowerCase();
			if (text.includes(searchTerm)) {
				row.style.display = '';
			} else {
				row.style.display = 'none';
			}
		});
	});
}

// Export data functionality
function exportData(format, data, filename) {
	if (format === 'csv') {
		exportCSV(data, filename);
	} else if (format === 'json') {
		exportJSON(data, filename);
	}
}

function exportCSV(data, filename) {
	const csv = convertToCSV(data);
	downloadFile(csv, filename + '.csv', 'text/csv');
}

function exportJSON(data, filename) {
	const json = JSON.stringify(data, null, 2);
	downloadFile(json, filename + '.json', 'application/json');
}

function convertToCSV(data) {
	if (!data.length) return '';

	const headers = Object.keys(data[0]);
	const csvContent = [
		headers.join(','),
		...data.map((row) =>
			headers.map((header) => `"${row[header] || ''}"`).join(',')
		),
	].join('\n');

	return csvContent;
}

function downloadFile(content, filename, mimeType) {
	const blob = new Blob([content], { type: mimeType });
	const url = URL.createObjectURL(blob);
	const link = document.createElement('a');

	link.href = url;
	link.download = filename;
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
	URL.revokeObjectURL(url);
}

// Dashboard stats refresh
function refreshDashboardStats() {
	fetch('/admin/dashboard/stats')
		.then((response) => response.json())
		.then((data) => {
			updateDashboardStats(data);
		})
		.catch((error) => {
			console.error('Error refreshing dashboard stats:', error);
		});
}

function updateDashboardStats(stats) {
	Object.keys(stats).forEach((key) => {
		const element = document.querySelector(`[data-stat="${key}"]`);
		if (element) {
			element.textContent = stats[key];
		}
	});
}

// Real-time notifications
function initializeNotifications() {
	// Check for new orders/bookings every 30 seconds
	setInterval(checkForUpdates, 30000);
}

function checkForUpdates() {
	fetch('/admin/notifications/check')
		.then((response) => response.json())
		.then((data) => {
			if (data.newOrders > 0) {
				showNotification('New orders received!', 'info');
				updateOrderBadge(data.newOrders);
			}
			if (data.newBookings > 0) {
				showNotification('New bookings received!', 'info');
				updateBookingBadge(data.newBookings);
			}
		})
		.catch((error) => {
			console.error('Error checking for updates:', error);
		});
}

function showNotification(message, type) {
	// Create notification element
	const notification = document.createElement('div');
	notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
	notification.style.cssText =
		'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
	notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

	document.body.appendChild(notification);

	// Auto-hide after 5 seconds
	setTimeout(() => {
		if (notification.parentNode) {
			notification.remove();
		}
	}, 5000);
}

function updateOrderBadge(count) {
	const badge = document.querySelector(
		'.sidebar a[href="/admin/orders"] .badge'
	);
	if (badge) {
		badge.textContent = count;
		badge.style.display = count > 0 ? 'inline' : 'none';
	}
}

function updateBookingBadge(count) {
	const badge = document.querySelector(
		'.sidebar a[href="/admin/bookings"] .badge'
	);
	if (badge) {
		badge.textContent = count;
		badge.style.display = count > 0 ? 'inline' : 'none';
	}
}

// CSRF token management
window.csrfToken =
	document
		.querySelector('meta[name="csrf-token"]')
		?.getAttribute('content') || '';

// Set CSRF token for all AJAX requests
if (window.fetch) {
	const originalFetch = window.fetch;
	window.fetch = function (url, options = {}) {
		if (options.method && options.method.toUpperCase() !== 'GET') {
			if (options.body instanceof FormData) {
				options.body.append('csrf_token', window.csrfToken);
			} else if (
				typeof options.body === 'string' &&
				options.headers &&
				options.headers['Content-Type'] ===
					'application/x-www-form-urlencoded'
			) {
				options.body +=
					'&csrf_token=' + encodeURIComponent(window.csrfToken);
			}
		}
		return originalFetch(url, options);
	};
}

// Utility functions
function formatCurrency(amount) {
	return new Intl.NumberFormat('en-US', {
		style: 'currency',
		currency: 'USD',
	}).format(amount);
}

function formatDate(dateString) {
	return new Intl.DateTimeFormat('en-US', {
		year: 'numeric',
		month: 'short',
		day: 'numeric',
		hour: '2-digit',
		minute: '2-digit',
	}).format(new Date(dateString));
}

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

// Print functionality
function printElement(elementId) {
	const element = document.getElementById(elementId);
	if (!element) return;

	const printWindow = window.open('', '_blank');
	printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { padding: 20px; }
                .no-print { display: none !important; }
                @media print {
                    .btn, .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            ${element.innerHTML}
            <script>
                window.onload = function() {
                    window.print();
                    window.close();
                };
            </script>
        </body>
        </html>
    `);
	printWindow.document.close();
}

// Initialize notifications on load
document.addEventListener('DOMContentLoaded', function () {
	if (window.location.pathname.includes('/admin')) {
		initializeNotifications();
	}
});
