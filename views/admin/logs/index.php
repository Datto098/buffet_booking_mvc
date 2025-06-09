<?php
/**
 * Admin Logs Management - Index View
 */
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-alt me-2"></i>System Logs
            </h1>
            <p class="text-muted mb-0">Monitor and manage application logs</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary" onclick="refreshLogs()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Log Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Log Files
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($logFiles) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Size
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $totalSize = array_sum(array_column($logFiles, 'size'));
                                echo $totalSize > 1024 * 1024 ?
                                    number_format($totalSize / 1024 / 1024, 2) . ' MB' :
                                    number_format($totalSize / 1024, 2) . ' KB';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Recent Entries
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($recentLogs) ?>
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Error Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $errorCount = count(array_filter($recentLogs, function($log) {
                                    return $log['level'] === 'ERROR';
                                }));
                                $errorRate = count($recentLogs) > 0 ?
                                    round(($errorCount / count($recentLogs)) * 100, 1) : 0;
                                echo $errorRate . '%';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Log Files List -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-folder-open me-2"></i>Log Files
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($logFiles)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No log files found</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Size</th>
                                        <th>Modified</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logFiles as $logFile): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($logFile['name']) ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars($logFile['path']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?= $logFile['size'] > 1024 * 1024 ?
                                                        number_format($logFile['size'] / 1024 / 1024, 2) . ' MB' :
                                                        number_format($logFile['size'] / 1024, 2) . ' KB' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('Y-m-d H:i', $logFile['modified']) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group-sm">
                                                    <a href="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile['path']) ?>"
                                                       class="btn btn-sm btn-outline-primary" title="View Log">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= SITE_URL ?>/admin/logs/download/<?= urlencode($logFile['path']) ?>"
                                                       class="btn btn-sm btn-outline-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <?php if ($logFile['writable']): ?>
                                                        <button onclick="confirmClearLog('<?= htmlspecialchars($logFile['path']) ?>')"
                                                                class="btn btn-sm btn-outline-danger" title="Clear Log">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Log Entries -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list-alt me-2"></i>Recent Log Entries
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="filterRecentLogs('all')">All Levels</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterRecentLogs('ERROR')">Errors Only</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterRecentLogs('WARNING')">Warnings Only</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterRecentLogs('INFO')">Info Only</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentLogs)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-list-alt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No recent log entries found</p>
                        </div>
                    <?php else: ?>
                        <div class="log-entries" style="max-height: 500px; overflow-y: auto;">
                            <?php foreach ($recentLogs as $log): ?>
                                <div class="log-entry border-bottom p-3 log-level-<?= strtolower($log['level']) ?>"
                                     data-level="<?= $log['level'] ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge badge-<?= getLogLevelClass($log['level']) ?> me-2">
                                                    <?= $log['level'] ?>
                                                </span>
                                                <small class="text-muted">
                                                    <?= $log['file'] ?> •
                                                    <?= $log['timestamp'] ?: 'No timestamp' ?>
                                                </small>
                                            </div>
                                            <div class="log-message">
                                                <?= htmlspecialchars($log['message']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clear Log Confirmation Modal -->
<div class="modal fade" id="clearLogModal" tabindex="-1" aria-labelledby="clearLogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearLogModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>Clear Log File
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to clear this log file?</p>
                <p class="text-muted mb-0">
                    <strong>File:</strong> <span id="logFileToDelete"></span><br>
                    <strong>Warning:</strong> This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="clearLogFile()">
                    <i class="fas fa-trash me-1"></i>Clear Log
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let logFileToDelete = '';

function confirmClearLog(logPath) {
    logFileToDelete = logPath;
    document.getElementById('logFileToDelete').textContent = logPath;

    const modal = new bootstrap.Modal(document.getElementById('clearLogModal'));
    modal.show();
}

function clearLogFile() {
    if (!logFileToDelete) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `<?= SITE_URL ?>/admin/logs/clear/${encodeURIComponent(logFileToDelete)}`;

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?= $csrf_token ?>';
    form.appendChild(csrfInput);

    document.body.appendChild(form);
    form.submit();
}

function refreshLogs() {
    window.location.reload();
}

function filterRecentLogs(level) {
    const entries = document.querySelectorAll('.log-entry');

    entries.forEach(entry => {
        if (level === 'all' || entry.dataset.level === level) {
            entry.style.display = 'block';
        } else {
            entry.style.display = 'none';
        }
    });
}

// Auto-refresh every 30 seconds
setInterval(refreshLogs, 30000);
</script>

<style>
.log-level-error { border-left: 4px solid #dc3545; }
.log-level-warning { border-left: 4px solid #ffc107; }
.log-level-info { border-left: 4px solid #17a2b8; }
.log-level-debug { border-left: 4px solid #6c757d; }

.badge-ERROR { background-color: #dc3545; }
.badge-WARNING { background-color: #ffc107; color: #000; }
.badge-INFO { background-color: #17a2b8; }
.badge-DEBUG { background-color: #6c757d; }

.log-message {
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    word-break: break-word;
}

.log-entries {
    border-top: 1px solid #e3e6f0;
}

.log-entry:hover {
    background-color: #f8f9fc;
}
</style>

<?php
function getLogLevelClass($level) {
    switch (strtoupper($level)) {
        case 'ERROR': return 'ERROR';
        case 'WARNING': case 'WARN': return 'WARNING';
        case 'INFO': return 'INFO';
        case 'DEBUG': return 'DEBUG';
        default: return 'INFO';
    }
}
?>
