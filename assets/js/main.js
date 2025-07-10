// Main JavaScript for Buffet Booking Website

// Check if jQuery is loaded
if (typeof jQuery === 'undefined') {
	console.warn('jQuery is not loaded. Some features may not work properly.');
} else {
	$(document).ready(function () {
		// Initialize tooltips
		var tooltipTriggerList = [].slice.call(
			document.querySelectorAll('[data-bs-toggle="tooltip"]')
		);
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl);
		});

		// Initialize cart
		updateCartDisplay();

		// Add to cart functionality
		$('.add-to-cart').on('click', function (e) {
			e.preventDefault();

			var foodId = $(this).data('food-id');
			var foodName = $(this).data('food-name');
			var foodPrice = $(this).data('food-price');
			var quantity =
				$(this).closest('.card').find('.quantity-input').val() || 1;

			addToCart(foodId, foodName, foodPrice, quantity);
		});

		// Update cart quantity
		$(document).on('click', '.cart-quantity-btn', function () {
			var foodId = $(this).data('food-id');
			var action = $(this).data('action');
			var currentQty = parseInt($('#qty-' + foodId).text());
			var newQty =
				action === 'increase'
					? currentQty + 1
					: Math.max(1, currentQty - 1);

			updateCartQuantity(foodId, newQty);
		});

		// Remove from cart
		$(document).on('click', '.remove-from-cart', function () {
			var foodId = $(this).data('food-id');
			removeFromCart(foodId);
		});

		// Apply promotion code
		$('#apply-promotion').on('click', function () {
			var promoCode = $('#promo-code').val().trim();
			if (promoCode) {
				applyPromotionCode(promoCode);
			}
		});

		// Form validation
		$('form[data-validate="true"]').on('submit', function (e) {
			if (!validateForm(this)) {
				e.preventDefault();
			}
		});

		// Search functionality
		$('#search-form').on('submit', function (e) {
			var query = $('#search-input').val().trim();
			if (!query) {
				e.preventDefault();
				showAlert('warning', 'Vui lòng nhập từ khóa tìm kiếm');
			}
		});

		// Smooth scrolling for anchor links
		$('a[href^="#"]').on('click', function (e) {
			e.preventDefault();
			var target = $(this.getAttribute('href'));
			if (target.length) {
				$('html, body')
					.stop()
					.animate(
						{
							scrollTop: target.offset().top - 100,
						},
						1000
					);
			}
		});

		// Auto-hide alerts
		setTimeout(function () {
			$('.alert').fadeOut();
		}, 5000);

		// Cart Management Functions
		function addToCart(foodId, foodName, foodPrice, quantity) {
			$.ajax({
				url: window.siteUrl + '/cart/add',
				method: 'POST',
				data: {
					food_id: foodId,
					quantity: quantity,
					csrf_token: window.csrfToken,
				},
				success: function (response) {
					if (response.success) {
						showAlert(
							'success',
							'Đã thêm ' + foodName + ' vào giỏ hàng'
						);
						updateCartDisplay();
					} else {
						showAlert('error', response.message || 'Có lỗi xảy ra');
					}
				},
				error: function () {
					showAlert('error', 'Không thể kết nối đến server');
				},
			});
		}

		function updateCartQuantity(foodId, quantity) {
			$.ajax({
				url: window.siteUrl + '/cart/update',
				method: 'POST',
				data: {
					food_id: foodId,
					quantity: quantity,
					csrf_token: window.csrfToken,
				},
				success: function (response) {
					if (response.success) {
						updateCartDisplay();
						location.reload(); // Refresh cart page
					} else {
						showAlert('error', response.message || 'Có lỗi xảy ra');
					}
				},
				error: function () {
					showAlert('error', 'Không thể kết nối đến server');
				},
			});
		}

		function removeFromCart(foodId) {
			if (confirm('Bạn có chắc muốn xóa món này khỏi giỏ hàng?')) {
				$.ajax({
					url: window.siteUrl + '/cart/remove',
					method: 'POST',
					data: {
						food_id: foodId,
						csrf_token: window.csrfToken,
					},
					success: function (response) {
						if (response.success) {
							showAlert('success', 'Đã xóa món ăn khỏi giỏ hàng');
							updateCartDisplay();
							location.reload(); // Refresh cart page
						} else {
							showAlert(
								'error',
								response.message || 'Có lỗi xảy ra'
							);
						}
					},
					error: function () {
						showAlert('error', 'Không thể kết nối đến server');
					},
				});
			}
		}

		function updateCartDisplay() {
			$.ajax({
				url: window.siteUrl + '/cart/info',
				method: 'POST',
				data: {
					csrf_token: window.csrfToken,
				},
				success: function (response) {
					if (response.success) {
						$('#cart-count').text(response.count);
					}
				},
			});
		}

		function applyPromotionCode(code) {
			$.ajax({
				url: window.siteUrl + '/promotion/apply',
				method: 'POST',
				data: {
					code: code,
					csrf_token: window.csrfToken,
				},
				success: function (response) {
					if (response.success) {
						showAlert('success', 'Mã khuyến mãi đã được áp dụng');
						location.reload();
					} else {
						showAlert(
							'error',
							response.message || 'Mã khuyến mãi không hợp lệ'
						);
					}
				},
				error: function () {
					showAlert('error', 'Không thể kết nối đến server');
				},
			});
		}

		// Form Validation
		function validateForm(form) {
			var isValid = true;
			var $form = $(form);

			// Clear previous errors
			$form.find('.is-invalid').removeClass('is-invalid');
			$form.find('.invalid-feedback').remove();

			// Validate required fields
			$form.find('[required]').each(function () {
				var $field = $(this);
				var value = $field.val().trim();

				if (!value) {
					showFieldError($field, 'Trường này là bắt buộc');
					isValid = false;
				}
			});

			// Validate email
			$form.find('input[type="email"]').each(function () {
				var $field = $(this);
				var email = $field.val().trim();

				if (email && !isValidEmail(email)) {
					showFieldError($field, 'Email không hợp lệ');
					isValid = false;
				}
			});

			// Validate phone
			$form
				.find('input[type="tel"], input[name*="phone"]')
				.each(function () {
					var $field = $(this);
					var phone = $field.val().trim();

					if (phone && !isValidPhone(phone)) {
						showFieldError($field, 'Số điện thoại không hợp lệ');
						isValid = false;
					}
				});

			// Validate password confirmation
			var $password = $form.find('input[name="password"]');
			var $confirmPassword = $form.find('input[name="confirm_password"]');

			if ($password.length && $confirmPassword.length) {
				if ($password.val() !== $confirmPassword.val()) {
					showFieldError(
						$confirmPassword,
						'Mật khẩu xác nhận không khớp'
					);
					isValid = false;
				}
			}

			return isValid;
		}

		function showFieldError($field, message) {
			$field.addClass('is-invalid');
			$field.after('<div class="invalid-feedback">' + message + '</div>');
		}

		function isValidEmail(email) {
			var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			return regex.test(email);
		}

		function isValidPhone(phone) {
			var regex = /^[0-9+\-\s()]{10,}$/;
			return regex.test(phone);
		}

		// Alert Functions
		function showAlert(type, message) {
			var alertClass = 'alert-' + (type === 'error' ? 'danger' : type);
			var alertHtml =
				'<div class="alert ' +
				alertClass +
				' alert-dismissible fade show" role="alert">' +
				message +
				'<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
				'</div>';

			// Add to alert container or top of page
			var $container = $('#alert-container');
			if ($container.length === 0) {
				$container = $(
					'<div id="alert-container" class="container mt-3"></div>'
				);
				$('main').prepend($container);
			}

			$container.append(alertHtml);

			// Auto-hide after 5 seconds
			setTimeout(function () {
				$container
					.find('.alert')
					.last()
					.fadeOut(function () {
						$(this).remove();
					});
			}, 5000);
		}

		// Image Loading with Fallback
		function handleImageError(img) {
			img.src = window.siteUrl + '/assets/images/no-image.svg';
		}

		// Loading Spinner
		function showLoading(element) {
			$(element).html('<div class="spinner"></div>');
		}

		function hideLoading() {
			$('.spinner').remove();
		}

		// Number Formatting
		function formatCurrency(amount) {
			return new Intl.NumberFormat('vi-VN', {
				style: 'currency',
				currency: 'VND',
			}).format(amount);
		}

		function formatNumber(number) {
			return new Intl.NumberFormat('vi-VN').format(number);
		}

		// Date Formatting
		function formatDate(dateString) {
			var date = new Date(dateString);
			return date.toLocaleDateString('vi-VN', {
				year: 'numeric',
				month: '2-digit',
				day: '2-digit',
			});
		}

		function formatDateTime(dateString) {
			var date = new Date(dateString);
			return date.toLocaleString('vi-VN', {
				year: 'numeric',
				month: '2-digit',
				day: '2-digit',
				hour: '2-digit',
				minute: '2-digit',
			});
		}

		// Local Storage Helpers
		function saveToStorage(key, data) {
			try {
				localStorage.setItem(key, JSON.stringify(data));
			} catch (e) {
				console.warn('Could not save to localStorage:', e);
			}
		}

		function getFromStorage(key) {
			try {
				var data = localStorage.getItem(key);
				return data ? JSON.parse(data) : null;
			} catch (e) {
				console.warn('Could not read from localStorage:', e);
				return null;
			}
		}

		function removeFromStorage(key) {
			try {
				localStorage.removeItem(key);
			} catch (e) {
				console.warn('Could not remove from localStorage:', e);
			}
		}
	});
}
