<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ZKTeco Device Manager') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.5;
        }

        .navbar {
            background-color: #1f2937;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-right: 1.5rem;
            transition: color 0.2s;
        }

        .navbar a:hover {
            color: #e5e7eb;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }

        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .grid {
            display: grid;
            gap: 20px;
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .info-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }

        .info-label {
            font-size: 13px;
            color: #666;
            text-transform: capitalize;
        }

        .info-value {
            font-weight: bold;
            margin-top: 5px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f1f1f1;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e5e5;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .stat-box {
            padding: 20px;
            border-radius: 12px;
            color: #fff;
        }

        .stat-blue {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .stat-purple {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        }

        .stat-green {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-sync {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-sync:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .btn-sync:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .time-highlight {
            animation: highlight 1s ease;
        }

        @keyframes highlight {
            0% {
                background-color: rgba(255, 255, 255, 0.3);
            }

            100% {
                background-color: transparent;
            }
        }

        footer {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            margin-top: 4rem;
        }


        /* ----- Sync Data tree (left) + log table (right) ----- */

        /* LEFT CARD: sync data tree */
        .sync-tree-card {
            background: #ffffffd9;
            border-radius: 24px;
            padding: 1.5rem 1.2rem;
            border: 1px solid #c2d2e6;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.02);
            backdrop-filter: blur(2px);
        }

        .tree-header {
            font-weight: 700;
            font-size: 1.1rem;
            color: #113355;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px dashed #a5bbd5;
            padding-bottom: 0.5rem;
        }

        .tree-item {
            margin-bottom: 1.5rem;
        }

        .area1-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 1rem;
            color: #163a5e;
            background: #eaf0fa;
            padding: 0.3rem 1rem;
            border-radius: 40px;
            border: 1px solid #b6cbeb;
            width: fit-content;
            margin-bottom: 1rem;
        }

        .area1-row i {
            color: #2f6491;
        }

        .mac-addr {
            font-family: 'Courier New', monospace;
            background: #d1def0;
            padding: 0.2rem 0.9rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #0b2d4b;
            border: 1px solid #9cb4d3;
            margin-left: 0.5rem;
        }

        .sync-options {
            list-style: none;
            margin-left: 1.2rem;
            border-left: 3px solid #9fb8d6;
            padding-left: 1.2rem;
        }

        .sync-options li {
            padding: 0.45rem 0.8rem;
            margin-bottom: 0.8rem;
            background: #f6faff;
            border-radius: 30px;
            border: 1px solid #cbdaf0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1d3e60;
        }

        .sync-options li i {
            color: #3f6b97;
            width: 1.4rem;
            font-size: 0.95rem;
        }

        /* left block – Device Management */
        .device-mgmt-block {
            background: #f8fafc;
            border-radius: 20px;
            padding: 1.2rem 1rem 1rem 1rem;
            border: 1px solid #d0d9e3;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.02);
        }

        .device-mgmt-block h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #11324b;
            margin-bottom: 1rem;
            letter-spacing: -0.2px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px dashed #acbacb;
            padding-bottom: 0.5rem;
        }

        .mgmt-options {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .mgmt-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 0.4rem 0.9rem 0.4rem 1rem;
            border-radius: 40px;
            border: 1px solid #ccd7e4;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1e2f3d;
        }

        .mgmt-row .left-label i {
            width: 1.4rem;
            color: #3f5e7a;
        }

        .mgmt-row .value-placeholder {
            background: #eef3f9;
            padding: 0.2rem 1rem;
            border-radius: 30px;
            font-size: 0.8rem;
            color: #163a5e;
            border: 1px solid #b7c7da;
            font-weight: 600;
        }

    </style>
</head>

<body>
    <!-- Navigation Bar - Simplified without auth -->
    <nav class="navbar">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div>
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a>
                <a href="{{ url('/dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="{{ route('zk.users.index') }}"><i class="fas fa-users"></i> Users</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} ZKTeco Device Manager. All rights reserved.</p>
    </footer>
</body>

</html>
