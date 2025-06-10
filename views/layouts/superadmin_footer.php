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
        window.SITE_URL = '<?= SITE_URL ?>';

        // Initialize Super Admin features
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to forms
            document.querySelectorAll('form').forEach(form => {
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
                    }
                });
            });
        });
    </script>

</body>
</html>
