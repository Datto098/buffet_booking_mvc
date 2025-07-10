 <!-- Page Header -->
 <!-- Load the fix script at the very beginning to ensure it overrides any problematic functions -->

 <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
     <div>
         <h1 class="h2">Bookings Management</h1>
         <nav aria-label="breadcrumb">
             <ol class="breadcrumb">
                 <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                 <li class="breadcrumb-item active">Bookings</li>
             </ol>
         </nav>
     </div>
     <div class="btn-toolbar mb-2 mb-md-0">
         <div class="btn-group me-2">
             <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                 <i class="fas fa-filter"></i> Filter
             </button>
             <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportBookings()">
                 <i class="fas fa-download"></i> Export
             </button>
         </div>
     </div>
 </div>

 <!-- Flash Messages -->
 <?php if (isset($_SESSION['success'])): ?>
     <div class="alert alert-success alert-dismissible fade show" role="alert">
         <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success'];
                                                unset($_SESSION['success']); ?>
         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
     </div>
 <?php endif; ?>

 <?php if (isset($_SESSION['error'])): ?>
     <div class="alert alert-danger alert-dismissible fade show" role="alert">
         <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error'];
                                                    unset($_SESSION['error']); ?>
         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
     </div>
 <?php endif; ?>

 <!-- Statistics Cards -->
 <div class="row mb-4">
     <div class="col-xl-3 col-md-6 mb-4">
         <div class="card border-0 shadow-sm h-100 card-gradient-primary">
             <div class="card-body">
                 <div class="row no-gutters align-items-center">
                     <div class="col mr-2">
                         <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                             Total Bookings
                         </div>
                         <div class="h5 mb-0 font-weight-bold text-gray-800">
                             <?php echo $totalBookings ?? 0; ?>
                         </div>
                     </div>
                     <div class="col-auto">
                         <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <div class="col-xl-3 col-md-6 mb-4">
         <div class="card border-0 shadow-sm h-100 card-gradient-success">
             <div class="card-body">
                 <div class="row no-gutters align-items-center">
                     <div class="col mr-2">
                         <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                             Confirmed
                         </div>
                         <div class="h5 mb-0 font-weight-bold text-gray-800">
                             <?php echo $confirmedBookings ?? 0; ?>
                         </div>
                     </div>
                     <div class="col-auto">
                         <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <div class="col-xl-3 col-md-6 mb-4">
         <div class="card border-0 shadow-sm h-100 card-gradient-warning">
             <div class="card-body">
                 <div class="row no-gutters align-items-center">
                     <div class="col mr-2">
                         <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                             Pending
                         </div>
                         <div class="h5 mb-0 font-weight-bold text-gray-800">
                             <?php echo $pendingBookings ?? 0; ?>
                         </div>
                     </div>
                     <div class="col-auto">
                         <i class="fas fa-clock fa-2x text-gray-300"></i>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <div class="col-xl-3 col-md-6 mb-4">
         <div class="card border-0 shadow-sm h-100 card-gradient-info">
             <div class="card-body">
                 <div class="row no-gutters align-items-center">
                     <div class="col mr-2">
                         <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                             Today
                         </div>
                         <div class="h5 mb-0 font-weight-bold text-gray-800">
                             <?php echo $todayBookings ?? 0; ?>
                         </div>
                     </div>
                     <div class="col-auto">
                         <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <!-- Filter Bar -->
 <div class="filter-bar mb-4">
     <form action="<?= SITE_URL ?>/admin/bookings" method="GET" class="row g-3 align-items-end">
         <div class="col-md-4">
             <label class="form-label">Search Bookings</label>
             <div class="search-box">
                 <input type="text" class="form-control" name="search" placeholder="Customer name, email, booking ID..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
             </div>
         </div>
         <div class="col-md-3">
             <label class="form-label">Status</label>
             <select class="form-select" name="status">
                 <option value="">All Status</option>
                 <option value="pending" <?= ($_GET['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                 <option value="confirmed" <?= ($_GET['status'] ?? '') == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                 <option value="seated" <?= ($_GET['status'] ?? '') == 'seated' ? 'selected' : '' ?>>Seated</option>
                 <option value="completed" <?= ($_GET['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                 <option value="cancelled" <?= ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                 <option value="no_show" <?= ($_GET['status'] ?? '') == 'no_show' ? 'selected' : '' ?>>No Show</option>
             </select>
         </div>
         <div class="col-md-2">
             <label class="form-label">Date</label>
             <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
         </div>
         <div class="col-md-3">
             <button type="submit" class="btn btn-primary me-2">
                 <i class="fas fa-filter me-1"></i>Filter
             </button>
             <a href="<?= SITE_URL ?>/admin/bookings" class="btn btn-outline-secondary">
                 <i class="fas fa-times me-1"></i>Clear
             </a>
         </div>
     </form>
 </div>

 <!-- Bookings Table -->
 <div class="card">
     <div class="card-header d-flex justify-content-between align-items-center">
         <h5 class="card-title mb-0">
             <i class="fas fa-calendar-alt"></i> Bookings List
         </h5>
         <div class="d-flex gap-2">
             <span class="badge bg-primary">
                 <?php echo count($bookings ?? []); ?> total
             </span>
         </div>
     </div>
     <div class="card-body">
         <?php if (!empty($bookings)): ?>
             <div class="table-responsive">
                 <table class="table table-hover align-middle" id="bookingsTable">
                     <thead class="table-light">
                         <tr>
                             <th width="40">
                                 <input type="checkbox" class="form-check-input" id="selectAll">
                             </th>
                             <th>Booking ID</th>
                             <th>Customer</th>
                             <th>Date & Time</th>
                             <th>Guests</th>
                             <th>Table</th>
                             <th>Status</th>
                             <th>Special Requests</th>
                             <th width="100">Actions</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php foreach ($bookings as $booking): ?>
                             <tr>
                                 <td>
                                     <input type="checkbox" class="form-check-input booking-checkbox"
                                         value="<?php echo $booking['id']; ?>">
                                 </td>
                                 <td>
                                     <strong class="text-primary">#<?php echo htmlspecialchars($booking['id']); ?></strong>
                                 </td>
                                 <td>
                                     <div class="d-flex align-items-center">
                                         <div class="avatar-sm me-2">
                                             <i class="fas fa-user-circle fa-2x text-muted"></i>
                                         </div>
                                         <div>
                                             <div class="fw-medium"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                                             <small class="text-muted"><?php echo htmlspecialchars($booking['customer_email'] ?? $booking['phone_number'] ?? 'No email'); ?></small>
                                         </div>
                                     </div>
                                 </td>
                                 <td>
                                     <div>
                                         <div class="fw-medium"><?php echo date('M j, Y', strtotime($booking['reservation_time'])); ?></div>
                                         <small class="text-muted"><?php echo date('g:i A', strtotime($booking['reservation_time'])); ?></small>
                                     </div>
                                 </td>
                                 <td>
                                     <span class="badge bg-light text-dark">
                                         <i class="fas fa-users"></i> <?php echo htmlspecialchars($booking['number_of_guests']); ?>
                                     </span>
                                 </td>
                                 <td>
                                     <?php if (!empty($booking['table_number'])): ?>
                                         <span class="badge bg-info">Table <?php echo htmlspecialchars($booking['table_number']); ?></span>
                                     <?php else: ?>
                                         <span class="text-muted">Not assigned</span>
                                     <?php endif; ?>
                                 </td>
                                 <td>
                                     <?php
                                        $statusBadgeClass = '';
                                        $status = $booking['status'] ?? 'pending';

                                        switch ($status) {
                                            case 'pending':
                                                $statusBadgeClass = 'bg-warning';
                                                break;
                                            case 'confirmed':
                                                $statusBadgeClass = 'bg-success';
                                                break;
                                            case 'completed':
                                                $statusBadgeClass = 'bg-primary';
                                                break;
                                            case 'cancelled':
                                                $statusBadgeClass = 'bg-danger';
                                                break;
                                            case 'no_show':
                                                $statusBadgeClass = 'bg-secondary';
                                                break;
                                            default:
                                                $statusBadgeClass = 'bg-info';
                                        }

                                        $displayStatus = ucfirst($status);
                                        ?>
                                     <span class="badge <?php echo $statusBadgeClass; ?>">
                                         <?php echo $displayStatus; ?>
                                     </span>
                                 </td>
                                 <td>
                                     <?php if (!empty($booking['notes'])): ?>
                                         <button type="button" class="btn btn-sm btn-outline-info"
                                             data-bs-toggle="tooltip"
                                             title="<?php echo htmlspecialchars($booking['notes']); ?>">
                                             <i class="fas fa-comment"></i>
                                         </button>
                                     <?php else: ?>
                                         <span class="text-muted">None</span>
                                     <?php endif; ?>
                                 </td>
                                 <td>
                                     <div class="btn-group" role="group">
                                         <button type="button"
                                             class="btn btn-sm btn-outline-primary"
                                             onclick="viewBookingDetails(<?php echo $booking['id']; ?>)"
                                             title="View Details">
                                             <i class="fas fa-eye"></i>
                                         </button>
                                         <button type="button"
                                             class="btn btn-sm btn-outline-secondary"
                                             onclick="sendConfirmationEmail(<?php echo $booking['id']; ?>)"
                                             title="Send Email">
                                             <i class="fas fa-envelope"></i>
                                         </button>
                                     </div>
                                 </td>
                             </tr>
                         <?php endforeach; ?>
                     </tbody>
                 </table>
             </div>

             <!-- Pagination -->
             <?php if (isset($totalPages) && $totalPages > 1): ?>
                 <nav aria-label="Bookings pagination" class="mt-4">
                     <ul class="pagination justify-content-center">
                         <?php if (isset($currentPage) && $currentPage > 1): ?>
                             <li class="page-item">
                                 <a class="page-link" href="?page=<?php echo ($currentPage - 1); ?>">Previous</a>
                             </li>
                         <?php endif; ?>

                         <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                             <li class="page-item <?php echo (isset($currentPage) && $i == $currentPage) ? 'active' : ''; ?>">
                                 <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                             </li>
                         <?php endfor; ?>

                         <?php if (isset($currentPage) && $currentPage < $totalPages): ?>
                             <li class="page-item">
                                 <a class="page-link" href="?page=<?php echo ($currentPage + 1); ?>">Next</a>
                             </li>
                         <?php endif; ?>
                     </ul>
                 </nav>
             <?php endif; ?>
         <?php else: ?>
             <div class="text-center py-5">
                 <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                 <h5 class="text-muted">No Bookings Found</h5>
                 <p class="text-muted">No bookings match your current filters.</p>
             </div>
         <?php endif; ?>
     </div>
 </div>

 <!-- Booking Details Modal -->
 <div class="modal fade" id="bookingDetailsModal" tabindex="-1">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">
                     <i class="fas fa-calendar-alt"></i> Booking Details
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <div class="modal-body" id="bookingDetailsContent">
                 <!-- Default content template (will be replaced by AJAX) -->
                 <div class="text-center py-4">
                     <div class="spinner-border text-primary" role="status">
                         <span class="visually-hidden">Loading...</span>
                     </div>
                     <p class="mt-2 text-muted">Loading booking details...</p>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Filter Modal -->
 <div class="modal fade" id="filterModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Filter Bookings</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <div class="modal-body">
                 <form id="filterForm" method="GET">
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="filterStatus" class="form-label">Status</label>
                                 <select class="form-select" id="filterStatus" name="status">
                                     <option value="">All Statuses</option>
                                     <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                     <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                     <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                     <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                 </select>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="filterGuests" class="form-label">Number of Guests</label>
                                 <select class="form-select" id="filterGuests" name="guests">
                                     <option value="">Any Size</option>
                                     <option value="1-2" <?php echo (isset($_GET['guests']) && $_GET['guests'] == '1-2') ? 'selected' : ''; ?>>1-2 Guests</option>
                                     <option value="3-4" <?php echo (isset($_GET['guests']) && $_GET['guests'] == '3-4') ? 'selected' : ''; ?>>3-4 Guests</option>
                                     <option value="5-8" <?php echo (isset($_GET['guests']) && $_GET['guests'] == '5-8') ? 'selected' : ''; ?>>5-8 Guests</option>
                                     <option value="9+" <?php echo (isset($_GET['guests']) && $_GET['guests'] == '9+') ? 'selected' : ''; ?>>9+ Guests</option>
                                 </select>
                             </div>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="filterDateFrom" class="form-label">Date From</label>
                                 <input type="date" class="form-control" id="filterDateFrom" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="filterDateTo" class="form-label">Date To</label>
                                 <input type="date" class="form-control" id="filterDateTo" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                             </div>
                         </div>
                     </div>
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
                 <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Table Assignment Modal -->
 <div class="modal fade" id="tableAssignModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Assign Table</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <div class="modal-body">
                 <form id="tableAssignForm">
                     <input type="hidden" id="bookingIdForTable" name="booking_id">
                     <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
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
                 <button type="button" class="btn btn-primary" onclick="saveTableAssignment()">Assign Table</button>
             </div>
         </div>
     </div>
 </div>

 <script>
     window.SITE_URL = '<?= SITE_URL ?>';

     document.addEventListener('DOMContentLoaded', function() {
         // Initialize tooltips
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
         var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
             return new bootstrap.Tooltip(tooltipTriggerEl);
         });
     });

     // Function to view booking details
     function viewBookingDetails(bookingId) {
         const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
         const bookingDetailsContent = document.getElementById('bookingDetailsContent');

         // Show loading spinner
         bookingDetailsContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading booking details...</p>
            </div>
        `;

         modal.show();

         // Fetch booking details
         fetch(`${window.SITE_URL}/admin/bookings/view/${bookingId}`)
             .then(response => response.json())
             .then(data => {
                 if (data.success && data.booking) {
                     showBookingDetailsStatic(data.booking);
                 } else {
                     bookingDetailsContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            ${data.message || 'Failed to load booking details.'}
                        </div>
                    `;
                 }
             })
             .catch(error => {
                 bookingDetailsContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        An error occurred while loading booking details.
                    </div>
                `;
                 console.error('Error fetching booking details:', error);
             });
     }

     // Function to send confirmation email
     function sendConfirmationEmail(bookingId) {
         if (!confirm('Send confirmation email to customer?')) {
             return;
         }

         fetch(`${window.SITE_URL}/admin/bookings/send-confirmation/${bookingId}`, {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json'
                 }
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     alert('Email sent successfully!');
                 } else {
                     alert(data.message || 'Failed to send email.');
                 }
             })
             .catch(error => {
                 alert('An error occurred while sending email.');
                 console.error('Error sending email:', error);
             });
     }

     // Function to export bookings
     function exportBookings() {
         // Get current filter parameters
         const urlParams = new URLSearchParams(window.location.search);
         let params = '';
         if (urlParams.toString()) {
             params = '?' + urlParams.toString() + '&export=true';
         } else {
             params = '?export=true';
         }

         // Redirect to export URL
         window.location.href = `${window.SITE_URL}/admin/bookings/export${params}`;
     }

     // Functions for filter modal
     function applyFilters() {
         document.getElementById('filterForm').submit();
     }

     function clearFilters() {
         window.location.href = `${window.SITE_URL}/admin/bookings`;
     }    // Function to show booking details in modal
    function showBookingDetailsStatic(bookingData) {
        const bookingDetailsContent = document.getElementById('bookingDetailsContent');
        const statusBadgeClass = getStatusBadgeClass(bookingData.status);

        bookingDetailsContent.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-user"></i> Customer Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>${bookingData.customer_name || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>${bookingData.customer_email || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>${bookingData.phone_number || 'N/A'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-calendar-alt"></i> Booking Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Booking ID:</strong></td>
                                    <td>#${bookingData.id}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date & Time:</strong></td>
                                    <td>${new Date(bookingData.reservation_time).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td><strong>Guests:</strong></td>
                                    <td>${bookingData.number_of_guests}</td>
                                </tr>
                                <tr>
                                    <td><strong>Table:</strong></td>
                                    <td>${bookingData.table_number ? 'Table ' + bookingData.table_number : 'Not assigned'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-${statusBadgeClass}">
                                            ${bookingData.status.charAt(0).toUpperCase() + bookingData.status.slice(1)}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            ${bookingData.notes ? `
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-comment"></i> Special Requests
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">${bookingData.notes}</p>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-envelope"></i> Communication
                            </h6>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-outline-secondary btn-sm" onclick="sendConfirmationEmail(${bookingData.id})">
                                <i class="fas fa-envelope"></i> Send Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

     // Helper function to get status badge class
     function getStatusBadgeClass(status) {
         switch (status) {
             case 'confirmed':
                 return 'success';
             case 'pending':
                 return 'warning';
             case 'cancelled':
                 return 'danger';
             case 'completed':
                 return 'primary';
             default:
                 return 'secondary';
         }
     }
 </script>

<!-- Load the fix script at the very end to ensure it overrides any problematic functions -->
<script src="<?= SITE_URL ?>/assets/js/bookings-fix.js"></script>
