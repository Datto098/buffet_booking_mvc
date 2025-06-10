<?php
/**
 * Admin Logs Management - Log File View
 */

function getLogLevelClass($level) {
    switch (strtoupper($level)) {
        case 'ERROR':
            return 'danger';
        case 'WARNING':
        case 'WARN':
            return 'warning';
        case 'INFO':
            return 'info';
        case 'DEBUG':
            return 'secondary';
        default:
            return 'light';
    }
}
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= SITE_URL ?>/admin/logs">
                            <i class="fas fa-file-alt me-1"></i>System Logs
                        </a>
                    </li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars(basename($logFile)) ?></li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye me-2"></i>View Log: <?= htmlspecialchars(basename($logFile)) ?>
            </h1>
            <p class="text-muted mb-0">
                Total Lines: <?= number_format($logData['totalLines']) ?> |
                Page <?= $currentPage ?> of <?= $logData['totalPages'] ?>
            </p>
        </div>
        <div>
            <a href="<?= SITE_URL ?>/admin/logs/download/<?= urlencode($logFile) ?>"
               class="btn btn-success me-2">
                <i class="fas fa-download me-1"></i>Download
            </a>
            <a href="<?= SITE_URL ?>/admin/logs" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Logs
            </a>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile) ?>" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Term</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="<?= htmlspecialchars($searchTerm) ?>"
                                   placeholder="Search in log entries...">
                        </div>
                        <div class="col-md-3">
                            <label for="level" class="form-label">Log Level</label>
                            <select class="form-select" id="level" name="level">
                                <option value="">All Levels</option>
                                <option value="ERROR" <?= $level === 'ERROR' ? 'selected' : '' ?>>Error</option>
                                <option value="WARNING" <?= $level === 'WARNING' ? 'selected' : '' ?>>Warning</option>
                                <option value="INFO" <?= $level === 'INFO' ? 'selected' : '' ?>>Info</option>
                                <option value="DEBUG" <?= $level === 'DEBUG' ? 'selected' : '' ?>>Debug</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="page" class="form-label">Page</label>
                            <input type="number" class="form-control" id="page" name="page"
                                   value="<?= $currentPage ?>" min="1" max="<?= $logData['totalPages'] ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            <a href="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile) ?>"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Log Entries
                        <?php if ($searchTerm): ?>
                            - Filtered by: "<?= htmlspecialchars($searchTerm) ?>"
                        <?php endif; ?>
                        <?php if ($level): ?>
                            - Level: <?= $level ?>
                        <?php endif; ?>
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>Options
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="toggleLineNumbers()">
                                    <i class="fas fa-list-ol me-2"></i>Toggle Line Numbers
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="toggleWordWrap()">
                                    <i class="fas fa-align-left me-2"></i>Toggle Word Wrap
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= SITE_URL ?>/admin/logs/download/<?= urlencode($logFile) ?>">
                                    <i class="fas fa-download me-2"></i>Download File
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($logData['lines'])): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No log entries found</p>
                            <?php if ($searchTerm || $level): ?>
                                <a href="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile) ?>"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="log-viewer" id="logViewer">
                            <?php foreach ($logData['lines'] as $line): ?>
                                <div class="log-line d-flex" data-level="<?= $line['level'] ?>">
                                    <div class="line-number text-muted">
                                        <?= $line['number'] ?>
                                    </div>
                                    <div class="line-content flex-grow-1">
                                        <div class="d-flex align-items-start">
                                            <?php if ($line['timestamp']): ?>
                                                <span class="timestamp text-muted me-2">
                                                    <?= htmlspecialchars($line['timestamp']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="badge badge-<?= getLogLevelClass($line['level']) ?> me-2">
                                                <?= $line['level'] ?>
                                            </span>
                                            <div class="message flex-grow-1">
                                                <?= htmlspecialchars($line['message']) ?>
                                            </div>
                                        </div>
                                        <?php if ($line['context']): ?>
                                            <div class="context text-muted small mt-1">
                                                <?= htmlspecialchars($line['context']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($logData['totalPages'] > 1): ?>
        <div class="row">
            <div class="col-12">
                <nav aria-label="Log pagination">
                    <ul class="pagination justify-content-center">
                        <!-- First Page -->
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile) ?>?page=1<?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?><?= $level ? '&level=' . $level : '' ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile) ?>?page=<?= $currentPage - 1 ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?><?= $level ? '&level=' . $level : '' ?>">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($logData['totalPages'], $currentPage + 2);

                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile) ?>?page=<?= $i ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?><?= $level ? '&level=' . $level : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next/Last Page -->
                        <?php if ($currentPage < $logData['totalPages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile) ?>?page=<?= $currentPage + 1 ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?><?= $level ? '&level=' . $level : '' ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= SITE_URL ?>/admin/logs/view/<?= urlencode($logFile) ?>?page=<?= $logData['totalPages'] ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?><?= $level ? '&level=' . $level : '' ?>">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.log-viewer {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    max-height: 70vh;
    overflow-y: auto;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.log-line {
    border-bottom: 1px solid #e9ecef;
    padding: 8px 12px;
    transition: background-color 0.2s;
}

.log-line:hover {
    background-color: #e9ecef;
}

.line-number {
    min-width: 60px;
    text-align: right;
    padding-right: 12px;
    font-size: 11px;
    user-select: none;
    border-right: 1px solid #dee2e6;
    margin-right: 12px;
}

.line-content {
    white-space: pre-wrap;
    word-break: break-word;
}

.timestamp {
    font-size: 11px;
    min-width: 140px;
}

.message {
    line-height: 1.4;
}

.context {
    padding-left: 20px;
    border-left: 2px solid #6c757d;
    margin-left: 10px;
}

/* Log level specific colors */
.log-line[data-level="ERROR"] {
    border-left: 3px solid #dc3545;
}

.log-line[data-level="WARNING"],
.log-line[data-level="WARN"] {
    border-left: 3px solid #ffc107;
}

.log-line[data-level="INFO"] {
    border-left: 3px solid #17a2b8;
}

.log-line[data-level="DEBUG"] {
    border-left: 3px solid #6c757d;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .line-number {
        display: none;
    }

    .timestamp {
        min-width: auto;
        font-size: 10px;
    }

    .log-viewer {
        font-size: 12px;
    }
}

/* Word wrap toggle */
.log-viewer.nowrap .line-content {
    white-space: nowrap;
    overflow-x: auto;
}
</style>

<script>
let showLineNumbers = true;
let wordWrap = true;

function toggleLineNumbers() {
    showLineNumbers = !showLineNumbers;
    const lineNumbers = document.querySelectorAll('.line-number');
    lineNumbers.forEach(el => {
        el.style.display = showLineNumbers ? 'block' : 'none';
    });
}

function toggleWordWrap() {
    wordWrap = !wordWrap;
    const logViewer = document.getElementById('logViewer');
    if (wordWrap) {
        logViewer.classList.remove('nowrap');
    } else {
        logViewer.classList.add('nowrap');
    }
}

// Auto-scroll to bottom on page load if it's the last page
document.addEventListener('DOMContentLoaded', function() {
    const logViewer = document.getElementById('logViewer');
    const currentPage = <?= $currentPage ?>;
    const totalPages = <?= $logData['totalPages'] ?>;

    // If we're on the last page and no search filters, scroll to bottom
    if (currentPage === totalPages && !<?= json_encode($searchTerm || $level) ?>) {
        logViewer.scrollTop = logViewer.scrollHeight;
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('search').focus();
    }

    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('search');
        if (searchInput === document.activeElement) {
            searchInput.value = '';
        }
    }
});
</script>
