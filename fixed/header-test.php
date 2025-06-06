<?php
// Simple test page to check header rendering
define('SITE_NAME', 'Buffet Booking');
define('SITE_URL', 'http://localhost:8080');

$title = 'Test Page - ' . SITE_NAME;
$meta_description = 'Test page for CSS debugging';

include 'views/layouts/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="card luxury-card">
                <div class="card-body">
                    <h1 class="luxury-title">Header CSS Test</h1>
                    <p class="text-secondary">This page tests if the header is rendering with proper CSS styles.</p>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card border-luxury">
                                <div class="card-body text-center">
                                    <i class="fas fa-crown luxury-icon mb-3"></i>
                                    <h5 class="luxury-title">Luxury Icon</h5>
                                    <p class="text-muted">Testing luxury styling</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-luxury">
                                <div class="card-body text-center">
                                    <i class="fas fa-utensils luxury-icon mb-3"></i>
                                    <h5 class="luxury-title">Menu Icon</h5>
                                    <p class="text-muted">Testing navigation styles</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-luxury">
                                <div class="card-body text-center">
                                    <i class="fas fa-concierge-bell luxury-icon mb-3"></i>
                                    <h5 class="luxury-title">Service Icon</h5>
                                    <p class="text-muted">Testing button styles</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <button class="btn btn-luxury me-3">
                            <i class="fas fa-star"></i> Luxury Button
                        </button>
                        <button class="btn btn-outline-luxury">
                            <i class="fas fa-heart"></i> Outline Button
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Luxury Effects JS -->
<script src="<?php echo SITE_URL; ?>/assets/js/luxury-effects.js"></script>

</body>
</html>
