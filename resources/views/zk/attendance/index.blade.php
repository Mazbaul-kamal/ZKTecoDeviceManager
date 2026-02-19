<!-- resources/views/zk/attendance/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="zk-container">
        <!-- Header with title and action buttons -->
        <div class="page-header">
            <div class="title-section">
                <h1 class="page-title">
                    <i class="fas fa-clock"></i>
                    Attendance Logs
                </h1>
                <span class="record-badge">
                    <i class="fas fa-calendar-alt"></i>
                    {{ now()->format('F d, Y') }}
                </span>
            </div>
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="syncAttendance()">
                    <i class="fas fa-sync-alt"></i>
                    Get Transactions
                </button>
                <button class="btn btn-secondary" onclick="exportData()">
                    <i class="fas fa-download"></i>
                    Export
                </button>
                <button class="btn btn-secondary" onclick="printReport()">
                    <i class="fas fa-print"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Device Selection and Sync Status (like first screenshot) -->
        <div class="device-status-bar">
            <div class="device-info">
                <i class="fas fa-network-wired"></i>
                <span class="device-label">Active Device:</span>
                <span class="device-mac">00:17:61:11:9b:90</span>
                <span class="device-name">Main Entrance</span>
            </div>
            <div class="sync-status">
                <i class="fas fa-circle" style="color: #10b981;"></i>
                <span>Connected</span>
                <span class="separator-dot"></span>
                <span>Last Sync: {{ now()->format('H:i:s') }}</span>
            </div>
        </div>

        <!-- Filter Bar (like screenshot) -->
        <div class="filter-bar">
            <div class="filter-tabs">
                <button class="filter-tab active">All Logs</button>
                <button class="filter-tab">Check In</button>
                <button class="filter-tab">Check Out</button>
                <button class="filter-tab">Overtime</button>
                <button class="filter-tab">Manual</button>
            </div>
            <div class="date-range">
                <div class="date-input">
                    <i class="fas fa-calendar"></i>
                    <input type="date" id="startDate" value="{{ now()->format('Y-m-d') }}">
                </div>
                <span>to</span>
                <div class="date-input">
                    <i class="fas fa-calendar"></i>
                    <input type="date" id="endDate" value="{{ now()->format('Y-m-d') }}">
                </div>
                <button class="btn-filter">
                    <i class="fas fa-filter"></i>
                    Apply
                </button>
            </div>
        </div>

        <!-- Search Bar (matching screenshot) -->
        <div class="search-bar">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search by Name, ID, or Department..." id="attendanceSearch">
            </div>
            <div class="view-options">
                <button class="view-btn active"><i class="fas fa-table"></i></button>
                <button class="view-btn"><i class="fas fa-calendar-alt"></i></button>
                <button class="view-btn"><i class="fas fa-chart-bar"></i></button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-icon present">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="summary-details">
                    <span class="summary-label">Present Today</span>
                    <span class="summary-value">156</span>
                    <span class="summary-trend positive">
                        <i class="fas fa-arrow-up"></i> +12
                    </span>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon absent">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="summary-details">
                    <span class="summary-label">Absent</span>
                    <span class="summary-value">23</span>
                    <span class="summary-trend negative">
                        <i class="fas fa-arrow-down"></i> -5
                    </span>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon late">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="summary-details">
                    <span class="summary-label">Late Arrivals</span>
                    <span class="summary-value">12</span>
                    <span class="summary-trend negative">
                        <i class="fas fa-arrow-up"></i> +3
                    </span>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon overtime">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="summary-details">
                    <span class="summary-label">Overtime</span>
                    <span class="summary-value">8</span>
                    <span class="summary-trend positive">
                        <i class="fas fa-clock"></i> 24h
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Attendance Table (matching second screenshot style) -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-header-left">
                    <div class="card-title">
                        <i class="fas fa-history"></i> Attendance Logs
                    </div>
                </div>
                <div class="table-header-right">
                    <span class="record-info">
                        <i class="fas fa-database"></i>
                        Records: {{ $attendance_count }}
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>UID</th>
                            <th>User ID</th>
                            <th>Event</th>
                            <th>Status</th>
                            <th>Device</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($sample_attendance ?? [] as $log)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td>
                                <td>{{ $log['uid'] ?? '-' }}</td>
                                <td>{{ $log['id'] ?? '-' }}</td>
                                <td>{{ $log['name'] ?? '-' }}</td>
                                <td>
                                    @switch($log['state'] ?? 0)
                                        @case(1)
                                            <span class="badge bg-success">Check-in</span>
                                        @break

                                        @case(2)
                                            <span class="badge bg-danger">Check-out</span>
                                        @break

                                        @case(3)
                                            <span class="badge bg-warning">Break-out</span>
                                        @break

                                        @case(4)
                                            <span class="badge bg-info">Break-in</span>
                                        @break

                                        @case(5)
                                            <span class="badge bg-secondary">Overtime-in</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">Unknown</span>
                                    @endswitch
                                </td>
                                <td>{{ $log['timestamp'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>

    </div>

    @push('styles')
        <style>
            /* Modern ZK BioTime.Net Attendance Styling */
            :root {
                --primary: #1e3a5f;
                --primary-light: #2b4c7c;
                --primary-soft: #e8f0fe;
                --secondary: #5f6b7a;
                --success: #10b981;
                --success-light: #d1fae5;
                --danger: #ef4444;
                --danger-light: #fee2e2;
                --warning: #f59e0b;
                --warning-light: #fef3c7;
                --info: #3b82f6;
                --info-light: #dbeafe;
                --bg-light: #f3f4f6;
                --border-light: #e2e8f0;
                --text-dark: #1e293b;
                --text-soft: #64748b;
            }

            .zk-container {
                max-width: 1600px;
                margin: 2rem auto;
                padding: 0 1.5rem;
            }

            /* Page Header */
            .page-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .title-section {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .page-title {
                font-size: 1.875rem;
                font-weight: 700;
                color: var(--primary);
                margin: 0;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .page-title i {
                color: var(--primary-light);
                font-size: 2rem;
            }

            .record-badge {
                background: var(--primary-soft);
                color: var(--primary);
                padding: 0.5rem 1rem;
                border-radius: 100px;
                font-size: 0.875rem;
                font-weight: 600;
                border: 1px solid var(--border-light);
            }

            /* Buttons */
            .btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.625rem 1.25rem;
                border-radius: 100px;
                font-weight: 600;
                font-size: 0.875rem;
                transition: all 0.2s ease;
                cursor: pointer;
                border: 1px solid transparent;
                background: white;
            }

            .btn-primary {
                background: var(--primary);
                color: white;
                border-color: var(--primary);
            }

            .btn-primary:hover {
                background: var(--primary-light);
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .btn-secondary {
                background: white;
                color: var(--text-dark);
                border-color: var(--border-light);
            }

            .btn-secondary:hover {
                background: var(--bg-light);
                border-color: var(--text-soft);
            }

            .btn-clear {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.375rem 1rem;
                border-radius: 100px;
                font-weight: 500;
                font-size: 0.813rem;
                background: white;
                border: 1px solid var(--border-light);
                color: var(--text-soft);
                cursor: pointer;
            }

            .btn-clear:hover {
                background: var(--danger-light);
                color: var(--danger);
                border-color: var(--danger);
            }

            /* Device Status Bar */
            .device-status-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: white;
                padding: 0.75rem 1.5rem;
                border-radius: 100px;
                border: 1px solid var(--border-light);
                margin-bottom: 1.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            }

            .device-info {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .device-mac {
                font-family: 'Courier New', monospace;
                background: var(--primary-soft);
                padding: 0.25rem 0.75rem;
                border-radius: 100px;
                font-weight: 600;
                color: var(--primary);
                border: 1px solid var(--border-light);
            }

            .device-name {
                font-weight: 500;
                color: var(--text-dark);
            }

            .sync-status {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: var(--text-soft);
            }

            .separator-dot {
                width: 4px;
                height: 4px;
                background: var(--text-soft);
                border-radius: 50%;
            }

            /* Filter Bar */
            .filter-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .filter-tabs {
                display: flex;
                gap: 0.5rem;
                background: white;
                padding: 0.25rem;
                border-radius: 100px;
                border: 1px solid var(--border-light);
            }

            .filter-tab {
                padding: 0.5rem 1.25rem;
                border-radius: 100px;
                border: none;
                background: transparent;
                font-weight: 500;
                color: var(--text-soft);
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .filter-tab.active {
                background: var(--primary);
                color: white;
            }

            .date-range {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                background: white;
                padding: 0.25rem 0.5rem;
                border-radius: 100px;
                border: 1px solid var(--border-light);
            }

            .date-input {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.375rem 0.75rem;
                background: var(--bg-light);
                border-radius: 100px;
            }

            .date-input input {
                border: none;
                background: transparent;
                outline: none;
                font-size: 0.875rem;
                color: var(--text-dark);
            }

            .btn-filter {
                padding: 0.5rem 1rem;
                border-radius: 100px;
                border: none;
                background: var(--primary);
                color: white;
                font-weight: 500;
                cursor: pointer;
            }

            /* Search Bar */
            .search-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
            }

            .search-wrapper {
                display: flex;
                align-items: center;
                gap: 1rem;
                background: white;
                padding: 0.5rem 1.5rem;
                border-radius: 100px;
                border: 1px solid var(--border-light);
                flex: 1;
                max-width: 400px;
            }

            .search-wrapper input {
                border: none;
                outline: none;
                width: 100%;
                font-size: 0.938rem;
            }

            .view-options {
                display: flex;
                gap: 0.25rem;
                background: white;
                padding: 0.25rem;
                border-radius: 100px;
                border: 1px solid var(--border-light);
            }

            .view-btn {
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: none;
                background: transparent;
                border-radius: 50%;
                color: var(--text-soft);
                cursor: pointer;
            }

            .view-btn.active {
                background: var(--primary);
                color: white;
            }

            /* Summary Cards */
            .summary-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            .summary-card {
                background: white;
                border-radius: 20px;
                padding: 1.5rem;
                display: flex;
                align-items: center;
                gap: 1rem;
                border: 1px solid var(--border-light);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .summary-icon {
                width: 48px;
                height: 48px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
            }

            .summary-icon.present {
                background: var(--success-light);
                color: var(--success);
            }

            .summary-icon.absent {
                background: var(--danger-light);
                color: var(--danger);
            }

            .summary-icon.late {
                background: var(--warning-light);
                color: var(--warning);
            }

            .summary-icon.overtime {
                background: var(--info-light);
                color: var(--info);
            }

            .summary-details {
                flex: 1;
            }

            .summary-label {
                display: block;
                font-size: 0.813rem;
                color: var(--text-soft);
                margin-bottom: 0.25rem;
            }

            .summary-value {
                display: block;
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--text-dark);
                line-height: 1.2;
            }

            .summary-trend {
                font-size: 0.75rem;
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                padding: 0.125rem 0.5rem;
                border-radius: 100px;
                margin-top: 0.25rem;
            }

            .summary-trend.positive {
                background: var(--success-light);
                color: var(--success);
            }

            .summary-trend.negative {
                background: var(--danger-light);
                color: var(--danger);
            }

            /* Attendance Table */
            .attendance-table {
                width: 100%;
                border-collapse: collapse;
            }

            .attendance-table thead tr {
                background: #f8fafc;
                border-bottom: 2px solid var(--border-light);
            }

            .attendance-table th {
                text-align: left;
                padding: 1rem 1rem;
                font-weight: 600;
                font-size: 0.813rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-soft);
            }

            .attendance-table td {
                padding: 1rem 1rem;
                border-bottom: 1px solid var(--border-light);
                color: var(--text-dark);
            }

            .attendance-table tbody tr:hover {
                background: var(--primary-soft);
            }

            .time-col {
                font-family: 'Courier New', monospace;
                font-weight: 600;
                color: var(--primary);
            }

            .employee-info {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .employee-avatar {
                width: 32px;
                height: 32px;
                background: var(--primary);
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 0.813rem;
                text-transform: uppercase;
            }

            .id-badge {
                background: var(--bg-light);
                padding: 0.25rem 0.75rem;
                border-radius: 100px;
                font-size: 0.813rem;
                font-family: 'Courier New', monospace;
                font-weight: 600;
                color: var(--text-soft);
                border: 1px solid var(--border-light);
            }

            .event-badge {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                border-radius: 100px;
                font-size: 0.813rem;
                font-weight: 600;
            }

            .event-in {
                background: var(--success-light);
                color: var(--success);
            }

            .event-get {
                background: var(--info-light);
                color: var(--info);
            }

            .event-begin {
                background: var(--warning-light);
                color: var(--warning);
            }

            .event-upload {
                background: var(--primary-soft);
                color: var(--primary);
            }

            .status-badge {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                border-radius: 100px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .status-ontime {
                background: var(--success-light);
                color: var(--success);
            }

            .status-late {
                background: var(--warning-light);
                color: var(--warning);
            }

            .status-overtime {
                background: var(--info-light);
                color: var(--info);
            }

            .status-manual {
                background: var(--primary-soft);
                color: var(--primary);
            }

            /* Sync Panel */
            .sync-panel {
                margin-top: 2rem;
                background: white;
                border-radius: 20px;
                border: 1px solid var(--border-light);
                overflow: hidden;
            }

            .sync-panel-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 1rem 1.5rem;
                background: #f8fafc;
                border-bottom: 1px solid var(--border-light);
            }

            .sync-title {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-weight: 600;
                color: var(--primary);
            }

            .sync-controls {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .sync-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
            }

            .sync-dot.live {
                background: var(--success);
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }

                100% {
                    opacity: 1;
                }
            }

            .sync-content {
                display: grid;
                grid-template-columns: 300px 1fr;
                gap: 1rem;
                padding: 1.5rem;
            }

            .sync-node {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem;
                background: var(--primary-soft);
                border-radius: 100px;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: var(--primary);
            }

            .sync-children {
                margin-left: 2rem;
            }

            .sync-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.5rem 1rem;
                border-radius: 100px;
                margin-bottom: 0.25rem;
                color: var(--text-soft);
            }

            .sync-item.active {
                background: var(--primary-soft);
                color: var(--primary);
                font-weight: 500;
            }

            .sync-log {
                background: #f8fafc;
                border-radius: 16px;
                padding: 1rem;
            }

            .sync-log-header {
                display: grid;
                grid-template-columns: 100px 1fr;
                padding: 0.5rem 1rem;
                font-weight: 600;
                color: var(--text-soft);
                border-bottom: 1px solid var(--border-light);
            }

            .sync-log-entries {
                max-height: 200px;
                overflow-y: auto;
            }

            .log-entry {
                display: grid;
                grid-template-columns: 100px 1fr;
                padding: 0.5rem 1rem;
                border-bottom: 1px solid var(--border-light);
            }

            .log-entry .event {
                background: var(--primary-soft);
                padding: 0.125rem 0.75rem;
                border-radius: 100px;
                width: fit-content;
                font-size: 0.813rem;
                font-weight: 500;
                color: var(--primary);
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .summary-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .filter-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .date-range {
                    flex-wrap: wrap;
                }

                .sync-content {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Live search functionality
            document.getElementById('attendanceSearch').addEventListener('keyup', function() {
                let searchTerm = this.value.toLowerCase();
                let rows = document.querySelectorAll('.attendance-table tbody tr');

                rows.forEach(row => {
                    let text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Select all checkbox
            document.getElementById('selectAll').addEventListener('change', function() {
                let checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });

            // Sync attendance function
            function syncAttendance() {
                alert('Syncing attendance data from device...');
            }

            // Export data
            function exportData() {
                alert('Exporting attendance data...');
            }

            // Print report
            function printReport() {
                window.print();
            }

            // Clear filters
            function clearFilters() {
                document.getElementById('attendanceSearch').value = '';
                document.querySelectorAll('.attendance-table tbody tr').forEach(row => {
                    row.style.display = '';
                });
            }

            // View details
            function viewDetails(employeeId) {
                alert('Viewing details for employee: ' + employeeId);
            }

            // Edit log
            function editLog(employeeId) {
                alert('Editing log for employee: ' + employeeId);
            }

            // Filter tabs
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        </script>
    @endpush
@endsection
