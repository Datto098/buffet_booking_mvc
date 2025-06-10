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
}

/**
 * Initialize dashboard charts
 * @param {Object} chartData - Chart data containing revenue and booking statistics
 */
function initializeCharts(chartData) {
	// Initialize Revenue Chart (Line Chart)
	initializeRevenueChart(chartData.monthly_revenue_data);

	// Initialize Booking Status Chart (Pie Chart)
	initializeBookingStatusChart(chartData.booking_stats);
}

/**
 * Initialize revenue trend line chart
 * @param {Array} revenueData - Monthly revenue data for the past 12 months
 */
function initializeRevenueChart(revenueData) {
	const ctx = document.getElementById('revenueChart');
	if (!ctx) return;

	// Generate month labels for the past 12 months
	const monthLabels = [];
	const currentDate = new Date();
	for (let i = 11; i >= 0; i--) {
		const date = new Date(
			currentDate.getFullYear(),
			currentDate.getMonth() - i,
			1
		);
		monthLabels.push(
			date.toLocaleDateString('en-US', {
				month: 'short',
				year: '2-digit',
			})
		);
	}

	new Chart(ctx, {
		type: 'line',
		data: {
			labels: monthLabels,
			datasets: [
				{
					label: 'Revenue ($)',
					data: revenueData || Array(12).fill(0),
					borderColor: '#4e73df',
					backgroundColor: 'rgba(78, 115, 223, 0.1)',
					borderWidth: 2,
					fill: true,
					tension: 0.3,
					pointBackgroundColor: '#4e73df',
					pointBorderColor: '#ffffff',
					pointBorderWidth: 2,
					pointRadius: 4,
					pointHoverRadius: 6,
				},
			],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					display: false,
				},
				tooltip: {
					backgroundColor: 'rgba(0, 0, 0, 0.8)',
					titleColor: '#ffffff',
					bodyColor: '#ffffff',
					borderColor: '#4e73df',
					borderWidth: 1,
					callbacks: {
						label: function (context) {
							return (
								'Revenue: $' + context.parsed.y.toLocaleString()
							);
						},
					},
				},
			},
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						callback: function (value) {
							return '$' + value.toLocaleString();
						},
					},
					grid: {
						color: 'rgba(0, 0, 0, 0.1)',
					},
				},
				x: {
					grid: {
						display: false,
					},
				},
			},
			interaction: {
				intersect: false,
				mode: 'index',
			},
		},
	});
}

/**
 * Initialize booking status pie chart
 * @param {Object} bookingStats - Booking statistics (confirmed, pending, cancelled)
 */
function initializeBookingStatusChart(bookingStats) {
	const ctx = document.getElementById('bookingStatusChart');
	if (!ctx) return;

	const stats = bookingStats || { confirmed: 0, pending: 0, cancelled: 0 };
	const total = stats.confirmed + stats.pending + stats.cancelled;

	// Don't show chart if no data
	if (total === 0) {
		ctx.parentElement.innerHTML =
			'<div class="text-center py-4"><i class="fas fa-chart-pie fa-3x text-muted mb-3"></i><br><span class="text-muted">No booking data available</span></div>';
		return;
	}

	new Chart(ctx, {
		type: 'doughnut',
		data: {
			labels: ['Confirmed', 'Pending', 'Cancelled'],
			datasets: [
				{
					data: [stats.confirmed, stats.pending, stats.cancelled],
					backgroundColor: [
						'#1cc88a', // Success green
						'#f6c23e', // Warning yellow
						'#e74a3b', // Danger red
					],
					borderColor: ['#17a673', '#dda20a', '#c0392b'],
					borderWidth: 2,
					hoverOffset: 4,
				},
			],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					position: 'bottom',
					labels: {
						padding: 20,
						usePointStyle: true,
						font: {
							size: 12,
						},
					},
				},
				tooltip: {
					backgroundColor: 'rgba(0, 0, 0, 0.8)',
					titleColor: '#ffffff',
					bodyColor: '#ffffff',
					callbacks: {
						label: function (context) {
							const percentage = (
								(context.parsed / total) *
								100
							).toFixed(1);
							return (
								context.label +
								': ' +
								context.parsed +
								' (' +
								percentage +
								'%)'
							);
						},
					},
				},
			},
			cutout: '60%',
			animation: {
				animateRotate: true,
				duration: 1000,
			},
		},
	});
}

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
	// Show loading state
	const button = event.target;
	const originalText = button.innerHTML;
	button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
	button.disabled = true;

	// Reload the page to get fresh data
	setTimeout(() => {
		window.location.reload();
	}, 500);
}

/**
 * Export chart data
 * @param {string} chartType - Type of chart to export ('revenue', 'bookings', etc.)
 */
