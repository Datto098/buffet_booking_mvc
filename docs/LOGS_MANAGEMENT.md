# Logs Management System Documentation

## Overview

The Logs Management System provides a comprehensive interface for administrators to monitor, view, and manage application logs within the Buffet Booking MVC application. This system was created to replace the previous `/admin/logs` URL that was incorrectly showing the dashboard.

## Features

### 1. **Logs Dashboard** (`/admin/logs`)
- **Statistics Cards**: Display total log files, total size, recent entries count, and error rate
- **Log Files List**: Shows all discovered log files with metadata (size, modification date, permissions)
- **Recent Log Entries**: Displays the most recent log entries across all files with level filtering
- **Auto-refresh**: Automatically refreshes every 30 seconds
- **Action Buttons**: View, download, and clear log files

### 2. **Log File Viewer** (`/admin/logs/view/{logfile}`)
- **Detailed View**: View individual log files with pagination
- **Filtering**: Filter by log level (ERROR, WARNING, INFO, DEBUG) and search terms
- **Line Numbers**: Toggle line numbers on/off
- **Word Wrap**: Toggle word wrapping for long lines
- **Syntax Highlighting**: Color-coded log levels for easy identification
- **Navigation**: Breadcrumb navigation and pagination controls

### 3. **Log File Management**
- **Download**: Download log files for offline analysis
- **Clear**: Safely clear log files with CSRF protection
- **Security**: Path traversal protection and file type validation

## Architecture

### Controller Methods (AdminController.php)

#### Public Methods
- `logs()` - Main logs dashboard
- `viewLog($logFile)` - View individual log file
- `downloadLog($logFile)` - Download log file
- `clearLog($logFile)` - Clear log file contents

#### Private Helper Methods
- `getLogFiles()` - Discover and return available log files
- `getLogPath($logFile)` - Safely resolve log file paths
- `readLogFile($logPath, $page, $linesPerPage, $searchTerm, $level)` - Read and parse log files
- `parseLogLine($line, $lineNumber)` - Parse individual log lines
- `getRecentLogEntries($limit)` - Get recent entries across all log files
- `logLineMatchesLevel($line, $level)` - Check if log line matches specific level

### Routing (index.php)

```php
function handleAdminLogsRoute($controller, $action, $param)
{
    switch ($action) {
        case 'download':
            $controller->downloadLog($param);
            break;
        case 'clear':
            $controller->clearLog($param);
            break;
        case 'view':
            $controller->viewLog($param);
            break;
        default:
            $controller->logs();
    }
}
```

### View Files

1. **views/admin/logs/index.php** - Main logs dashboard
2. **views/admin/logs/view.php** - Individual log file viewer

## Security Features

### 1. **Authentication & Authorization**
- Requires admin or manager role
- Session validation for all operations

### 2. **Path Security**
- Path traversal attack prevention (`../../../etc/passwd`)
- File type validation (only `.log` files)
- Real path validation within allowed directories

### 3. **CSRF Protection**
- CSRF tokens for destructive operations (clear logs)
- Form validation for all POST requests

### 4. **Input Sanitization**
- File path sanitization
- Search term sanitization
- XSS prevention in output

## Supported Log Formats

The system can parse various log formats:

1. **Standard Format**: `[2024-12-15 10:30:15] [INFO] Message`
2. **Simple Format**: `2024-12-15 10:30:16 ERROR: Message`
3. **Level Only**: `[WARNING] Message`
4. **Plain Text**: `DEBUG: Message`

### Log Levels Supported
- **ERROR** - Error messages, fatal errors, parse errors
- **WARNING/WARN** - Warning messages
- **INFO** - Informational messages
- **DEBUG** - Debug messages

## Log File Discovery

The system automatically discovers log files in these locations:
- Root directory: `*.log`
- Logs directory: `logs/*.log`
- Storage logs: `storage/logs/*.log`
- Var logs: `var/log/*.log`

## Usage Examples

### Viewing Logs Dashboard
```
URL: http://localhost/buffet_booking_mvc/admin/logs
```

