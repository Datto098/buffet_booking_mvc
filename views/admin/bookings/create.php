<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/admin/layouts/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/admin/layouts/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show mt-3" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h1 class="h2">
                            <i class="fas fa-plus-circle me-2"></i>Create New Booking
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/admin/bookings">Bookings</a></li>
                                <li class="breadcrumb-item active">Create Booking</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?= SITE_URL ?>/admin/bookings" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bookings
                        </a>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Booking Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="bookingForm" action="<?= SITE_URL ?>/admin/bookings/store" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                    <!-- Customer Information -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="text-primary mb-3">
                                                <i class="fas fa-user me-2"></i>Customer Information
                                            </h6>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="customer_name" class="form-label">
                                                Customer Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="customer_name"
                                                   name="customer_name"
                                                   required
                                                   maxlength="100"
                                                   value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="phone_number" class="form-label">
                                                Phone Number <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel"
                                                   class="form-control"
                                                   id="phone_number"
                                                   name="phone_number"
                                                   required
                                                   pattern="[0-9\-\+\(\)\s]+"
                                                   value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="customer_email" class="form-label">Email (Optional)</label>
                                            <input type="email"
                                                   class="form-control"
                                                   id="customer_email"
                                                   name="customer_email"
                                                   value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="number_of_guests" class="form-label">
                                                Number of Guests <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="number_of_guests" name="number_of_guests" required>
                                                <option value="">Select party size</option>
                                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                                    <option value="<?= $i ?>" <?= ($_POST['number_of_guests'] ?? '') == $i ? 'selected' : '' ?>>
                                                        <?= $i ?> <?= $i == 1 ? 'Guest' : 'Guests' ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Reservation Details -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="text-primary mb-3">
                                                <i class="fas fa-calendar-alt me-2"></i>Reservation Details
                                            </h6>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="reservation_date" class="form-label">
                                                Reservation Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date"
                                                   class="form-control"
                                                   id="reservation_date"
                                                   name="reservation_date"
                                                   required
                                                   min="<?= date('Y-m-d') ?>"
                                                   value="<?= htmlspecialchars($_POST['reservation_date'] ?? '') ?>">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="reservation_time" class="form-label">
                                                Reservation Time <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="reservation_time" name="reservation_time" required>
                                                <option value="">Select time</option>
                                                <?php
                                                $start_hour = 10; // 10 AM
                                                $end_hour = 22;   // 10 PM
                                                for ($hour = $start_hour; $hour <= $end_hour; $hour++) {
                                                    for ($minute = 0; $minute < 60; $minute += 30) {
                                                        $time_24 = sprintf('%02d:%02d', $hour, $minute);
                                                        $time_12 = date('g:i A', strtotime($time_24));
                                                        $selected = ($_POST['reservation_time'] ?? '') == $time_24 ? 'selected' : '';
                                                        echo "<option value=\"{$time_24}\" {$selected}>{$time_12}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="table_id" class="form-label">Preferred Table</label>
                                            <select class="form-select" id="table_id" name="table_id">
                                                <option value="">Auto-assign table</option>
                                                <?php if (isset($available_tables) && !empty($available_tables)): ?>
                                                    <?php foreach ($available_tables as $table): ?>
                                                        <option value="<?= $table['id'] ?>"
                                                                data-capacity="<?= $table['capacity'] ?>"
                                                                <?= ($_POST['table_id'] ?? '') == $table['id'] ? 'selected' : '' ?>>
                                                            Table <?= $table['table_number'] ?> (<?= $table['capacity'] ?> seats) - <?= $table['location'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <div class="form-text">Tables will be filtered based on party size and availability</div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="pending" <?= ($_POST['status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="confirmed" <?= ($_POST['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                <option value="cancelled" <?= ($_POST['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Special Requests -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="text-primary mb-3">
                                                <i class="fas fa-comment-alt me-2"></i>Special Requests
                                            </h6>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control"
                                                      id="notes"
                                                      name="notes"
                                                      rows="3"
                                                      maxlength="500"
                                                      placeholder="Any special requests, dietary restrictions, or additional notes..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                                            <div class="form-text" id="notesCount">0/500 characters</div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                        <div>
                                            <button type="button" class="btn btn-outline-primary me-2" onclick="checkAvailability()">
                                                <i class="fas fa-search"></i> Check Availability
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Create Booking
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Booking Summary -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-clipboard-list me-2"></i>Booking Summary
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="bookingSummary">
                                    <div class="text-muted text-center py-3">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                                        Fill the form to see booking summary
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guidelines Card -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-lightbulb me-2"></i>Booking Guidelines
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 small">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Verify customer contact information
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Confirm date and time availability
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Select appropriate table size
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Note any special requirements
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Send confirmation to customer
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Available Tables -->
                        <div class="card shadow" id="availableTablesCard" style="display: none;">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chair me-2"></i>Available Tables
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="availableTablesList">
                                    <!-- Available tables will be shown here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>    <?php require_once 'views/admin/layouts/footer.php'; ?>

    <script>
        window.SITE_URL = '<?= SITE_URL ?>';

        document.addEventListener('DOMContentLoaded', function() {
            initializeBookingForm();
        });

        // Override resetForm to use the consolidated function
        window.resetForm = resetBookingForm;
    </script>
</body>
</html>
