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

// Override assignTable to hide the booking details modal
window.assignTable = function (bookingId) {
	// First, hide the booking details modal if it's open
	const bookingDetailsModal = document.getElementById('bookingDetailsModal');
	if (bookingDetailsModal) {
		const bsModal = bootstrap.Modal.getInstance(bookingDetailsModal);
		if (bsModal) {
			bsModal.hide();
			// Small delay to prevent modal conflicts
			setTimeout(() => {
				// Then show the table assignment modal
				const tableModal = document.getElementById('tableAssignModal');
				if (tableModal) {
					const modal = new bootstrap.Modal(tableModal);
					// IMPORTANT: Make sure we set the booking ID correctly
					const bookingIdInput =
						document.getElementById('bookingIdForTable');
					if (bookingIdInput) {
						bookingIdInput.value = bookingId;
					} else {
						console.error('bookingIdForTable input not found!');
					}
					// Load available tables
					loadAvailableTables(bookingId);
					modal.show();
				} else {
					console.error(
						'Table assignment modal not found in the DOM'
					);
					showViewOnlyNotification(
						'Table assignment modal not found'
					);
				}
			}, 300);
		} else {
			// If no instance, just proceed with showing the table modal
			const tableModal = document.getElementById('tableAssignModal');
			if (tableModal) {
				const modal = new bootstrap.Modal(tableModal);
				const bookingIdInput =
					document.getElementById('bookingIdForTable');
				if (bookingIdInput) {
					bookingIdInput.value = bookingId;
				}
				// Load available tables
				loadAvailableTables(bookingId);
				modal.show();
			}
		}
	} else {
		// If booking details modal doesn't exist, just show the table modal
		const tableModal = document.getElementById('tableAssignModal');
		if (tableModal) {
			const modal = new bootstrap.Modal(tableModal);
			const bookingIdInput = document.getElementById('bookingIdForTable');
			if (bookingIdInput) {
				bookingIdInput.value = bookingId;
			}
			// Load available tables
			loadAvailableTables(bookingId);
			modal.show();
		}
	}
};

// Load available tables for a booking
window.loadAvailableTables = function (bookingId) {
	console.log('Loading available tables for booking:', bookingId);

	// First get booking details to know the location
	fetch(
		`${window.SITE_URL}/admin/bookings/available-tables?booking_id=${bookingId}`,
		{
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
			},
		}
	)
		.then((response) => response.json())
		.then((data) => {
			const tableSelect = document.getElementById('tableNumber');
			if (!tableSelect) {
				console.error('Table select element not found');
				return;
			}

			// Clear existing options
			tableSelect.innerHTML = '<option value="">Select Table</option>';

			if (data.success && data.tables && data.tables.length > 0) {
				data.tables.forEach((table) => {
					const option = document.createElement('option');
					option.value = table.id;
					option.textContent = `Table ${table.table_number} (${
						table.capacity
					} seats)${
						table.location
							? ` - ${table.location.substring(0, 30)}...`
							: ''
					}`;
					tableSelect.appendChild(option);
				});
				console.log(`Loaded ${data.tables.length} available tables`);
			} else {
				const option = document.createElement('option');
				option.value = '';
				option.textContent = 'No available tables';
				option.disabled = true;
				tableSelect.appendChild(option);
				console.log('No available tables found');
			}
		})
		.catch((error) => {
			console.error('Error loading available tables:', error);
			const tableSelect = document.getElementById('tableNumber');
			if (tableSelect) {
				tableSelect.innerHTML =
					'<option value="">Error loading tables</option>';
			}
		});
};