### Viewing Specific Log File
```
URL: http://localhost/buffet_booking_mvc/admin/logs/view/debug.log
URL: http://localhost/buffet_booking_mvc/admin/logs/view/logs%2Fapplication.log
```

### Downloading Log File
```
URL: http://localhost/buffet_booking_mvc/admin/logs/download/debug.log
```

### Clearing Log File (POST only)
```
POST: http://localhost/buffet_booking_mvc/admin/logs/clear/debug.log
Data: csrf_token=xxx
```

## Configuration

### Environment Requirements
- PHP 7.4+
- Read/write permissions on log directories
- Bootstrap 5.x for UI
- Font Awesome for icons

### Log File Permissions
- **Readable**: Required for viewing and downloading
- **Writable**: Required for clearing log files

## User Interface Features

### Dashboard Cards
1. **Log Files Count** - Total number of log files found
2. **Total Size** - Combined size of all log files
3. **Recent Entries** - Count of recent log entries
4. **Error Rate** - Percentage of ERROR level entries

### Interactive Features
- **Auto-refresh**: Refreshes page every 30 seconds
- **Level Filtering**: Filter recent entries by log level
- **Search**: Search within log entries
- **Responsive Design**: Mobile-friendly interface

### Visual Indicators
- **Color-coded levels**: Different colors for each log level
- **File size formatting**: Human-readable file sizes
- **Timestamp formatting**: Consistent date/time display
- **Status badges**: Visual indicators for file permissions

## Error Handling

### Common Error Scenarios
1. **File Not Found**: Graceful handling with user-friendly messages
2. **Permission Denied**: Clear indication of permission issues
3. **Invalid File Paths**: Security validation with proper error messages
4. **Large Files**: Pagination for handling large log files

### Error Messages
- Flash messages for user feedback
- Detailed error logging for debugging
- Graceful degradation for missing features

## Testing

### Automated Tests
Run the comprehensive test suite:
```
URL: http://localhost/buffet_booking_mvc/test_logs_comprehensive.php
```

### Manual Testing Checklist
1. ✅ Access logs dashboard
2. ✅ View individual log files
3. ✅ Download log files
4. ✅ Clear log files (with confirmation)
5. ✅ Filter and search functionality
6. ✅ Pagination for large files
7. ✅ Security restrictions
8. ✅ Error handling

## Troubleshooting

### Common Issues

#### 1. No Log Files Found
- Check file permissions
- Verify log file locations
- Ensure log files have `.log` extension

#### 2. View Page Not Loading
- Verify `views/admin/logs/view.php` exists
- Check for PHP syntax errors
- Ensure proper routing configuration

#### 3. Download Not Working
- Check file permissions
- Verify file exists and is readable
- Check for path traversal attempts

#### 4. Clear Function Not Working
- Verify CSRF token
- Check file write permissions
- Ensure proper POST request format

### Debug Mode
Enable debug logging by checking the `debug.log` file for detailed error messages and execution traces.

## Future Enhancements

### Potential Improvements
1. **Real-time Log Streaming** - WebSocket-based live log viewing
2. **Log Rotation Management** - Automated log rotation and archiving
3. **Advanced Filtering** - Date range filters, regex patterns
4. **Export Options** - Export filtered logs to CSV/JSON
5. **Log Analytics** - Graphical representation of log data
6. **Alert System** - Email notifications for critical errors
7. **Log Backup** - Automated backup of important log files

### Performance Optimizations
1. **Caching** - Cache parsed log data for faster access
2. **Indexing** - Create searchable indexes for large log files
3. **Compression** - Compress old log files to save space
4. **Background Processing** - Process large files in background

## Conclusion

The Logs Management System provides a robust, secure, and user-friendly interface for monitoring application logs. It successfully replaces the previous dashboard fallback with proper logging functionality, enabling administrators to effectively monitor system health and troubleshoot issues.

---

**Last Updated**: December 2024
**Version**: 1.0
**Compatibility**: PHP 7.4+, Bootstrap 5.x
