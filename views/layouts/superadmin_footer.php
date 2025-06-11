    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (if needed for legacy code) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables (for advanced table functionality) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Initialize DataTables for all tables -->
    <script>
        $(document).ready(function() {
            // Initialize DataTables for all tables with class 'data-table'
            $('.data-table').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                order: [[0, 'desc']] // Default sort by first column descending
            });

            // Auto-hide alerts after 5 seconds
            $('.alert').each(function() {
                const alert = this;
                setTimeout(function() {
                    $(alert).fadeOut('slow');
                }, 5000);
            });
        });
    </script>

    <!-- Super Admin specific JavaScript -->
    <script>
        // Set global SITE_URL for JavaScript
        window.SITE_URL = '<?= SITE_URL ?>';        // Initialize Super Admin features
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize notification checking
            initNotificationSystem();

            // Add loading states to forms (but skip forms with custom handlers)
            document.querySelectorAll('form').forEach(form => {
                // Skip forms that have custom handlers to prevent conflicts
                if (form.hasAttribute('data-custom-handler') || form.hasAttribute('data-restaurant-protected')) {
                    console.log('⚠️ Skipping form with custom handler:', form.id || 'unnamed form');
                    return;
                }

                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        submitBtn.disabled = true;

                        // Re-enable after 3 seconds (fallback)
                        setTimeout(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }, 3000);
                    }
                });
            });

            // Add smooth hover effects to cards
            document.querySelectorAll('.card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Initialize collapsible sidebar items
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(trigger => {
                trigger.addEventListener('click', function() {
                    const icon = this.querySelector('.fa-caret-down');
                    if (icon) {
                        icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
                    }                });
            });
        });        // Notification System Functions
        function initNotificationSystem() {
            // Initial check
            checkUnreadNotifications();
            loadRecentNotifications();

            // Check every 30 seconds for new notifications
            setInterval(checkUnreadNotifications, 30000);
            setInterval(loadRecentNotifications, 60000); // Refresh dropdown every minute
        }

        function checkUnreadNotifications() {
            fetch(window.SITE_URL + '/superadmin/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.unread_count || 0);
                })
                .catch(error => {
                    console.error('Error checking notifications:', error);
                });
        }

        function loadRecentNotifications() {
            fetch(window.SITE_URL + '/superadmin/notifications/recent')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationDropdown(data.notifications);
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }

        function updateNotificationBadge(count) {
            const badge = document.querySelector('.notification-count-badge');
            const sidebarBadge = document.querySelector('.notification-badge');

            if (badge) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.style.display = 'flex';

                    // Play notification sound if count increased
                    if (window.lastNotificationCount !== undefined && count > window.lastNotificationCount) {
                        playNotificationSound();
                    }
                } else {
                    badge.style.display = 'none';
                }
                window.lastNotificationCount = count;
            }

            // Update sidebar badge too
            if (sidebarBadge) {
                if (count > 0) {
                    sidebarBadge.textContent = count > 99 ? '99+' : count;
                    sidebarBadge.style.display = 'inline';
                } else {
                    sidebarBadge.style.display = 'none';
                }
            }
        }

        function updateNotificationDropdown(notifications) {
            const notificationList = document.getElementById('notificationList');
            if (!notificationList) return;

            if (notifications.length === 0) {
                notificationList.innerHTML = `
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <div>No new notifications</div>
                    </div>
                `;
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                const isUnread = !notification.is_read;
                const typeIcon = getNotificationIcon(notification.type);
                const typeColor = getNotificationColor(notification.type);

                html += `
                    <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${notification.id}">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-${typeIcon} text-${typeColor}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${notification.title}</div>
                                <div class="text-muted small">${notification.message}</div>
                                <div class="notification-meta">${notification.time_ago}</div>
                            </div>
                            ${isUnread ? '<div class="ms-2"><span class="badge bg-primary">New</span></div>' : ''}
                        </div>
                        ${notification.data && notification.data.url ?
                            `<div class="mt-2">
                                <a href="${window.SITE_URL}${notification.data.url}"
                                   class="btn btn-sm btn-outline-primary"
                                   onclick="markNotificationAsRead(${notification.id})">View Details</a>
                            </div>` : ''}
                    </div>
                `;
            });

            notificationList.innerHTML = html;
        }

        function getNotificationIcon(type) {
            switch(type) {
                case 'new_order': return 'shopping-cart';
                case 'new_booking': return 'calendar-alt';
                case 'system': return 'cog';
                default: return 'bell';
            }
        }

        function getNotificationColor(type) {
            switch(type) {
                case 'new_order': return 'success';
                case 'new_booking': return 'info';
                case 'system': return 'warning';
                default: return 'secondary';
            }
        }

        function markNotificationAsRead(notificationId) {
            fetch(`${window.SITE_URL}/superadmin/notifications/mark-read/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.classList.remove('unread');
                        const badge = notificationItem.querySelector('.badge.bg-primary');
                        if (badge) badge.remove();
                    }

                    // Update badge count
                    checkUnreadNotifications();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function markAllNotificationsRead() {
            fetch(`${window.SITE_URL}/superadmin/notifications/mark-all-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh notifications
                    loadRecentNotifications();
                    checkUnreadNotifications();
                }
            })
            .catch(error => console.error('Error marking all notifications as read:', error));
        }

        function playNotificationSound() {
            // Create and play notification sound
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmQdABWO0fPRfilGHHfIs+XYpWAhABB+y/DijEqKBkysAAABAA==');
                audio.volume = 0.3;
                audio.play().catch(() => {
                    // Ignore autoplay policy errors
                });
            } catch (e) {
                // Ignore audio errors
            }
        }
    </script>

</body>
</html>
