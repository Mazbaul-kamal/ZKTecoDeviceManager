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

        th, td {
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
            0% { background-color: rgba(255, 255, 255, 0.3); }
            100% { background-color: transparent; }
        }

        footer {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            margin-top: 4rem;
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