function exportChart(chartType) {
	showNotification('Exporting ' + chartType + ' chart data...', 'info');

	try {
		// Create export data based on chart type
		let exportData = [];
		let filename = '';

		switch (chartType) {
			case 'revenue':
				// Get revenue chart data
				const revenueChart = Chart.getChart('revenueChart');
				if (revenueChart) {
					const labels = revenueChart.data.labels;
					const data = revenueChart.data.datasets[0].data;
					exportData = labels.map((label, index) => ({
						Month: label,
						Revenue: data[index] || 0,
					}));
					filename = 'revenue_chart_data.csv';
				}
				break;
			case 'bookings':
				// Get booking status chart data
				const bookingChart = Chart.getChart('bookingStatusChart');
				if (bookingChart) {
					const labels = bookingChart.data.labels;
					const data = bookingChart.data.datasets[0].data;
					exportData = labels.map((label, index) => ({
						Status: label,
						Count: data[index] || 0,
					}));
					filename = 'booking_status_chart_data.csv';
				}
				break;
			default:
				showNotification('Unknown chart type: ' + chartType, 'error');
				return;
		}

		if (exportData.length === 0) {
			showNotification('No data available to export', 'warning');
			return;
		}

		// Convert to CSV
		const csv = convertToCSV(exportData);

		// Download CSV file
		downloadCSV(csv, filename);

		showNotification('Chart data exported successfully!', 'success');
	} catch (error) {
		console.error('Error exporting chart:', error);
		showNotification('Error exporting chart data', 'error');
	}
}

/**
 * Convert array of objects to CSV string
 * @param {Array} data - Array of objects to convert
 * @returns {string} CSV string
 */
function convertToCSV(data) {
	if (!data || data.length === 0) return '';

	// Get headers from first object
	const headers = Object.keys(data[0]);

	// Create CSV content
	const csvContent = [
		headers.join(','), // Header row
		...data.map((row) =>
			headers
				.map((header) => {
					const value = row[header];
					// Escape commas and quotes in values
					return typeof value === 'string' &&
						(value.includes(',') || value.includes('"'))
						? '"' + value.replace(/"/g, '""') + '"'
						: value;
				})
				.join(',')
		),
	].join('\n');

	return csvContent;
}

/**
 * Download CSV data as file
 * @param {string} csvContent - CSV content to download
 * @param {string} filename - Name of the file to download
 */
function downloadCSV(csvContent, filename) {
	const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
	const link = document.createElement('a');

	if (link.download !== undefined) {
		// Modern browsers
		const url = URL.createObjectURL(blob);
		link.setAttribute('href', url);
		link.setAttribute('download', filename);
		link.style.visibility = 'hidden';
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
		URL.revokeObjectURL(url);
	} else {
		// Fallback for older browsers
		window.open(
			'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent)
		);
	}
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

// =============================================================================
// ORDER MANAGEMENT FUNCTIONS
// =============================================================================

/**
 * View order details in modal
 * @param {number} orderId - Order ID to view
 */
function viewOrderDetails(orderId) {
	if (!orderId || orderId <= 0) {
		showNotification('Invalid order ID', 'error');
		return;
	}

	// Show loading in modal
	const modal = document.getElementById('orderDetailsModal');
	const modalBody = document.getElementById('orderDetailsContent');

	if (!modal || !modalBody) {
		showNotification('Modal not found', 'error');
		return;
	}

	// Show loading spinner
	modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading order details...</span>
            </div>
            <p class="mt-2 text-muted">Loading order details...</p>
        </div>
    `;

	// Show the modal
	const bsModal = new bootstrap.Modal(modal);
	bsModal.show(); // Fetch order details
	fetch(`${window.SITE_URL}/admin/orders/details/${orderId}`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
			'X-CSRF-Token': window.adminConfig.csrfToken,
		},
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.text();
		})
		.then((html) => {
			modalBody.innerHTML = html;

			// Update modal title with order ID
			const orderIdMatch = html.match(/#(\d+)/);
			if (orderIdMatch) {
				const modalTitle = modal.querySelector('.modal-title');
				if (modalTitle) {
					modalTitle.innerHTML = `<i class="fas fa-receipt"></i> Order Details #${orderIdMatch[1]}`;
				}
			}
		})
		.catch((error) => {
			console.error('Error loading order details:', error);
			modalBody.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error loading order details:</strong> ${error.message}
                <br><br>
                <button class="btn btn-outline-danger btn-sm" onclick="viewOrderDetails(${orderId})">
                    <i class="fas fa-retry"></i> Try Again
                </button>
            </div>
        `;
		});
}

/**
 * Print order receipt
 * @param {number} orderId - Order ID to print
 */
function printOrder(orderId) {
	if (!orderId || orderId <= 0) {
		showNotification('Invalid order ID', 'error');
		return;
	}

	// Open print window
	const printWindow = window.open(
		'',
		'_blank',
		'width=800,height=600,scrollbars=yes'
	);

	if (!printWindow) {
		showNotification(
			'Popup blocked. Please allow popups for this site.',
			'error'
		);
		return;
	}

	// Show loading in print window
	printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Loading Order Receipt...</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; margin: 20px auto; }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
        </head>
        <body>
            <h3>Loading Order Receipt...</h3>
            <div class="spinner"></div>
            <p>Please wait while we prepare your order receipt.</p>
        </body>
        </html>
    `); // Fetch printable order
	fetch(`${window.SITE_URL}/admin/orders/print/${orderId}`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
			'X-CSRF-Token': window.adminConfig.csrfToken,
		},
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.text();
		})
		.then((html) => {
			printWindow.document.open();
			printWindow.document.write(html);
			printWindow.document.close();

			// Auto-print after content loads
			printWindow.onload = function () {
				setTimeout(() => {
					printWindow.print();
				}, 500);
			};
		})
		.catch((error) => {
			console.error('Error loading print order:', error);
			printWindow.document.open();
			printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head><title>Print Error</title></head>
            <body style="font-family: Arial, sans-serif; padding: 20px;">
                <h3 style="color: #dc3545;">Error Loading Order Receipt</h3>
                <p><strong>Error:</strong> ${error.message}</p>
                <p>Please try again or contact support if the problem persists.</p>
                <button onclick="window.close()">Close Window</button>
            </body>
            </html>
        `);
			printWindow.document.close();
		});
}

/**
 * Print current order from modal
 */
function printCurrentOrder() {
	const modal = document.getElementById('orderDetailsModal');
	const orderIdMatch = modal
		?.querySelector('.modal-title')
		?.textContent?.match(/#(\d+)/);

	if (orderIdMatch && orderIdMatch[1]) {
		printOrder(parseInt(orderIdMatch[1]));
	} else {
		showNotification('No order selected for printing', 'error');
	}
}

/**
 * Export orders to CSV
 */
function exportOrders() {
	// Get current filter values
	const statusFilter = document.getElementById('statusFilter')?.value || '';
	const searchFilter = document.getElementById('searchOrders')?.value || '';
	const dateFromFilter = document.getElementById('dateFrom')?.value || '';
	const dateToFilter = document.getElementById('dateTo')?.value || '';

	// Build query parameters
	const params = new URLSearchParams();
	if (statusFilter) params.append('status', statusFilter);
	if (searchFilter) params.append('search', searchFilter);
	if (dateFromFilter) params.append('date_from', dateFromFilter);
	if (dateToFilter) params.append('date_to', dateToFilter); // Create download URL
	const downloadUrl = `${
		window.SITE_URL
	}/admin/orders/export-csv?${params.toString()}`;

	// Create temporary link and trigger download
	const link = document.createElement('a');
	link.href = downloadUrl;
	link.download = `orders_export_${
		new Date().toISOString().split('T')[0]
	}.csv`;
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);

	showNotification(
		'Orders export started. Download will begin shortly.',
		'success'
	);
}

/**
 * Edit order (placeholder function)
 * @param {number} orderId - Order ID to edit
 */
function editOrder(orderId) {
	showNotification('Edit order functionality coming soon', 'info');
	// TODO: Implement edit order functionality
}

/**
 * Delete order with confirmation
 * @param {number} orderId - Order ID to delete
 */
function deleteOrder(orderId) {
	if (!orderId || orderId <= 0) {
		showNotification('Invalid order ID', 'error');
		return;
	}

	// Show confirmation dialog
	if (
		!confirm(
			`Are you sure you want to delete order #${orderId}? This action cannot be undone.`
		)
	) {
		return;
	}

	// Show loading state
	const button = event.target.closest('button') || event.target;
	const originalText = button.innerHTML;
	button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
	button.disabled = true;

	fetch(`${window.SITE_URL}/admin/orders/delete/${orderId}`, {
		method: 'DELETE',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-Token': window.adminConfig.csrfToken,
		},
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showNotification('Order deleted successfully', 'success');
				// Remove the row from table or refresh page
				setTimeout(() => location.reload(), 1000);
			} else {
				throw new Error(data.message || 'Failed to delete order');
			}
		})
		.catch((error) => {
			console.error('Error deleting order:', error);
			showNotification('Error deleting order: ' + error.message, 'error');
		})
		.finally(() => {
			button.innerHTML = originalText;
			button.disabled = false;
		});
}

