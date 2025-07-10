<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'views/layouts/superadmin_header.php'; ?>
    <title>Booking Management - Super Admin</title>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once 'views/layouts/superadmin_sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex align-items-center mb-4">
                    <i class="fas fa-map-marker-alt page-header-icon me-3"></i>
                    <div>
                        <h3 class="page-title mb-0">Quản Lý Địa Chỉ</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/superadmin/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Address</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="ms-auto">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-1"></i> Thêm Địa Chỉ
                        </button>
                    </div>
                </div>

                <!-- Table Address -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <!-- Search & Filter -->
                        <form class="row g-3 mb-3" id="addressFilterForm" autocomplete="off" onsubmit="return false;">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="addressSearchInput" placeholder="Tìm kiếm địa chỉ, tên đường, quận...">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="cityFilterSelect">
                                    <option value="">-- Lọc theo Tỉnh/Thành Phố --</option>
                                    <?php
                                    $cities = [];
                                    foreach (($addresses ?? []) as $address) {
                                        $city = '';
                                        if (preg_match('/Thành phố ([^,]+)/u', $address['address'], $m)) {
                                            $city = trim($m[1]);
                                        } elseif (preg_match('/Tỉnh ([^,]+)/u', $address['address'], $m)) {
                                            $city = trim($m[1]);
                                        } else {
                                            $parts = explode(',', $address['address']);
                                            if (count($parts) > 1) {
                                                $last = trim($parts[count($parts)-2]);
                                                if ($last !== 'Việt Nam') $city = $last;
                                            }
                                        }
                                        if ($city) $cities[] = $city;
                                    }
                                    $cities = array_unique($cities);
                                    foreach ($cities as $city) {
                                        echo "<option value=\"$city\">$city</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Bỏ nút search -->
                        </form>
                        <table class="table table-hover align-middle" id="addressTable">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>Địa Chỉ</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($addresses ?? [] as $address): ?>
                                    <tr>
                                        <td><?= $address['id'] ?></td>
                                        <td class="address-text"><?= htmlspecialchars($address['address']) ?></td>
                                        <td>
                                            <?php if ($address['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-warning ms-2 toggle-status-btn" data-id="<?= $address['id'] ?>" data-status="<?= $address['status'] ?>">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger delete-address-btn" data-id="<?= $address['id'] ?>"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Thêm Địa Chỉ -->
                <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form class="modal-content" method="post" action="<?= SITE_URL ?>/superadmin/address/add">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addAddressModalLabel"><i class="fas fa-plus"></i> Thêm Địa Chỉ Mới</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Địa chỉ (nhập hoặc chọn trên bản đồ)</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="address" id="addressInput" required placeholder="Nhập địa chỉ, tên địa điểm, đường, quận...">
                                    </div>
                                    <label class="form-label">Chọn vị trí trên bản đồ</label>
                                    <div id="map" style="height: 270px; border-radius: 12px; border: 1px solid #ccc;"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-select" name="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" style="min-width: 100px; font-weight: 600;"><i class="fas fa-save"></i> Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Map Script -->
                <script>
                function removeZipcode(address) {
                    return address.replace(/,\s*\d{4,6}(?=,|$)/, '');
                }

                var mapModal = document.getElementById('addAddressModal');
                var map, marker, geocoder;
                mapModal.addEventListener('shown.bs.modal', function () {
                    setTimeout(function() {
                        if (!map) {
                            map = L.map('map').setView([10.762622, 106.660172], 13);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(map);
                            geocoder = L.Control.geocoder({
                                defaultMarkGeocode: false
                            })
                            .on('markgeocode', function(e) {
                                var latlng = e.geocode.center;
                                map.setView(latlng, 16);
                                if (marker) map.removeLayer(marker);
                                var cleanAddress = removeZipcode(e.geocode.name);
                                marker = L.marker(latlng).addTo(map).bindPopup(cleanAddress).openPopup();
                                document.getElementById('addressInput').value = cleanAddress;
                            })
                            .addTo(map);
                            map.on('click', function(e) {
                                if (marker) map.removeLayer(marker);
                                marker = L.marker(e.latlng).addTo(map);
                                // Reverse geocode
                                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        var address = data.display_name || (e.latlng.lat + ',' + e.latlng.lng);
                                        var cleanAddress = removeZipcode(address);
                                        marker.bindPopup(cleanAddress).openPopup();
                                        document.getElementById('addressInput').value = cleanAddress;
                                    });
                            });
                        } else {
                            map.invalidateSize();
                        }

                        // Chỉ còn sự kiện enter để không chặn submit form
                        var addressInput = document.getElementById('addressInput');
                        if (addressInput) {
                            addressInput.onkeydown = null;
                        }
                    }, 300);
                });
                mapModal.addEventListener('hidden.bs.modal', function () {
                    if (map) {
                        map.remove();
                        map = null;
                        marker = null;
                        geocoder = null;
                        document.getElementById('map').innerHTML = "";
                    }
                });
                // Search event for addressInput
                var addressInput = document.getElementById('addressInput');
                var searchBtn = document.getElementById('searchBtn');
                function doSearch() {
                    if (geocoder && addressInput.value.trim()) {
                        geocoder.options.geocoder.geocode(addressInput.value, function(results) {
                            if (results && results.length > 0) {
                                var r = results[0];
                                map.setView(r.center, 16);
                                if (marker) map.removeLayer(marker);
                                var cleanAddress = removeZipcode(r.name);
                                marker = L.marker(r.center).addTo(map).bindPopup(cleanAddress).openPopup();
                                addressInput.value = cleanAddress;
                            }
                        });
                    }
                }
                if (addressInput) {
                    addressInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            doSearch();
                        }
                    });
                }
                </script>
            </main>
        </div>
    </div>

    <?php require_once 'views/layouts/superadmin_footer.php'; ?>
    <script>
    // Toggle status AJAX
    $(document).on('click', '.toggle-status-btn', function() {
        var btn = $(this);
        var id = btn.data('id');
        var currentStatus = btn.data('status');
        var newStatus = currentStatus == 1 ? 0 : 1;
        $.post('<?= SITE_URL ?>/superadmin/address/toggle-status', {id: id, status: newStatus}, function(res) {
            location.reload();
        });
    });
    // Delete address AJAX
    $(document).on('click', '.delete-address-btn', function() {
        if (!confirm('Bạn có chắc muốn xóa địa chỉ này?')) return;
        var id = $(this).data('id');
        $.post('<?= SITE_URL ?>/superadmin/address/delete', {id: id}, function(res) {
            location.reload();
        });
    });

    // Lọc bảng địa chỉ bằng JS
    function filterAddressTable() {
        var search = document.getElementById('addressSearchInput').value.toLowerCase();
        var city = document.getElementById('cityFilterSelect').value;
        var rows = document.querySelectorAll('#addressTable tbody tr');
        rows.forEach(function(row) {
            var address = row.querySelector('.address-text').textContent.toLowerCase();
            var show = true;
            if (search && address.indexOf(search) === -1) show = false;
            if (city && address.indexOf(city.toLowerCase()) === -1) show = false;
            row.style.display = show ? '' : 'none';
        });
    }
    document.getElementById('addressSearchInput').addEventListener('input', filterAddressTable);
    document.getElementById('cityFilterSelect').addEventListener('change', filterAddressTable);
    </script>
</body>
</html>