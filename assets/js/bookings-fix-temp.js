// This file fixes the Uncaught TypeError: Cannot read properties of undefined (reading 'backdrop')
// by ensuring the Modal class is available and any removed edit functions are handled

console.log('Bookings fix script loaded!');

// Helper function to show a temporary notification
function showViewOnlyNotification(message) {
	console.log(message);

	// Show a more user-friendly notification
	const notification = document.createElement('div');
	notification.className =
		'alert alert-info alert-dismissible fade show fixed-top w-50 mx-auto mt-3';
	notification.style.zIndex = '9999';
	notification.innerHTML = `
		<i class="fas fa-info-circle me-2"></i>
		<strong>View Only Mode:</strong> ${message}
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	`;
	document.body.appendChild(notification);

	// Auto-remove after 3 seconds
	setTimeout(() => {
		if (notification.parentNode) {
			notification.classList.remove('show');
			setTimeout(() => notification.remove(), 300);
		}
	}, 3000);
}

// Only disable the edit booking button/link - NOT table assignment or status updates
window.editBooking = function (bookingId) {
	showViewOnlyNotification(
		'Editing bookings has been disabled in this view.'
	);
	return false;
};

// Ensure viewBookingDetails is defined and doesn't trigger assignTable errors
document.addEventListener('DOMContentLoaded', function () {
	// Check if we're on the bookings page
	if (window.location.pathname.includes('/admin/bookings')) {
		console.log('Bookings page DOM loaded');

		// Make sure Bootstrap Modal is available
		if (typeof bootstrap === 'undefined') {
			console.error(
				'Bootstrap is not loaded, modal functionality will not work'
			);
			// Define a fallback viewBookingDetails if it's not defined
			if (typeof window.viewBookingDetails !== 'function') {
				window.viewBookingDetails = function (bookingId) {
					alert(
						'Booking details view is not available at this time.'
					);
				};
			}
		}

		// Disable only edit booking functionality, not table assignment or status updates
		window.editBooking = function (bookingId) {
			showViewOnlyNotification(
				'Editing bookings has been disabled in this view.'
			);
			return false;
		};

		// Find and disable any edit booking links in the DOM
		const editLinks = document.querySelectorAll(
			'a[href*="admin/bookings/edit"], button[onclick*="editBooking"]'
		);
		editLinks.forEach((link) => {
			link.addEventListener('click', function (e) {
				e.preventDefault();
				showViewOnlyNotification(
					'Editing bookings has been disabled in this view.'
				);
				return false;
			});

			// Optionally hide or disable the button visually
			link.classList.add('disabled');
			link.setAttribute('disabled', 'disabled');
			link.style.display = 'none'; // Hide completely
		});

		// Show initial info notification
		setTimeout(() => {
			showViewOnlyNotification(
				'Edit booking functionality has been disabled in this view.'
			);
		}, 500);
	}
});