/**
 * Duplicate order (placeholder function)
 * @param {number} orderId - Order ID to duplicate
 */
function duplicateOrder(orderId) {
	showNotification('Duplicate order functionality coming soon', 'info');
	// TODO: Implement duplicate order functionality
}

/**
 * Delete table with confirmation
 * @param {number} tableId - Table ID to delete
 */
function deleteTable(tableId) {
	if (!tableId || tableId <= 0) {
		showNotification('Invalid table ID', 'error');
		return;
	}

	// Show confirmation dialog
	if (
		!confirm(
			`Are you sure you want to delete this table? This action cannot be undone and may affect existing bookings.`
		)
	) {
		return;
	}

	// Show loading state
	const button = event.target.closest('button') || event.target;
	const originalText = button.innerHTML;
	button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
	button.disabled = true;

	fetch(`${window.SITE_URL}/admin/tables/delete/${tableId}`, {
		method: 'DELETE',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-Token': window.adminConfig.csrfToken,
		},
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showNotification('Table deleted successfully', 'success');
				// Remove the row from table or refresh page
				setTimeout(() => location.reload(), 1000);
			} else {
				throw new Error(data.message || 'Failed to delete table');
			}
		})
		.catch((error) => {
			console.error('Error deleting table:', error);
			showNotification('Error deleting table: ' + error.message, 'error');
		})
		.finally(() => {
			button.innerHTML = originalText;
			button.disabled = false;
		});
}

/**
 * Send order email (placeholder function)
 * @param {number} orderId - Order ID to send email for
 */
function sendOrderEmail(orderId) {
	showNotification('Send order email functionality coming soon', 'info');
	// TODO: Implement send order email functionality
}

/**
 * Refresh dashboard data
 */
function refreshDashboard() {
	// Show loading state
	const button = event.target;
	const originalText = button.innerHTML;
	button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
	button.disabled = true;

	// Reload the page to get fresh data
	setTimeout(() => {
		window.location.reload();
	}, 500);
}

