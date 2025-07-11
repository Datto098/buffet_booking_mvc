<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
    <title><?= $title ?? 'Super Admin' ?> - <?= SITE_NAME ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Internal Messages CSS -->
    <link href="<?= SITE_URL ?>/assets/css/internal-messages.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --secondary-color: #64748b;
            --accent-color: #8b5cf6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --box-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 50%, #cbd5e1 100%);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.6;
            color: var(--gray-700);
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            padding: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e293b 0%, #334155 30%, #475569 70%, #64748b 100%);
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(37, 99, 235, 0.1) 0%, transparent 50%, rgba(139, 92, 246, 0.1) 100%);
            pointer-events: none;
        }

        .sidebar-sticky {
            position: relative;
            height: 100vh;
            padding: 0;
            overflow-x: hidden;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .sidebar-sticky::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-sticky::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-sticky::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-header h4 {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .sidebar-header .fas {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-right: 0.5rem;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 0.875rem 1.25rem;
            margin: 0.125rem 0.75rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.875rem;
            position: relative;
            text-decoration: none;
            overflow: hidden;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .sidebar .nav-link:hover::before {
            left: 100%;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: #fff;
            border-radius: 0 2px 2px 0;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar-heading {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1rem 1.25rem 0.5rem;
            margin-top: 1rem;
        }

        .collapse .nav-link {
            padding-left: 3rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .collapse .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Topbar Styles */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            padding-left: var(--sidebar-width);
            right: 0;
            height: 85px;
            background: linear-gradient(135deg, #ffffff 0%, #fafbff 50%, #f0f4ff 100%);
            border-bottom: 1px solid rgba(37, 99, 235, 0.1);
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        .topbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--accent-color) 30%, #06b6d4 60%, var(--primary-color) 100%);
            background-size: 200% 100%;
            animation: gradientShift 3s ease-in-out infinite;
        }

        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .topbar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.05), transparent);
            animation: topbarShimmer 4s infinite;
        }

        @keyframes topbarShimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        .topbar .container-fluid {
            height: 100%;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .topbar-left {
            display: flex;
            align-items: center;
        }

        .topbar-left h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-left: 1rem;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-toggle {
            color: var(--gray-600);
            text-decoration: none;
            padding: 12px;
            border-radius: 12px;
            transition: var(--transition);
            background: rgba(37, 99, 235, 0.05);
            border: 1px solid rgba(37, 99, 235, 0.1);
        }

        .sidebar-toggle:hover {
            color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
            transform: scale(1.05);
        }

        /* Notification Dropdown */
        .notification-dropdown {
            width: 420px;
            max-height: 550px;
            border: 0;
            box-shadow: 0 25px 50px rgba(37, 99, 235, 0.15);
            border-radius: var(--border-radius-lg);
            border: 1px solid rgba(37, 99, 235, 0.1);
            overflow: hidden;
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
        }

        .notification-dropdown .dropdown-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            margin: 0;
            border-radius: 0;
            position: relative;
        }

        .notification-dropdown .dropdown-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.8) 50%, rgba(255, 255, 255, 0.3) 100%);
        }

        .notification-dropdown .dropdown-header button {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.2s ease;
        }

        .notification-dropdown .dropdown-header button:hover {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        /* Notification Button */
        .notification-btn {
            position: relative;
            padding: 12px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
            border: 2px solid rgba(37, 99, 235, 0.1);
            transition: all 0.3s ease;
            color: var(--primary-color) !important;
        }

        .notification-btn:hover {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            border-color: rgba(37, 99, 235, 0.3);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.2);
        }

        .notification-btn i {
            font-size: 1.25rem;
        }

        .notification-count-badge {
            font-size: 0.7rem;
            min-width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
            animation: notificationPulse 2s infinite;
            font-weight: 600;
        }

        @keyframes notificationPulse {
            0% {
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4), 0 0 0 0 rgba(239, 68, 68, 0.7);
                transform: scale(1);
            }

            50% {
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4), 0 0 0 8px rgba(239, 68, 68, 0);
                transform: scale(1.05);
            }

            100% {
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4), 0 0 0 0 rgba(239, 68, 68, 0);
                transform: scale(1);
            }
        }

        .notification-list {
            max-height: 320px;
            overflow-y: auto;
        }

        .notification-list::-webkit-scrollbar {
            width: 4px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 2px;
        }

        .notification-item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-100);
            transition: all 0.2s ease;
        }

        .notification-item:hover {
            background-color: var(--gray-50);
            transform: translateX(2px);
        }

        .notification-item.unread {
            background: linear-gradient(90deg, #dbeafe 0%, #eff6ff 100%);
            border-left: 4px solid var(--primary-color);
        }

        .notification-meta {
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        .dropdown-footer {
            padding: 12px 20px;
            background-color: var(--gray-50);
            border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
        }

        .avatar-sm {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            font-size: 20px;
            border: 3px solid rgba(37, 99, 235, 0.2);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .avatar-sm::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .avatar-sm:hover::before {
            left: 100%;
        }

        .avatar-sm:hover {
            transform: scale(1.1);
            border-color: var(--primary-color);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        /* User Dropdown */
        .user-dropdown-btn {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(248, 250, 255, 0.8) 100%);
            border: 2px solid rgba(37, 99, 235, 0.1);
            border-radius: 50px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
        }

        .user-dropdown-btn:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(248, 250, 255, 1) 100%);
            border-color: rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.2);
        }

        .user-dropdown-menu {
            border: 0;
            box-shadow: 0 25px 50px rgba(37, 99, 235, 0.15);
            border-radius: var(--border-radius-lg);
            padding: 0.5rem 0;
            min-width: 200px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
        }

        .user-dropdown-menu .dropdown-item {
            padding: 0.75rem 1.25rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .user-dropdown-menu .dropdown-item:hover {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
            transform: translateX(4px);
        }

        .user-dropdown-menu .dropdown-item.text-danger:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(220, 38, 38, 0.05) 100%);
            color: #dc2626 !important;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 85px;
            /* Account for topbar height */
            transition: var(--transition);
            min-height: calc(100vh - 85px);
            background: transparent;
        }

        .container-fluid {
            padding: 2rem;
        }

        .page-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.05), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }


        .page-header h1 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.75rem;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .page-header h1 i {
            margin-right: 0.75rem;
            color: var(--primary-color);
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--accent-color) 50%, var(--primary-color) 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            box-shadow: var(--box-shadow-lg);
            transform: translateY(-4px);
        }

        .card-header {
            background: rgba(37, 99, 235, 0.1);
            border-bottom: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 1rem 1.25rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: var(--transition);
            border: none;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1e40af 100%);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.4);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #146c43 100%);
            box-shadow: 0 2px 4px rgba(25, 135, 84, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #e6ac00 100%);
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--info-color) 0%, #0aa2c0 100%);
            box-shadow: 0 2px 4px rgba(13, 202, 240, 0.3);
        }

        .btn-outline-primary {
            position: relative;
            overflow: hidden;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .btn-outline-primary:hover::before {
            left: 100%;
        }

        .table {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .table th {
            background: rgba(37, 99, 235, 0.1);
            border-bottom: 2px solid rgba(37, 99, 235, 0.2);
            font-weight: 600;
            color: var(--dark-color);
            padding: 1rem;
        }

        .table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .alert {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            border-left-color: var(--success-color);
            color: #0f5132;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
            color: #991b1b;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border-left-color: var(--warning-color);
            color: #664d03;
        }

        .alert-info {
            background: rgba(13, 202, 240, 0.1);
            border-left-color: var(--info-color);
            color: #055160;
        }

        .form-control,
        .form-select {
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0.75rem;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
        }

        .user-info {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            margin-right: 0.75rem;
        }

        .stats-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #146c43 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #e6ac00 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, var(--info-color) 0%, #0aa2c0 100%);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: var(--transition);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                left: 0;
                height: 70px;
            }

            .topbar-left h2 {
                display: none !important;
            }

            .user-dropdown-btn .d-none {
                display: none !important;
            }

            .notification-dropdown {
                width: 320px;
            }

            .container-fluid {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .main-content {
                margin-top: 70px;
                min-height: calc(100vh - 70px);
            }
        }

        @media (max-width: 576px) {
            .topbar-right .d-none {
                display: none !important;
            }

            .notification-dropdown {
                width: 280px;
            }

            .user-dropdown-btn {
                padding: 6px 12px;
            }

            .avatar-sm {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in-left {
            animation: slideInLeft 0.5s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .card-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #dc2626 100%);
            color: white;
        }

        .card-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .card-gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, var(--accent-color) 100%);
            color: white;
        }

        .card-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #dc2626 100%);
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(220, 53, 69, 0.3);
        }

        .table {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .table thead th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            border: none;
            padding: 0.75rem;
            vertical-align: middle;
        }

        .table tbody tr {
            border-bottom: 1px solid #f1f3f4;
        }

        .badge {
            font-weight: 500;
            padding: 0.375rem 0.75rem;
        }

        .alert {
            border: none;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .user-info {
            color: rgba(255, 255, 255, 0.9);
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }

        .logout-btn {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.5rem 1rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logout-btn:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Modern Button Hover Effects */
        .btn-outline-primary {
            position: relative;
            overflow: hidden;
        }

        .btn-outline-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .btn-outline-primary:hover::before {
            left: 100%;
        }

        /* Smooth Loading States */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        }

        .loading-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid rgba(37, 99, 235, 0.2);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Enhanced Focus States */
        .btn:focus,
        .dropdown-toggle:focus {
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
            outline: none;
        }

        /* Improved Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        .banner-thumb {
    display: inline-block;
    position: relative;
    margin: 4px;
    vertical-align: top;
}
.banner-thumb img {
    display: block;
    max-width: 120px;
    border-radius: 6px;
}
.banner-thumb .remove-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    z-index: 2;
    padding: 2px 6px;
    font-size: 14px;
    line-height: 1;
    border-radius: 50%;
    background: #dc3545;
    color: #fff;
    border: none;
    cursor: pointer;
    opacity: 0.9;
    transition: opacity 0.2s;
}
.banner-thumb .remove-btn:hover {
    opacity: 1;
}
    </style>

    <!-- Custom CSS -->
    <link href="<?= SITE_URL ?>/assets/css/internal-messages.css" rel="stylesheet">

    <!-- Bootstrap JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Super Admin JavaScript -->
    <script src="<?= SITE_URL ?>/assets/js/superadmin.js" defer></script>
    <!-- Realtime Notifications -->
    <script src="<?= SITE_URL ?>/assets/js/realtime-notifications.js"></script>
</head>

<body class="super-admin-body"> <!-- Top Navigation Bar -->
    <nav class="topbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="topbar-left">
                    <button type="button" class="btn btn-link sidebar-toggle d-md-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="d-none d-md-block">
                        <i class="fas fa-crown me-2"></i>
                        Super Admin Dashboard
                    </h2>
                </div>

                <div class="topbar-right">
                    <div class="d-flex align-items-center">
                        <!-- Quick Actions -->
                        <div class="d-none d-lg-flex me-3">
                            <button class="btn btn-outline-primary btn-sm me-2" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-1"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