// Global saveTableAssignment function for modal onclick
window.saveTableAssignment = function () {
	console.log('Global saveTableAssignment called');
	const bookingId = document.getElementById('bookingIdForTable')?.value;
	const tableId = document.getElementById('tableNumber')?.value;
	const notes = document.getElementById('assignTableNotes')?.value || '';

	console.log('Save table assignment values:', { bookingId, tableId, notes });

	if (!bookingId) {
		showViewOnlyNotification('Booking ID is missing');
		return;
	}

	if (!tableId) {
		showViewOnlyNotification('Please select a table');
		return;
	}

	const csrfToken =
		document
			.querySelector('meta[name="csrf-token"]')
			?.getAttribute('content') ||
		document.querySelector('input[name="csrf_token"]')?.value ||
		'';

	fetch(`${window.SITE_URL}/admin/bookings/assign-table`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-Token': csrfToken,
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
				showViewOnlyNotification('Table assigned successfully');

				const tableModal = document.getElementById('tableAssignModal');
				if (tableModal) {
					const modal = bootstrap.Modal.getInstance(tableModal);
					if (modal) {
						modal.hide();
					}
				}

				// Reload the page to reflect changes
				setTimeout(() => {
					window.location.reload();
				}, 1000);
			} else {
				showViewOnlyNotification(
					data.message || 'Failed to assign table'
				);
			}
		})
		.catch((error) => {
			console.error('Error assigning table:', error);
			showViewOnlyNotification(
				'An error occurred while processing your request'
			);
		});
};

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
		// Make sure the table assignment modal exists
		if (!document.getElementById('tableAssignModal')) {
			// Create a table assignment modal if it doesn't exist
			const modalHtml = `
				<div class="modal fade" id="tableAssignModal" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Assign Table</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="tableAssignForm">
									<input type="hidden" id="bookingIdForTable" name="booking_id">
									<input type="hidden" name="csrf_token" value="${
										document
											.querySelector(
												'meta[name="csrf-token"]'
											)
											?.getAttribute('content') ||
										document.querySelector(
											'input[name="csrf_token"]'
										)?.value ||
										''
									}">
									<div class="mb-3">
										<label for="tableNumber" class="form-label">Select Table</label>
										<select class="form-select" id="tableNumber" name="table_id" required>
											<option value="">Select Table</option>
										</select>
									</div>
									<div class="mb-3">
										<label for="assignTableNotes" class="form-label">Notes (Optional)</label>
										<textarea class="form-control" id="assignTableNotes" name="notes" rows="3"></textarea>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
								<button type="button" class="btn btn-primary" id="assignTableButton">Assign Table</button>
							</div>
						</div>
					</div>
				</div>
			`;
			const modalContainer = document.createElement('div');
			modalContainer.innerHTML = modalHtml;
			document.body.appendChild(modalContainer);
		}

		// IMPORTANT: Create our own direct implementation of the save function to avoid the collision
		const ourSaveTableAssignment = function () {
			console.log('Called our direct saveTableAssignment function');
			const bookingId =
				document.getElementById('bookingIdForTable')?.value;
			const tableId = document.getElementById('tableNumber')?.value;
			const notes =
				document.getElementById('assignTableNotes')?.value || '';

			console.log('Assign table values:', { bookingId, tableId, notes });

			if (!bookingId) {
				showViewOnlyNotification('Booking ID is missing');
				return;
			}

			if (!tableId) {
				if (typeof showAlert === 'function') {
					showAlert('Please select a table', 'warning');
				} else {
					showViewOnlyNotification('Please select a table');
				}
				return;
			}

			const csrfToken =
				document
					.querySelector('meta[name="csrf-token"]')
					?.getAttribute('content') ||
				document.querySelector('input[name="csrf_token"]')?.value ||
				'';

			fetch(`${window.SITE_URL}/admin/bookings/assign-table`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-Token': csrfToken,
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
						if (typeof showAlert === 'function') {
							showAlert('Table assigned successfully', 'success');
						} else {
							showViewOnlyNotification(
								'Table assigned successfully'
							);
						}

						const tableModal =
							document.getElementById('tableAssignModal');
						if (tableModal) {
							const modal =
								bootstrap.Modal.getInstance(tableModal);
							if (modal) {
								modal.hide();
							}
						}

						// Reload the page to reflect changes
						setTimeout(() => {
							window.location.reload();
						}, 1000);
					} else {
						if (typeof showAlert === 'function') {
							showAlert(
								data.message || 'Failed to assign table',
								'danger'
							);
						} else {
							showViewOnlyNotification(
								data.message || 'Failed to assign table'
							);
						}
					}
				})
				.catch((error) => {
					console.error('Error assigning table:', error);
					if (typeof showAlert === 'function') {
						showAlert(
							'An error occurred while processing your request',
							'danger'
						);
					} else {
						showViewOnlyNotification(
							'An error occurred while processing your request'
						);
					}
				});
		};

		// Find ALL assign table buttons and replace their onclick handler directly
		const allAssignTableButtons = document.querySelectorAll(
			'#tableAssignModal .btn-primary'
		);
		allAssignTableButtons.forEach((button) => {
			console.log('Found assign table button, replacing event handler');
			// Remove any existing listeners or onclick attribute
			button.removeAttribute('onclick');
			// Clone and replace to remove any existing event listeners
			const newButton = button.cloneNode(true);
			button.parentNode.replaceChild(newButton, button);
			// Add our direct function
			newButton.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				console.log(
					'Assign table button clicked with our direct handler'
				);
				ourSaveTableAssignment();
			});
		});

		// Show initial info notification
		setTimeout(() => {
			showViewOnlyNotification(
				'Edit booking functionality has been disabled in this view.'
			);
		}, 500);

		// Use MutationObserver to watch for dynamically added buttons
		const bodyObserver = new MutationObserver(function (mutations) {
			mutations.forEach(function (mutation) {
				if (mutation.addedNodes && mutation.addedNodes.length > 0) {
					// Check for new assign table buttons
					const newAssignButtons = document.querySelectorAll(
						'#tableAssignModal .btn-primary:not([data-bookings-fix-handled])'
					);
					if (newAssignButtons.length > 0) {
						console.log(
							'Found new assign table buttons via MutationObserver, attaching handlers'
						);
						newAssignButtons.forEach((button) => {
							button.removeAttribute('onclick');
							// Mark as handled
							button.setAttribute(
								'data-bookings-fix-handled',
								'true'
							);
							// Clone and replace to remove any existing event listeners
							const newButton = button.cloneNode(true);
							button.parentNode.replaceChild(newButton, button);
							// Add our direct function
							newButton.addEventListener('click', function (e) {
								e.preventDefault();
								e.stopPropagation();
								console.log(
									'Assign table button clicked via MutationObserver handler'
								);
								ourSaveTableAssignment();
							});
						});
					}
				}
			});
		});

		// Start observing the document body for dynamically added elements
		bodyObserver.observe(document.body, {
			childList: true,
			subtree: true,
		});
	}
});