/**
 * Export chart data
 * @param {string} chartType - Type of chart to export ('revenue', 'bookings', etc.)
 */
function exportChart(chartType) {
	showNotification('Exporting ' + chartType + ' chart data...', 'info');

	try {
		// Create export data based on chart type
		let exportData = [];
		let filename = '';

		switch (chartType) {
			case 'revenue':
				// Get revenue chart data
				const revenueChart = Chart.getChart('revenueChart');
				if (revenueChart) {
					const labels = revenueChart.data.labels;
					const data = revenueChart.data.datasets[0].data;
					exportData = labels.map((label, index) => ({
						Month: label,
						Revenue: data[index] || 0,
					}));
					filename = 'revenue_chart_data.csv';
				}
				break;
			case 'bookings':
				// Get booking status chart data
				const bookingChart = Chart.getChart('bookingStatusChart');
				if (bookingChart) {
					const labels = bookingChart.data.labels;
					const data = bookingChart.data.datasets[0].data;
					exportData = labels.map((label, index) => ({
						Status: label,
						Count: data[index] || 0,
					}));
					filename = 'booking_status_chart_data.csv';
				}
				break;
			default:
				showNotification('Unknown chart type: ' + chartType, 'error');
				return;
		}

		if (exportData.length === 0) {
			showNotification('No data available to export', 'warning');
			return;
		}

		// Convert to CSV
		const csv = convertToCSV(exportData);

		// Download CSV file
		downloadCSV(csv, filename);

		showNotification('Chart data exported successfully!', 'success');
	} catch (error) {
		console.error('Error exporting chart:', error);
		showNotification('Error exporting chart data', 'error');
	}
}

/**
 * Convert array of objects to CSV string
 * @param {Array} data - Array of objects to convert
 * @returns {string} CSV string
 */
function convertToCSV(data) {
	if (!data || data.length === 0) return '';

	// Get headers from first object
	const headers = Object.keys(data[0]);

	// Create CSV content
	const csvContent = [
		headers.join(','), // Header row
		...data.map((row) =>
			headers
				.map((header) => {
					const value = row[header];
					// Escape commas and quotes in values
					return typeof value === 'string' &&
						(value.includes(',') || value.includes('"'))
						? '"' + value.replace(/"/g, '""') + '"'
						: value;
				})
				.join(',')
		),
	].join('\n');

	return csvContent;
}

/**
 * Download CSV data as file
 * @param {string} csvContent - CSV content to download
 * @param {string} filename - Name of the file to download
 */
function downloadCSV(csvContent, filename) {
	const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
	const link = document.createElement('a');

	if (link.download !== undefined) {
		// Modern browsers
		const url = URL.createObjectURL(blob);
		link.setAttribute('href', url);
		link.setAttribute('download', filename);
		link.style.visibility = 'hidden';
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
		URL.revokeObjectURL(url);
	} else {
		// Fallback for older browsers
		window.open(
			'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent)
		);
	}
}

// =============================================================================
// ORDER STATUS MANAGEMENT
// =============================================================================

/**
 * Initialize order status update functionality
 */
function initializeOrderStatusUpdates() {
	const statusSelects = document.querySelectorAll('.status-select');

	statusSelects.forEach((select) => {
		select.addEventListener('change', function () {
			const orderId = this.dataset.orderId;
			const newStatus = this.value;
			const oldStatus = this.dataset.currentStatus;

			updateOrderStatus(orderId, newStatus, oldStatus, this);
		});
	});
}

/**
 * Update order status
 * @param {number} orderId - Order ID
 * @param {string} newStatus - New status
 * @param {string} oldStatus - Old status for rollback
 * @param {HTMLElement} selectElement - The select element
 */
function updateOrderStatus(orderId, newStatus, oldStatus, selectElement) {
	// Disable the select during update
	selectElement.disabled = true;

	// Get CSRF token from meta tag or window variable
	const csrfToken =
		document
			.querySelector('meta[name="csrf-token"]')
			?.getAttribute('content') ||
		window.csrfToken ||
		window.adminConfig?.csrfToken ||
		'';

	fetch(`${window.SITE_URL}/admin/orders/update-status/${orderId}`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: `csrf_token=${encodeURIComponent(
			csrfToken
		)}&status=${encodeURIComponent(newStatus)}`,
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showNotification(
					`Order #${orderId} status updated to ${newStatus}`,
					'success'
				);
				selectElement.dataset.currentStatus = newStatus;

				// Update any visual indicators (badges, etc.)
				updateOrderStatusDisplay(orderId, newStatus);
			} else {
				throw new Error(data.message || 'Failed to update status');
			}
		})
		.catch((error) => {
			console.error('Error updating order status:', error);
			showNotification(
				'Error updating order status: ' + error.message,
				'error'
			);

			// Rollback the select value
			selectElement.value = oldStatus;
		})
		.finally(() => {
			selectElement.disabled = false;
		});
}

/**
 * Update order status display elements
 * @param {number} orderId - Order ID
 * @param {string} status - New status
 */
function updateOrderStatusDisplay(orderId, status) {
	// Update any status badges or indicators in the row
	const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
	if (row) {
		const statusBadge = row.querySelector('.status-badge');
		if (statusBadge) {
			// Update badge classes and text based on status
			statusBadge.className = `badge status-badge bg-${getStatusColor(
				status
			)}`;
			statusBadge.textContent =
				status.charAt(0).toUpperCase() + status.slice(1);
		}
	}
}

