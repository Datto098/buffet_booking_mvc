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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #dc3545;
            --primary-dark: #b02a37;
            --secondary-color: #6c757d;
            --accent-color: #fd7e14;
            --success-color: #198754;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --border-radius: 12px;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --box-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.6;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            padding: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #dc3545 0%, #b02a37 50%, #8b1538 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
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
            color: #fff;        background: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: var(--transition);
            min-height: 100vh;
            background: transparent;
        }

        .container-fluid {
            padding: 1.5rem;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--box-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
        }

        .card:hover {
            box-shadow: var(--box-shadow-lg);
            transform: translateY(-2px);
        }

        .card-header {
            background: rgba(var(--primary-color), 0.1);
            border-bottom: 1px solid rgba(220, 53, 69, 0.2);
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
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #8b1538 100%);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
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
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: #fff;
            transform: translateY(-1px);
        }

        .table {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .table th {
            background: rgba(var(--primary-color), 0.1);
            border-bottom: 2px solid rgba(220, 53, 69, 0.2);
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
            background: rgba(220, 53, 69, 0.1);
            border-left-color: var(--primary-color);
            color: #721c24;
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

        .form-control, .form-select {
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0.75rem;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
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

            .container-fluid {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in-left {
            animation: slideInLeft 0.5s ease-out;
        }        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
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
        }        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>

    <!-- Custom Super Admin JavaScript -->
    <script src="<?= SITE_URL ?>/assets/js/superadmin.js" defer></script>
</head>
<body class="super-admin-body">
