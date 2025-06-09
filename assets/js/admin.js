// Admin Panel JavaScript Functions

// Global variables and configuration
window.adminConfig = {
	csrfToken:
		document
			.querySelector('meta[name="csrf-token"]')
			?.getAttribute('content') || '',
	initialized: false,
};

document.addEventListener('DOMContentLoaded', function () {
	if (window.adminConfig.initialized) return;

	// Initialize admin functionality
	initializeAdmin();

	// Handle sidebar toggle on mobile
	handleSidebarToggle();

	// Initialize CSRF token for requests
	initializeCSRF();

	// Initialize DataTables
	initializeDataTablesAdvanced();

	// Initialize notifications if in admin area
	if (window.location.pathname.includes('/admin')) {
		initializeNotifications();
	}

	window.adminConfig.initialized = true;
});

function initializeAdmin() {
	// Auto-hide alerts
	autoHideAlerts();

	// Initialize tooltips
	initializeTooltips();
	// Initialize status updates
	initializeStatusUpdates();

	// Initialize payment status updates
	initializePaymentStatusUpdates();

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

// Initialize CSRF token for all AJAX requests
function initializeCSRF() {
	// Set global CSRF token
	window.csrfToken = window.adminConfig.csrfToken;

	// Set CSRF token for jQuery AJAX requests
	if (window.jQuery) {
		$.ajaxSetup({
			beforeSend: function (xhr, settings) {
				if (
					!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) &&
					!this.crossDomain
				) {
					xhr.setRequestHeader('X-CSRFToken', window.csrfToken);
				}
			},
		});
	}

	// Override fetch to include CSRF token
	if (window.fetch) {
		const originalFetch = window.fetch;
		window.fetch = function (url, options = {}) {
			if (options.method && options.method.toUpperCase() !== 'GET') {
				if (options.body instanceof FormData) {
					if (!options.body.has('csrf_token')) {
						options.body.append('csrf_token', window.csrfToken);
					}
				} else if (
					typeof options.body === 'string' &&
					options.headers &&
					options.headers['Content-Type'] ===
						'application/x-www-form-urlencoded'
				) {
					if (!options.body.includes('csrf_token=')) {
						options.body +=
							'&csrf_token=' +
							encodeURIComponent(window.csrfToken);
					}
				}
			}
			return originalFetch(url, options);
		};
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

	// Get CSRF token from meta tag or window variable
	const csrfToken =
		document
			.querySelector('meta[name="csrf-token"]')
			?.getAttribute('content') ||
		window.csrfToken ||
		window.adminConfig?.csrfToken ||
		'';

	// Build the full URL with site URL
	const fullEndpoint = (window.SITE_URL || '') + endpoint + id;

	fetch(fullEndpoint, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body:
			'csrf_token=' +
			encodeURIComponent(csrfToken) +
			'&status=' +
			encodeURIComponent(status),
	})
		.then((response) => response.json())
		.then((data) => {
			selectElement.classList.remove('loading');

			if (data.success) {
				selectElement.dataset.currentStatus = status;
				showNotification(
					data.message || type + ' status updated successfully',
					'success'
				);

				// Update any status badges or indicators
				updateStatusIndicators(selectElement, status);
			} else {
				selectElement.value = originalStatus;
				showNotification(
					data.message || 'Failed to update ' + type + ' status',
					'error'
				);
			}
		})
		.catch((error) => {
			selectElement.classList.remove('loading');
			selectElement.value = originalStatus;
			showNotification('Failed to update ' + type + ' status', 'error');
			console.error('Error updating ' + type + ' status:', error);
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

// Initialize payment status update functionality
function initializePaymentStatusUpdates() {
	const paymentStatusSelects = document.querySelectorAll(
		'.payment-status-select'
	);

	paymentStatusSelects.forEach((select) => {
		select.addEventListener('change', function () {
			const orderId = this.dataset.orderId;
			const newPaymentStatus = this.value;
			const currentPaymentStatus = this.dataset.currentPaymentStatus;

			if (newPaymentStatus !== currentPaymentStatus) {
				updatePaymentStatus(orderId, newPaymentStatus, this);
			}
		});
	});
}

// Update payment status
function updatePaymentStatus(id, paymentStatus, selectElement) {
	const originalPaymentStatus = selectElement.dataset.currentPaymentStatus;
	const endpoint = '/admin/orders/update-payment-status/';

	// Show loading state
	selectElement.classList.add('loading');

	// Get CSRF token from meta tag or window variable
	const csrfToken =
		document
			.querySelector('meta[name="csrf-token"]')
			?.getAttribute('content') ||
		window.csrfToken ||
		window.adminConfig?.csrfToken ||
		'';

	// Build the full URL with site URL
	const fullEndpoint = (window.SITE_URL || '') + endpoint + id;

	fetch(fullEndpoint, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body:
			'csrf_token=' +
			encodeURIComponent(csrfToken) +
			'&payment_status=' +
			encodeURIComponent(paymentStatus),
	})
		.then((response) => response.json())
		.then((data) => {
			selectElement.classList.remove('loading');

			if (data.success) {
				selectElement.dataset.currentPaymentStatus = paymentStatus;
				showNotification(
					data.message || 'Payment status updated successfully',
					'success'
				);

				// Update payment status styling based on the new status
				updatePaymentStatusStyling(selectElement, paymentStatus);
			} else {
				selectElement.value = originalPaymentStatus;
				showNotification(
					data.message || 'Failed to update payment status',
					'error'
				);
			}
		})
		.catch((error) => {
			selectElement.classList.remove('loading');
			selectElement.value = originalPaymentStatus;
			showNotification('Failed to update payment status', 'error');
			console.error('Error updating payment status:', error);
		});
}

// Update payment status styling
function updatePaymentStatusStyling(selectElement, paymentStatus) {
	// Remove existing styling classes
	selectElement.classList.remove(
		'text-warning',
		'text-success',
		'text-danger',
		'text-info'
	);

	// Apply styling based on payment status
	const statusClasses = {
		pending: 'text-warning',
		paid: 'text-success',
		failed: 'text-danger',
		refunded: 'text-info',
	};

	const statusClass = statusClasses[paymentStatus] || 'text-secondary';
	selectElement.classList.add(statusClass);
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
function refreshDashboard() {
	// Refresh the entire page for now - can be made more sophisticated later
	window.location.reload();
}

function refreshDashboardStats() {
	const url = (window.SITE_URL || '') + '/admin/dashboard/stats';
	fetch(url)
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				updateDashboardStats(data.stats);
			}
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
	// Use the dashboard stats endpoint instead of notifications
	fetch(window.SITE_URL + '/admin/dashboard/stats')
		.then((response) => response.json())
		.then((data) => {
			if (data && data.pending_orders > 0) {
				updateOrderBadge(data.pending_orders);
			}
			if (data && data.pending_bookings > 0) {
				updateBookingBadge(data.pending_bookings);
			}
		})
		.catch((error) => {
			console.log('Stats check failed:', error);
			// Don't show error to user for background updates
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

// Initialize notifications on load
document.addEventListener('DOMContentLoaded', function () {
	if (window.location.pathname.includes('/admin')) {
		initializeNotifications();
	}
});

// Advanced DataTables initialization with comprehensive configuration
function initializeDataTablesAdvanced() {
	// Use jQuery document ready to ensure DataTables is loaded
	$(document).ready(function () {
		if ($.fn.DataTable && $('table.dataTable').length > 0) {
			$('table.dataTable').each(function () {
				// Skip tables with data-dt-disable attribute
				if ($(this).attr('data-dt-disable')) {
					return;
				}

				const tableId = $(this).attr('id');

				// Check if DataTable is already initialized
				if (!$.fn.DataTable.isDataTable(this)) {
					let config = {
						responsive: true,
						pageLength: 10,
						processing: true,
						language: {
							search: '_INPUT_',
							searchPlaceholder: 'Search...',
							lengthMenu: 'Show _MENU_ entries',
							info: 'Showing _START_ to _END_ of _TOTAL_ entries',
							infoEmpty: 'No entries found',
							emptyTable: 'No data available in table',
							zeroRecords: 'No matching records found',
							paginate: {
								first: 'First',
								last: 'Last',
								next: 'Next',
								previous: 'Previous',
							},
						},
						drawCallback: function () {
							// Reinitialize tooltips after table redraw
							$('[data-bs-toggle="tooltip"]').tooltip();
						},
					};

					// Custom configuration for specific tables
					switch (tableId) {
						case 'usersTable':
						case 'foodsTable':
						case 'ordersTable':
						case 'categoriesTable':
							config.order = [[1, 'desc']]; // Sort by ID column
							config.columnDefs = [
								{
									targets: 0, // Checkbox column
									orderable: false,
									searchable: false,
									className: 'text-center',
								},
								{
									targets: -1, // Actions column
									orderable: false,
									searchable: false,
									className: 'text-center',
								},
							];
							break;
						default:
							// Default configuration for other tables
							config.order = [[0, 'desc']];
							break;
					}

					try {
						$(this).DataTable(config);
						console.log(
							`DataTable initialized successfully for ${
								tableId || 'unnamed table'
							}`
						);
					} catch (error) {
						console.error(
							`Failed to initialize DataTable for ${
								tableId || 'unnamed table'
							}:`,
							error
						);
					}
				}
			});
		}
	});
}

// DataTables debug function
function debugDataTables() {
	console.log('DataTables Debug Info:');
	$('table.dataTable').each(function (index) {
		const tableId = this.id || 'table-' + index;
		const isInitialized = $.fn.DataTable.isDataTable(this);
		console.log(
			`Table ${tableId}: ${
				isInitialized ? 'Initialized' : 'Not initialized'
			}`
		);
	});
}

// Make debug function available globally
window.debugDataTables = debugDataTables;

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

// News management functions
function searchNews() {
	const searchTerm = document.getElementById('searchInput')?.value;
	const url = new URL(window.location);
	if (searchTerm) {
		url.searchParams.set('search', searchTerm);
	} else {
		url.searchParams.delete('search');
	}
	window.location = url;
}

function toggleNewsStatus(newsId, newStatus) {
	if (
		!confirm(
			`Are you sure you want to ${
				newStatus === 'published' ? 'publish' : 'unpublish'
			} this article?`
		)
	) {
		return;
	}

	fetch(window.SITE_URL + '/admin/news/toggle-status', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-Token': window.adminConfig.csrfToken,
		},
		body: JSON.stringify({
			id: newsId,
			status: newStatus,
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showNotification(
					'Article status updated successfully',
					'success'
				);
				setTimeout(() => location.reload(), 1000);
			} else {
				showNotification(
					data.message || 'Failed to update status',
					'error'
				);
			}
		})
		.catch((error) => {
			showNotification('An error occurred', 'error');
		});
}

function deleteNews(newsId) {
	if (
		!confirm(
			'Are you sure you want to delete this article? This action cannot be undone.'
		)
	) {
		return;
	}

	// Create a form to submit the delete request properly
	const form = document.createElement('form');
	form.method = 'POST';
	form.action = window.SITE_URL + '/admin/news/delete/' + newsId;

	// Add CSRF token
	const csrfInput = document.createElement('input');
	csrfInput.type = 'hidden';
	csrfInput.name = 'csrf_token';
	csrfInput.value =
		window.adminConfig.csrfToken ||
		document
			.querySelector('meta[name="csrf-token"]')
			?.getAttribute('content') ||
		'';
	form.appendChild(csrfInput);

	// Add the form to the document and submit it
	document.body.appendChild(form);
	form.submit();
}

// Export news function
function exportNews() {
	const format = prompt(
		'Choose export format:\n1. CSV\n2. JSON\nEnter 1 or 2:',
		'1'
	);

	if (!format || (format !== '1' && format !== '2')) {
		return;
	}

	const exportFormat = format === '1' ? 'csv' : 'json';

	// Create download link
	const link = document.createElement('a');
	link.href = window.SITE_URL + '/admin/news/export?format=' + exportFormat;
	link.download = 'news_export.' + exportFormat;
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}

// Toggle news status (publish/unpublish)
function toggleStatus(newsId, newStatus) {
	if (
		!confirm('Are you sure you want to change the status of this article?')
	) {
		return;
	}

	const form = document.createElement('form');
	form.method = 'POST';
	form.action = window.SITE_URL + '/admin/news/toggle-status';

	// Add CSRF token
	const csrfInput = document.createElement('input');
	csrfInput.type = 'hidden';
	csrfInput.name = 'csrf_token';
	csrfInput.value =
		window.adminConfig.csrfToken ||
		document
			.querySelector('meta[name="csrf-token"]')
			?.getAttribute('content') ||
		'';
	form.appendChild(csrfInput);

	// Add news ID
	const idInput = document.createElement('input');
	idInput.type = 'hidden';
	idInput.name = 'id';
	idInput.value = newsId;
	form.appendChild(idInput);

	// Add new status
	const statusInput = document.createElement('input');
	statusInput.type = 'hidden';
	statusInput.name = 'status';
	statusInput.value = newStatus;
	form.appendChild(statusInput);

	document.body.appendChild(form);
	form.submit();
}

// Execute bulk actions on selected news
function executeBulkAction() {
	const action = document.getElementById('bulkAction').value;
	const checkboxes = document.querySelectorAll('.news-checkbox:checked');

	if (!action) {
		alert('Please select an action');
		return;
	}

	if (checkboxes.length === 0) {
		alert('Please select at least one article');
		return;
	}

	const actionText =
		action === 'delete'
			? 'delete'
			: action === 'publish'
			? 'publish'
			: 'unpublish';

	if (
		!confirm(
			`Are you sure you want to ${actionText} ${checkboxes.length} selected article(s)?`
		)
	) {
		return;
	}

	const form = document.createElement('form');
	form.method = 'POST';
	form.action = window.SITE_URL + '/admin/news/bulk-action';

	// Add CSRF token
	const csrfInput = document.createElement('input');
	csrfInput.type = 'hidden';
	csrfInput.name = 'csrf_token';
	csrfInput.value =
		window.adminConfig.csrfToken ||
		document
			.querySelector('meta[name="csrf-token"]')
			?.getAttribute('content') ||
		'';
	form.appendChild(csrfInput);

	// Add action
	const actionInput = document.createElement('input');
	actionInput.type = 'hidden';
	actionInput.name = 'action';
	actionInput.value = action;
	form.appendChild(actionInput);

	// Add selected IDs
	checkboxes.forEach((checkbox) => {
		const idInput = document.createElement('input');
		idInput.type = 'hidden';
		idInput.name = 'ids[]';
		idInput.value = checkbox.value;
		form.appendChild(idInput);
	});

	document.body.appendChild(form);
	form.submit();
}

// Toggle all checkboxes
function toggleAllCheckboxes() {
	const selectAll = document.getElementById('selectAll');
	const checkboxes = document.querySelectorAll('.news-checkbox');

	checkboxes.forEach((checkbox) => {
		checkbox.checked = selectAll.checked;
	});
}

// Clear filters
function clearFilters() {
	document.getElementById('filterForm').reset();
	window.location.href = window.SITE_URL + '/admin/news';
}

// Apply filters
function applyFilters() {
	document.getElementById('filterForm').submit();
}

// ...existing code...