/**
 * Get appropriate color class for order status
 * @param {string} status - Order status
 * @returns {string} Bootstrap color class
 */
function getStatusColor(status) {
	const statusColors = {
		pending: 'warning',
		confirmed: 'info',
		preparing: 'primary',
		ready: 'success',
		completed: 'success',
		delivered: 'success',
		cancelled: 'danger',
	};

	return statusColors[status] || 'secondary';
}

// Initialize order management when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
	if (window.location.pathname.includes('/admin/orders')) {
		initializeOrderStatusUpdates();
	}

	// Initialize booking management
	if (window.location.pathname.includes('/admin/bookings')) {
		initializeBookingManagement();
	}
});

// ============= BOOKING MANAGEMENT FUNCTIONS =============

/**
 * Initialize booking management functionality
 */
function initializeBookingManagement() {
	// Initialize search functionality
	const searchInput = document.getElementById('searchBookings');
	if (searchInput) {
		searchInput.addEventListener('input', debounce(searchBookings, 300));
	}

	// Initialize bulk selection
	initializeBulkBookingSelection();

	// Initialize tooltips for special requests
	var tooltipTriggerList = [].slice.call(
		document.querySelectorAll('[data-bs-toggle="tooltip"]')
	);
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});
}

/**
 * View booking details in modal
 */
function viewBookingDetails(bookingId) {
	const modal = new bootstrap.Modal(
		document.getElementById('bookingDetailsModal')
	);
	const modalContent = document.getElementById('bookingDetailsContent');

	// Show loading state
	modalContent.innerHTML = `
		<div class="text-center p-4">
			<div class="spinner-border text-primary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
			<p class="mt-2">Loading booking details...</p>
		</div>
	`;

	modal.show();

	// Fetch booking details
	fetch(`${window.SITE_URL}/admin/bookings/details/${bookingId}`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
		},
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				modalContent.innerHTML = formatBookingDetails(data.booking);
			} else {
				modalContent.innerHTML = `
				<div class="alert alert-danger">
					<i class="fas fa-exclamation-triangle"></i>
					Error loading booking details: ${data.message || 'Unknown error'}
				</div>
			`;
			}
		})
		.catch((error) => {
			console.error('Error fetching booking details:', error);
			modalContent.innerHTML = `
			<div class="alert alert-danger">
				<i class="fas fa-exclamation-triangle"></i>
				Failed to load booking details. Please try again.
			</div>
		`;
		});
}

/**
 * Format booking details for display in modal
 */
function formatBookingDetails(booking) {
	const statusBadge = getBookingStatusBadge(booking.status);
	const bookingDate = new Date(
		booking.reservation_time ||
			booking.booking_date + ' ' + booking.booking_time
	);

	return `
		<div class="row">
			<div class="col-md-8">
				<h6 class="text-primary mb-3">
					<i class="fas fa-user"></i> Customer Information
				</h6>
				<div class="row mb-3">
					<div class="col-sm-6">
						<strong>Name:</strong><br>
						${booking.customer_name || 'N/A'}
					</div>
					<div class="col-sm-6">
						<strong>Email:</strong><br>
						${booking.customer_email || booking.email || 'N/A'}
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-6">
						<strong>Phone:</strong><br>
						${booking.phone_number || 'N/A'}
					</div>
					<div class="col-sm-6">
						<strong>Guests:</strong><br>
						<span class="badge bg-light text-dark">
							<i class="fas fa-users"></i> ${booking.number_of_guests || booking.party_size}
						</span>
					</div>
				</div>

				<h6 class="text-primary mb-3 mt-4">
					<i class="fas fa-calendar-alt"></i> Reservation Details
				</h6>
				<div class="row mb-3">
					<div class="col-sm-6">
						<strong>Date:</strong><br>
						${bookingDate.toLocaleDateString('en-US', {
							weekday: 'long',
							year: 'numeric',
							month: 'long',
							day: 'numeric',
						})}
					</div>
					<div class="col-sm-6">
						<strong>Time:</strong><br>
						${bookingDate.toLocaleTimeString('en-US', {
							hour: '2-digit',
							minute: '2-digit',
						})}
					</div>
				</div>

				${
					booking.table_number
						? `
				<div class="mb-3">
					<strong>Table Assignment:</strong><br>
					<span class="badge bg-info">Table ${booking.table_number}</span>
				</div>
				`
						: ''
				}

				${
					booking.special_requests
						? `
				<div class="mb-3">
					<strong>Special Requests:</strong><br>
					<div class="bg-light p-2 rounded">${booking.special_requests}</div>
				</div>
				`
						: ''
				}
			</div>

			<div class="col-md-4">
				<h6 class="text-primary mb-3">
					<i class="fas fa-info-circle"></i> Booking Status
				</h6>
				<div class="mb-3">
					<strong>Current Status:</strong><br>
					${statusBadge}
				</div>
				<div class="mb-3">
					<strong>Booking ID:</strong><br>
					<code>#${booking.id}</code>
				</div>
				<div class="mb-3">
					<strong>Created:</strong><br>
					<small class="text-muted">
						${new Date(booking.created_at).toLocaleString()}
					</small>
				</div>

				<hr>

				<div class="d-grid gap-2">
					<button class="btn btn-outline-primary btn-sm" onclick="editBooking(${
						booking.id
					})">
						<i class="fas fa-edit"></i> Edit Booking
					</button>

					${
						booking.status === 'pending'
							? `
					<button class="btn btn-success btn-sm" onclick="updateBookingStatus(${booking.id}, 'confirmed')">
						<i class="fas fa-check"></i> Confirm
					</button>
					`
							: ''
					}

					${
						['pending', 'confirmed'].includes(booking.status)
							? `
					<button class="btn btn-info btn-sm" onclick="assignTable(${booking.id})">
						<i class="fas fa-table"></i> Assign Table
					</button>
					<button class="btn btn-danger btn-sm" onclick="updateBookingStatus(${booking.id}, 'cancelled')">
						<i class="fas fa-times"></i> Cancel
					</button>
					`
							: ''
					}

					<button class="btn btn-outline-secondary btn-sm" onclick="sendConfirmationEmail(${
						booking.id
					})">
						<i class="fas fa-envelope"></i> Send Email
					</button>
				</div>
			</div>
		</div>
	`;
}

