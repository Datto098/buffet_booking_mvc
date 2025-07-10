        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Custom Admin JS -->
        <script src="<?= SITE_URL ?>/assets/js/admin.js"></script>

        <!-- Global variables for JavaScript -->
        <script>
            window.SITE_URL = '<?= SITE_URL ?>';
            window.csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';
        </script>
        </div>
    </div>
</body>
</html>
