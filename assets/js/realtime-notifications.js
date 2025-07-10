/**
 * Realtime Notifications System
 * Sử dụng Server-Sent Events (SSE) để nhận thông báo realtime
 */

class RealtimeNotifications {
	constructor() {
		this.eventSource = null;
		this.notificationCount = 0;
		this.isConnected = false;
		this.reconnectAttempts = 0;
		this.maxReconnectAttempts = 5;
		this.reconnectDelay = 3000; // 3 giây

		this.init();
	}

	init() {
		this.createNotificationContainer();
		this.startSSE();
		this.updateUnreadCount();

		// Cập nhật số thông báo chưa đọc mỗi 30 giây
		setInterval(() => {
			this.updateUnreadCount();
		}, 30000);
	}

	createNotificationContainer() {
		// Tạo container cho thông báo popup
		if (!document.getElementById('notification-container')) {
			const container = document.createElement('div');
			container.id = 'notification-container';
			container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
                pointer-events: none;
            `;
			document.body.appendChild(container);
		}

		// Tạo badge cho số thông báo chưa đọc
		this.createNotificationBadge();
	}

	createNotificationBadge() {
		// Tìm menu Internal Messages trong sidebar
		const internalMessagesLink = document.querySelector(
			'a[href*="internal-messages"]'
		);
		if (internalMessagesLink) {
			// Tạo badge
			let badge = internalMessagesLink.querySelector(
				'.notification-badge'
			);
			if (!badge) {
				badge = document.createElement('span');
				badge.className = 'notification-badge';
				badge.style.cssText = `
                    position: absolute;
                    top: -5px;
                    right: -5px;
                    background: #ff4444;
                    color: white;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    font-size: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    display: none;
                `;
				internalMessagesLink.style.position = 'relative';
				internalMessagesLink.appendChild(badge);
			}
		}
	}

	startSSE() {
		// Nếu đã có kết nối, đóng kết nối cũ trước khi tạo mới
		if (this.eventSource) {
			try {
				this.eventSource.close();
			} catch (e) {
				console.warn('Error closing previous EventSource:', e);
			}
			this.eventSource = null;
		}

		// Verify if we're still on a page that needs SSE
		if (
			!window.location.pathname.includes('/admin/internal-messages') &&
			!window.location.pathname.includes('/superadmin/internal-messages')
		) {
			console.log('Not on a notifications page, skipping SSE connection');
			return;
		}

		try {
			// Xác định role để chọn endpoint phù hợp
			const isSuperAdmin =
				window.location.pathname.includes('/superadmin/');
			const baseUrl = window.location.origin + '/buffet_booking_mvc';

			// Tạo một connection ID duy nhất để theo dõi kết nối
			const connectionId =
				Date.now() + '-' + Math.random().toString(36).substr(2, 9);

			// Thêm connectionId và timestamp vào URL để tránh cache và theo dõi kết nối
			const sseUrl = `${
				isSuperAdmin
					? baseUrl + '/superadmin/internal-messages/sse'
					: baseUrl + '/admin/internal-messages/sse'
			}?t=${Date.now()}&cid=${connectionId}`;

			console.log(`Starting SSE connection ${connectionId}`);

			// Define timeout to abort connection if it doesn't connect
			const connectionTimeout = setTimeout(() => {
				console.warn('SSE connection timeout');
				if (this.eventSource && this.eventSource.readyState !== 2) {
					// Not CLOSED
					this.eventSource.close();
					this.eventSource = null;
					this.reconnect();
				}
			}, 10000); // 10 second timeout

			// Tạo EventSource với URL có timestamp để tránh cache
			this.eventSource = new EventSource(sseUrl);

			this.eventSource.onopen = () => {
				console.log(`SSE Connected: ${connectionId}`);
				this.isConnected = true;
				this.reconnectAttempts = 0;

				// Clear the connection timeout
				clearTimeout(connectionTimeout);
			};

			this.eventSource.onmessage = (event) => {
				try {
					const data = JSON.parse(event.data);
					this.handleSSEMessage(data);
				} catch (error) {
					console.error('Error parsing SSE message:', error);
				}
			};

			this.eventSource.onerror = (error) => {
				// Log details about the error
				console.error('SSE Error:', error);
				console.log('Connection state:', this.eventSource.readyState);

				// Clear connection timeout if it exists
				clearTimeout(connectionTimeout);

				// Mark connection as disconnected
				this.isConnected = false;

				// Only close and reconnect if the connection is in CONNECTING (0) or OPEN (1) state
				// If it's already CLOSED (2), no need to close again
				if (this.eventSource.readyState !== 2) {
					this.eventSource.close();
				}

				this.eventSource = null;

				// Check if we're still on the page that needs this connection
				if (
					window.location.pathname.includes(
						'/admin/internal-messages'
					) ||
					window.location.pathname.includes(
						'/superadmin/internal-messages'
					)
				) {
					this.reconnect();
				} else {
					console.log('Page changed, not reconnecting SSE');
				}
			};

			// Đặt hẹn giờ tự đóng kết nối sau 30 giây và kết nối lại
			// Điều này giúp tránh kết nối kéo dài quá lâu và gây tắc nghẽn server
			setTimeout(() => {
				if (this.eventSource && this.eventSource.readyState !== 2) {
					console.log('Cycling SSE connection for performance');
					this.eventSource.close();
					this.eventSource = null;

					// Only reconnect if we're still on a relevant page
					if (
						window.location.pathname.includes(
							'/admin/internal-messages'
						) ||
						window.location.pathname.includes(
							'/superadmin/internal-messages'
						)
					) {
						this.reconnect();
					}
				}
			}, 30000);
		} catch (error) {
			console.error('Error starting SSE:', error);
			this.reconnect();
		}
	}

	handleSSEMessage(data) {
		switch (data.type) {
			case 'connection_id':
				// Lưu lại ID kết nối để debugging
				console.log(`Connection ID received: ${data.id}`);
				break;

			case 'ping':
				// Keep-alive message, không làm gì
				break;

			case 'new_message':
				this.handleNewMessage(data.message);
				break;

			case 'new_messages':
				// Xử lý nhiều thông báo cùng lúc (phiên bản tối ưu)
				if (data.messages && Array.isArray(data.messages)) {
					// Cập nhật số lượng thông báo
					this.notificationCount += data.count;
					this.updateNotificationBadge();

					// Hiển thị các thông báo
					data.messages.forEach((message) => {
						this.handleNewMessage(message);
					});
				}
				break;

			default:
				console.log('Unknown SSE message type:', data.type);
		}
	}

	handleNewMessage(message) {
		// Tăng số thông báo chưa đọc
		this.notificationCount++;

		// Cập nhật badge
		this.updateNotificationBadge();

		// Hiển thị popup thông báo
		this.showNotificationPopup(message);

		// Phát âm thanh thông báo (nếu có)
		this.playNotificationSound();
	}

	showNotificationPopup(message) {
		const container = document.getElementById('notification-container');
		const notification = document.createElement('div');
		notification.className = 'notification-popup';
		notification.style.cssText = `
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            pointer-events: auto;
            max-width: 350px;
            animation: slideInRight 0.3s ease-out;
        `;

		// Xác định màu sắc theo priority
		const priorityColors = {
			low: '#28a745',
			medium: '#ffc107',
			high: '#dc3545',
			urgent: '#dc3545',
		};

		const priorityColor = priorityColors[message.priority] || '#007bff';

		notification.innerHTML = `
            <div style="display: flex; align-items: flex-start; gap: 10px;">
                <div style="
                    width: 4px;
                    background: ${priorityColor};
                    border-radius: 2px;
                    flex-shrink: 0;
                "></div>
                <div style="flex: 1;">
                    <div style="
                        font-weight: bold;
                        color: #333;
                        margin-bottom: 5px;
                        font-size: 14px;
                    ">${this.escapeHtml(message.title)}</div>
                    <div style="
                        color: #666;
                        font-size: 12px;
                        margin-bottom: 8px;
                        line-height: 1.4;
                    ">${this.escapeHtml(
						message.content || 'Bạn có thông báo mới.'
					)}</div>
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        font-size: 11px;
                        color: #999;
                    ">
                        <span>Từ: ${this.escapeHtml(message.sender_name)}</span>
                        <span>${this.formatTime(message.created_at)}</span>
                    </div>
                    <div style="
                        margin-top: 8px;
                        display: flex;
                        gap: 8px;
                    ">
                        <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"
                                style="
                                    background: #f8f9fa;
                                    border: 1px solid #ddd;
                                    padding: 4px 8px;
                                    border-radius: 4px;
                                    font-size: 11px;
                                    cursor: pointer;
                                ">Đóng</button>
                        <button onclick="window.location.href='${this.getViewMessageUrl(
							message.id
						)}'"
                                style="
                                    background: ${priorityColor};
                                    color: white;
                                    border: none;
                                    padding: 4px 8px;
                                    border-radius: 4px;
                                    font-size: 11px;
                                    cursor: pointer;
                                ">Xem</button>
                    </div>
                </div>
            </div>
        `;

		container.appendChild(notification);

		// Tự động xóa sau 10 giây
		setTimeout(() => {
			if (notification.parentNode) {
				notification.style.animation = 'slideOutRight 0.3s ease-in';
				setTimeout(() => {
					if (notification.parentNode) {
						notification.remove();
					}
				}, 300);
			}
		}, 10000);
	}

	updateNotificationBadge() {
		const badge = document.querySelector('.notification-badge');
		if (badge) {
			if (this.notificationCount > 0) {
				badge.textContent =
					this.notificationCount > 99
						? '99+'
						: this.notificationCount;
				badge.style.display = 'flex';
			} else {
				badge.style.display = 'none';
			}
		}
	}

	async updateUnreadCount() {
		try {
			const isSuperAdmin =
				window.location.pathname.includes('/superadmin/');
			const baseUrl = window.location.origin + '/buffet_booking_mvc';
			const url = isSuperAdmin
				? baseUrl + '/superadmin/internal-messages/get-unread-count'
				: baseUrl + '/admin/internal-messages/get-unread-count';

			const response = await fetch(url);
			const data = await response.json();

			if (data.count !== undefined) {
				this.notificationCount = data.count;
				this.updateNotificationBadge();
			}
		} catch (error) {
			console.error('Error updating unread count:', error);
		}
	}

	async markAsRead(messageId) {
		try {
			const isSuperAdmin =
				window.location.pathname.includes('/superadmin/');
			const baseUrl = window.location.origin + '/buffet_booking_mvc';
			const url = isSuperAdmin
				? baseUrl + '/superadmin/internal-messages/mark-as-read'
				: baseUrl + '/admin/internal-messages/mark-as-read';

			const response = await fetch(url, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: `message_id=${messageId}`,
			});

			const data = await response.json();
			if (data.success) {
				this.notificationCount = Math.max(
					0,
					this.notificationCount - 1
				);
				this.updateNotificationBadge();
			}
		} catch (error) {
			console.error('Error marking message as read:', error);
		}
	}

	getViewMessageUrl(messageId) {
		const isSuperAdmin = window.location.pathname.includes('/superadmin/');
		const baseUrl = window.location.origin + '/buffet_booking_mvc';
		return isSuperAdmin
			? baseUrl + `/superadmin/internal-messages/view/${messageId}`
			: baseUrl + `/admin/internal-messages/view/${messageId}`;
	}

	playNotificationSound() {
		// Tạo âm thanh thông báo đơn giản
		try {
			const audioContext = new (window.AudioContext ||
				window.webkitAudioContext)();
			const oscillator = audioContext.createOscillator();
			const gainNode = audioContext.createGain();

			oscillator.connect(gainNode);
			gainNode.connect(audioContext.destination);

			oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
			oscillator.frequency.setValueAtTime(
				600,
				audioContext.currentTime + 0.1
			);

			gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
			gainNode.gain.exponentialRampToValueAtTime(
				0.01,
				audioContext.currentTime + 0.2
			);

			oscillator.start(audioContext.currentTime);
			oscillator.stop(audioContext.currentTime + 0.2);
		} catch (error) {
			console.log('Could not play notification sound:', error);
		}
	}

	reconnect() {
		// Check if the current page still needs SSE
		if (
			!window.location.pathname.includes('/admin/internal-messages') &&
			!window.location.pathname.includes('/superadmin/internal-messages')
		) {
			console.log(
				'No longer on a notifications page, skipping reconnection'
			);
			return;
		}

		if (this.reconnectAttempts < this.maxReconnectAttempts) {
			this.reconnectAttempts++;

			// Sử dụng exponential backoff để tăng dần thời gian chờ giữa các lần kết nối lại
			// Công thức: delay = base_delay * (2 ^ attempt) + random
			const backoffDelay =
				this.reconnectDelay * Math.pow(1.5, this.reconnectAttempts - 1);
			const jitter = Math.floor(Math.random() * 1000); // Thêm random jitter để tránh thundering herd
			const finalDelay = Math.min(backoffDelay + jitter, 30000); // Giới hạn tối đa 30 giây

			console.log(
				`Reconnecting... Attempt ${this.reconnectAttempts}/${
					this.maxReconnectAttempts
				} (delay: ${Math.round(finalDelay / 1000)}s)`
			);

			// Create a cleanup function for this reconnection attempt
			const cleanupAndReconnect = () => {
				if (this.eventSource) {
					try {
						this.eventSource.close();
					} catch (e) {
						console.warn('Error during connection cleanup:', e);
					}
					this.eventSource = null;
				}

				// Nếu tab không hiển thị, trì hoãn kết nối
				if (document.hidden) {
					console.log('Tab không hiển thị, trì hoãn kết nối SSE');

					// Thiết lập một sự kiện để kết nối lại khi tab được kích hoạt
					const visibilityHandler = () => {
						if (!document.hidden) {
							console.log(
								'Tab hiển thị lại, khôi phục kết nối SSE'
							);
							this.startSSE();
							document.removeEventListener(
								'visibilitychange',
								visibilityHandler
							);
						}
					};

					document.addEventListener(
						'visibilitychange',
						visibilityHandler
					);
				} else {
					// Check again if we're still on a notifications page
					if (
						window.location.pathname.includes(
							'/admin/internal-messages'
						) ||
						window.location.pathname.includes(
							'/superadmin/internal-messages'
						)
					) {
						this.startSSE();
					} else {
						console.log(
							'No longer on notifications page, aborting reconnection'
						);
					}
				}
			};

			setTimeout(cleanupAndReconnect, finalDelay);
		} else {
			console.error('Max reconnection attempts reached');

			// Reset the connection completely
			if (this.eventSource) {
				this.eventSource.close();
				this.eventSource = null;
			}

			// Show a visible error to the user
			this.showConnectionError();

			// Sau 1 phút, reset lại số lần thử kết nối để cho phép thử lại sau
			setTimeout(() => {
				this.reconnectAttempts = 0;
				// Only reconnect if still on relevant page
				if (
					window.location.pathname.includes(
						'/admin/internal-messages'
					) ||
					window.location.pathname.includes(
						'/superadmin/internal-messages'
					)
				) {
					console.log('Resuming connection attempts after timeout');
					this.reconnect();
				}
			}, 60000);
		}
	}

	// Show connection error message to user
	showConnectionError() {
		const container = document.getElementById('notification-container');
		if (!container) return;

		const errorNotification = document.createElement('div');
		errorNotification.className = 'notification-popup';
		errorNotification.style.cssText = `
			background: white;
			border: 1px solid #dc3545;
			border-radius: 8px;
			padding: 15px;
			margin-bottom: 10px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
			pointer-events: auto;
			max-width: 350px;
			animation: slideInRight 0.3s ease-out;
		`;

		errorNotification.innerHTML = `
			<div style="display: flex; flex-direction: column; gap: 10px;">
				<div style="font-weight: bold; color: #dc3545;">
					<i class="fas fa-exclamation-triangle"></i> Lỗi kết nối
				</div>
				<div style="color: #666; font-size: 13px;">
					Không thể kết nối đến hệ thống thông báo. Thử làm mới trang để kết nối lại.
				</div>
				<div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 5px;">
					<button onclick="window.location.reload()" style="
						background: #dc3545;
						color: white;
						border: none;
						padding: 6px 12px;
						border-radius: 4px;
						font-size: 12px;
						cursor: pointer;
					">Làm mới trang</button>
				</div>
			</div>
		`;

		container.appendChild(errorNotification);
	}

	disconnect() {
		if (this.eventSource) {
			this.eventSource.close();
			this.isConnected = false;
		}
	}

	escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

	formatTime(timestamp) {
		const date = new Date(timestamp);
		const now = new Date();
		const diff = now - date;

		if (diff < 60000) {
			// < 1 phút
			return 'Vừa xong';
		} else if (diff < 3600000) {
			// < 1 giờ
			return `${Math.floor(diff / 60000)} phút trước`;
		} else if (diff < 86400000) {
			// < 1 ngày
			return `${Math.floor(diff / 3600000)} giờ trước`;
		} else {
			return date.toLocaleDateString('vi-VN');
		}
	}
}

// Thêm CSS animations
const notificationsStyle = document.createElement('style');
notificationsStyle.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(notificationsStyle);

// Khởi tạo khi trang load xong
document.addEventListener('DOMContentLoaded', () => {
	// Chỉ khởi tạo cho Admin và Super Admin khi đang ở trang thông báo nội bộ
	if (
		window.location.pathname.includes('/admin/internal-messages') ||
		window.location.pathname.includes('/superadmin/internal-messages')
	) {
		// Kiểm tra xem đã khởi tạo hay chưa để tránh khởi tạo nhiều lần
		if (!window.realtimeNotifications) {
			console.log('Initializing realtime notifications system');
			window.realtimeNotifications = new RealtimeNotifications();
		}
	}
});

// Cleanup khi rời trang
window.addEventListener('beforeunload', () => {
	if (window.realtimeNotifications) {
		window.realtimeNotifications.disconnect();
	}
});