/**
 * Get booking status badge HTML
 */
function getBookingStatusBadge(status) {
	const statusConfig = {
		pending: { class: 'warning', icon: 'clock', text: 'Pending' },
		confirmed: {
			class: 'success',
			icon: 'check-circle',
			text: 'Confirmed',
		},
		seated: { class: 'info', icon: 'chair', text: 'Seated' },
		completed: { class: 'primary', icon: 'star', text: 'Completed' },
		cancelled: { class: 'danger', icon: 'times-circle', text: 'Cancelled' },
		no_show: { class: 'dark', icon: 'user-times', text: 'No Show' },
	};

	const config = statusConfig[status] || {
		class: 'secondary',
		icon: 'circle',
		text: 'Unknown',
	};

	return `
		<span class="badge bg-${config.class}">
			<i class="fas fa-${config.icon}"></i> ${config.text}
		</span>
	`;
}

/**
 * Update booking status
 */
function updateBookingStatus(bookingId, newStatus) {
	if (
		!confirm(
			`Are you sure you want to change this booking status to "${newStatus}"?`
		)
	) {
		return;
	}

	const csrfToken = document
		.querySelector('meta[name="csrf-token"]')
		.getAttribute('content');

	fetch(`${window.SITE_URL}/admin/bookings/update-status/${bookingId}`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: `csrf_token=${encodeURIComponent(
			csrfToken
		)}&status=${encodeURIComponent(newStatus)}`,
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showAlert('Booking status updated successfully', 'success');
				// Refresh the page to show updated status
				setTimeout(() => location.reload(), 1500);
			} else {
				showAlert(
					data.message || 'Failed to update booking status',
					'error'
				);
			}
		})
		.catch((error) => {
			console.error('Error updating booking status:', error);
			showAlert(
				'An error occurred while updating the booking status',
				'error'
			);
		});
}

/**
 * Assign table to booking
 */
function assignTable(bookingId) {
	const modal = new bootstrap.Modal(
		document.getElementById('tableAssignModal')
	);
	document.getElementById('bookingIdForTable').value = bookingId;

	// Load available tables
	loadAvailableTables(bookingId);

	modal.show();
}

/**
 * Load available tables for assignment
 */
function loadAvailableTables(bookingId) {
	const tableSelect = document.getElementById('tableNumber');
	tableSelect.innerHTML = '<option value="">Loading tables...</option>';

	fetch(
		`${window.SITE_URL}/admin/bookings/available-tables?booking_id=${bookingId}`
	)
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				tableSelect.innerHTML =
					'<option value="">Select Table</option>';
				data.tables.forEach((table) => {
					const option = document.createElement('option');
					option.value = table.id;
					option.textContent = `Table ${table.table_number} (${table.capacity} seats)`;
					tableSelect.appendChild(option);
				});
			} else {
				tableSelect.innerHTML =
					'<option value="">No tables available</option>';
			}
		})
		.catch((error) => {
			console.error('Error loading tables:', error);
			tableSelect.innerHTML =
				'<option value="">Error loading tables</option>';
		});
}

/**
 * Save table assignment
 */
function saveTableAssignment() {
	const bookingId = document.getElementById('bookingIdForTable').value;
	const tableId = document.getElementById('tableNumber').value;
	const notes = document.getElementById('tableNotes').value;

	if (!tableId) {
		showAlert('Please select a table', 'warning');
		return;
	}

	const csrfToken = document
		.querySelector('meta[name="csrf-token"]')
		.getAttribute('content');

	fetch(`${window.SITE_URL}/admin/bookings/assign-table`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			csrf_token: csrfToken,
			booking_id: parseInt(bookingId),
			table_id: parseInt(tableId),
			notes: notes,
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showAlert('Table assigned successfully', 'success');
				const modal = bootstrap.Modal.getInstance(
					document.getElementById('tableAssignModal')
				);
				modal.hide();
				setTimeout(() => location.reload(), 1500);
			} else {
				showAlert(data.message || 'Failed to assign table', 'error');
			}
		})
		.catch((error) => {
			console.error('Error assigning table:', error);
			showAlert('An error occurred while assigning the table', 'error');
		});
}

/**
 * Toggle bulk actions display
 */
