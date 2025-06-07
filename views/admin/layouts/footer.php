        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Custom Admin JS -->
        <script src="<?= SITE_URL ?>/assets/js/admin.js"></script>

        <script>
            // Auto-hide alerts after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    setTimeout(function() {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                });
            });


            // CSRF token for AJAX requests
            window.csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';

            // Set CSRF token for all AJAX requests
            if (window.jQuery) {
                $.ajaxSetup({
                    beforeSend: function(xhr, settings) {
                        if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                            xhr.setRequestHeader("X-CSRFToken", window.csrfToken);
                        }
                    }
                });
            }
            // Initialize DataTables if present
            $(document).ready(function() {
                if ($.fn.DataTable && $('table.dataTable').length > 0) {
                    $('table.dataTable').each(function() {
                        const tableId = $(this).attr('id');

                        // Check if DataTable is already initialized
                        if (!$.fn.DataTable.isDataTable(this)) {
                            let config = {
                                responsive: true,
                                pageLength: 10,
                                processing: true,
                                language: {
                                    search: "_INPUT_",
                                    searchPlaceholder: "Search...",
                                    lengthMenu: "Show _MENU_ entries",
                                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                                    infoEmpty: "No entries found",
                                    emptyTable: "No data available in table",
                                    zeroRecords: "No matching records found",
                                    paginate: {
                                        first: "First",
                                        last: "Last",
                                        next: "Next",
                                        previous: "Previous"
                                    }
                                },
                                drawCallback: function() {
                                    // Reinitialize tooltips after table redraw
                                    $('[data-bs-toggle="tooltip"]').tooltip();
                                }
                            };

                            // Custom configuration for specific tables
                            switch(tableId) {
                                case 'usersTable':
                                case 'foodsTable':
                                case 'ordersTable':
                                case 'categoriesTable':
                                    config.order = [[1, 'desc']]; // Sort by ID column
                                    config.columnDefs = [
                                        {
                                            targets: 0, // Checkbox column
                                            orderable: false,
                                            searchable: false,
                                            className: 'text-center'
                                        },
                                        {
                                            targets: -1, // Actions column
                                            orderable: false,
                                            searchable: false,
                                            className: 'text-center'
                                        }
                                    ];
                                    break;
                                default:
                                    // Default configuration for other tables
                                    config.order = [[0, 'desc']];
                                    break;
                            }

                            try {
                                $(this).DataTable(config);
                                console.log(`DataTable initialized successfully for ${tableId || 'unnamed table'}`);
                            } catch (error) {
                                console.error(`Failed to initialize DataTable for ${tableId || 'unnamed table'}:`, error);
                            }
                        }
                    });
                }
            });            // Debug function for DataTables issues (remove in production)
            window.debugDataTables = function() {
                console.log('DataTables Debug Info:');
                $('table.dataTable').each(function(index) {
                    const tableId = this.id || 'table-' + index;
                    const isInitialized = $.fn.DataTable.isDataTable(this);
                    console.log(`Table ${tableId}: ${isInitialized ? 'Initialized' : 'Not initialized'}`);
                });
            };
        </script>
        </div>
    </div>
</body>
</html>