function toggleBulkActions() {
	const bulkActionsBar = document.getElementById('bulkActionsBar');
	if (bulkActionsBar) {
		bulkActionsBar.style.display =
			bulkActionsBar.style.display === 'none' ? 'block' : 'none';
	}
}

/**
 * Edit booking - redirect to edit page
 */
function editBooking(bookingId) {
	window.location.href = `${window.SITE_URL}/admin/bookings/edit/${bookingId}`;
}

/**
 * Initialize bulk booking selection functionality
 */
function initializeBulkBookingSelection() {
	// Initialize select all checkbox
	const selectAllCheckbox = document.getElementById('selectAll');
	if (selectAllCheckbox) {
		selectAllCheckbox.addEventListener('change', function () {
			const bookingCheckboxes =
				document.querySelectorAll('.booking-checkbox');
			bookingCheckboxes.forEach((checkbox) => {
				checkbox.checked = this.checked;
			});
			updateSelectedCount();
			toggleBulkActionsVisibility();
		});
	}

	// Initialize individual checkboxes
	const bookingCheckboxes = document.querySelectorAll('.booking-checkbox');
	bookingCheckboxes.forEach((checkbox) => {
		checkbox.addEventListener('change', function () {
			updateSelectedCount();
			toggleBulkActionsVisibility();

			// Update select all checkbox state
			const totalCheckboxes = bookingCheckboxes.length;
			const checkedCheckboxes = document.querySelectorAll(
				'.booking-checkbox:checked'
			).length;

			if (selectAllCheckbox) {
				selectAllCheckbox.checked =
					checkedCheckboxes === totalCheckboxes;
				selectAllCheckbox.indeterminate =
					checkedCheckboxes > 0 &&
					checkedCheckboxes < totalCheckboxes;
			}
		});
	});

	// Initialize status change handlers
	const statusSelects = document.querySelectorAll('.status-select');
	statusSelects.forEach((select) => {
		select.addEventListener('change', function () {
			const bookingId = this.dataset.bookingId;
			const newStatus = this.value;
			const originalStatus = this.dataset.currentStatus;

			if (newStatus !== originalStatus) {
				updateBookingStatus(bookingId, newStatus);
			}
		});
	});
}

/**
 * Toggle bulk actions visibility
 */
function toggleBulkActionsVisibility() {
	const selectedCheckboxes = document.querySelectorAll(
		'.booking-checkbox:checked'
	);
	const bulkActionsBar = document.getElementById('bulkActionsBar');

	if (bulkActionsBar) {
		if (selectedCheckboxes.length > 0) {
			bulkActionsBar.style.display = 'block';
		} else {
			bulkActionsBar.style.display = 'none';
		}
	}
}

// Initialize booking management when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
	if (window.location.pathname.includes('/admin/bookings')) {
		initializeBookingManagement();
	}
});

// =============================================================================
// TABLE MANAGEMENT FUNCTIONS
// =============================================================================

/**
 * Toggle table status (available/unavailable)
 * @param {number} tableId - Table ID
 * @param {number} isAvailable - 1 for available, 0 for unavailable
 */
function toggleTableStatus(tableId, isAvailable) {
	if (!tableId) {
		showAlert('error', 'Invalid table ID');
		return;
	}

	// Show loading state
	const button = event.target;
	const originalText = button.innerHTML;
	button.disabled = true;
	button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

	// Make AJAX request
	fetch(`${window.SITE_URL || ''}/admin/tables/toggle-status`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
			'X-CSRF-TOKEN': window.adminConfig.csrfToken,
		},
		body: JSON.stringify({
			table_id: tableId,
			is_available: isAvailable,
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showAlert(
					'success',
					data.message || 'Table status updated successfully'
				);

				// Reload the page to reflect changes
				setTimeout(() => {
					window.location.reload();
				}, 1000);
			} else {
				showAlert(
					'error',
					data.message || 'Failed to update table status'
				);
				// Restore button
				button.disabled = false;
				button.innerHTML = originalText;
			}
		})
		.catch((error) => {
			console.error('Error updating table status:', error);
			showAlert('error', 'An error occurred while updating table status');
			// Restore button
			button.disabled = false;
			button.innerHTML = originalText;
		});
}

/**
 * View table booking history
 * @param {number} tableId - Table ID
 */
function viewTableHistory(tableId) {
	if (!tableId) {
		showAlert('error', 'Invalid table ID');
		return;
	}

	// Show loading in modal
	const modal = document.getElementById('tableHistoryModal');
	const content = document.getElementById('tableHistoryContent');

	if (modal && content) {
		content.innerHTML =
			'<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading history...</div>';

		// Show modal
		const bsModal = new bootstrap.Modal(modal);
		bsModal.show();

		// Load history data
		fetch(`${window.SITE_URL || ''}/admin/tables/history/${tableId}`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
			},
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.success) {
					content.innerHTML =
						data.html || '<p>No booking history found.</p>';
				} else {
					content.innerHTML =
						'<div class="alert alert-danger">Failed to load history</div>';
				}
			})
			.catch((error) => {
				console.error('Error loading table history:', error);
				content.innerHTML =
					'<div class="alert alert-danger">Error loading history</div>';
			});
	}
}

/**
 * View table bookings
 * @param {number} tableId - Table ID
 */
function viewTableBookings(tableId) {
	if (!tableId) {
		showAlert('error', 'Invalid table ID');
		return;
	}

	// Redirect to bookings page with table filter
	window.location.href = `${
		window.SITE_URL || ''
	}/admin/bookings?table_id=${tableId}`;
}

/**
 * Create new booking for specific table
 * @param {number} tableId - Table ID
 */
function createBooking(tableId) {
	if (!tableId) {
		showAlert('error', 'Invalid table ID');
		return;
	}

	// Redirect to booking creation page with pre-selected table
	window.location.href = `${
		window.SITE_URL || ''
	}/admin/bookings/create?table_id=${tableId}`;
}

/**
 * Show table utilization report
 */
function showUtilizationReport() {
	const modal = document.getElementById('utilizationModal');
	const content = document.getElementById('utilizationContent');

	if (modal && content) {
		content.innerHTML =
			'<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading report...</div>';

		// Show modal
		const bsModal = new bootstrap.Modal(modal);
		bsModal.show();

		// Load utilization data
		fetch(`${window.SITE_URL || ''}/admin/tables/utilization`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
			},
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.success) {
					content.innerHTML =
						data.html || '<p>No utilization data available.</p>';
				} else {
					content.innerHTML =
						'<div class="alert alert-danger">Failed to load utilization report</div>';
				}
			})
			.catch((error) => {
				console.error('Error loading utilization report:', error);
				content.innerHTML =
					'<div class="alert alert-danger">Error loading report</div>';
			});
	}
}

/**
 * Export tables data
 */
function exportTables() {
	// Get current filters if any
	const urlParams = new URLSearchParams(window.location.search);
	const filters = {};

	for (let [key, value] of urlParams) {
		if (
			[
				'location',
				'capacity_min',
				'capacity_max',
				'is_available',
			].includes(key)
		) {
			filters[key] = value;
		}
	}

	// Create export URL
	const exportUrl = new URL(`${window.SITE_URL || ''}/admin/tables/export`);
	Object.keys(filters).forEach((key) => {
		exportUrl.searchParams.append(key, filters[key]);
	});

	// Trigger download
	window.open(exportUrl.toString(), '_blank');
}

/**
 * Initialize table edit form functionality
 */
function initializeTableEditForm() {
	// Form validation
	const form = document.getElementById('editTableForm');
	if (form) {
		form.addEventListener('submit', function (e) {
			const tableNumber = document.getElementById('table_number');
			const capacity = document.getElementById('capacity');

			if (tableNumber && !tableNumber.value.trim()) {
				e.preventDefault();
				showAlert('error', 'Table number is required');
				tableNumber.focus();
				return;
			}

			if (capacity && (capacity.value < 1 || capacity.value > 20)) {
				e.preventDefault();
				showAlert('error', 'Capacity must be between 1 and 20');
				capacity.focus();
				return;
			}
		});
	}

	// Update table preview when form changes
	const inputs = form?.querySelectorAll('input, select, textarea');
	inputs?.forEach((input) => {
		input.addEventListener('change', updateTablePreview);
	});
}

/**
 * Update table preview based on form data
 */
function updateTablePreview() {
	const preview = document.querySelector('.table-preview');
	if (!preview) return;

	const capacity = document.getElementById('capacity')?.value || 4;
	const location = document.getElementById('location')?.value || 'Main Hall';
	const isAvailable = document.getElementById('is_available')?.value || 1;

	const statusClass =
		isAvailable == 1 ? 'table-available' : 'table-unavailable';
	const statusIcon =
		isAvailable == 1
			? 'fa-check-circle text-success'
			: 'fa-times-circle text-danger';

	preview.innerHTML = `
		<div class="table-visual ${statusClass} mx-auto" style="width: 80px; height: 80px; border-radius: 50%; background: ${
		isAvailable == 1 ? '#28a745' : '#dc3545'
	}; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
			${capacity}
		</div>
		<div class="mt-2">
			<i class="fas ${statusIcon}"></i>
			<small class="text-muted d-block">${location}</small>
		</div>
	`;
}

/**
 * Apply table filters
 */
function applyFilters() {
	const form = document.getElementById('filterForm');
	if (form) {
		form.submit();
	}
}

/**
 * Clear table filters
 */
function clearFilters() {
	const url = new URL(window.location);
	url.search = '';
	window.location = url;
}

/**
 * Quick booking function for tables
 * @param {number} tableId - Table ID to book
 */
function quickBooking(tableId) {
	if (!tableId || tableId <= 0) {
		showNotification('Invalid table ID', 'error');
		return;
	}

	// Redirect to booking page with pre-selected table
	window.location.href = `${window.SITE_URL}/admin/bookings/create?table_id=${tableId}`;
}

/**
 * Search tables function
 */
function searchTables() {
	const searchInput = document.querySelector('#tableSearch');
	const searchTerm = searchInput ? searchInput.value.trim() : '';

	if (!searchTerm) {
		showNotification('Please enter a search term', 'warning');
		return;
	}

	// Show loading state
	const searchButton = event.target;
	const originalText = searchButton.innerHTML;
	searchButton.innerHTML =
		'<i class="fas fa-spinner fa-spin"></i> Searching...';
	searchButton.disabled = true;

	// Perform search by reloading page with search parameter
	const currentUrl = new URL(window.location.href);
	currentUrl.searchParams.set('search', searchTerm);
	window.location.href = currentUrl.toString();
}
