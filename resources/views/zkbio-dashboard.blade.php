@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">
        <i class="fas fa-server"></i> ZKBio Time 9.0 Dashboard
    </h1>

    <!-- Connection Status -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-plug"></i> Connection Status
        </div>
        <span class="badge {{ ($connection ?? 'Failed') === 'Success' ? 'badge-success' : 'badge-failed' }}">
            <i class="fas {{ ($connection ?? 'Failed') === 'Success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
            {{ $connection ?? 'Failed' }}
        </span>
        <button class="btn btn-sm btn-outline-primary" onclick="restartDevice()">Restart Device</button>
    </div>
    <script>
        function restartDevice() {
            if (confirm('Are you sure you want to restart the device?')) {
                fetch('{{ route("zkbio.device.restart") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Device restart initiated successfully.');
                    } else {
                        alert('Failed to initiate device restart: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error restarting device:', error);
                    alert('An error occurred while trying to restart the device.');
                });
            }
        }
    </script>
    
    <!-- Device Info -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-info-circle"></i> Device Information
        </div>
        <div class="grid grid-3">
            @foreach ($device_info ?? [] as $key => $value)
                <div class="info-box">
                    <div class="info-label">
                        <i class="fas fa-tag"></i> {{ str_replace('_', ' ', $key) }}
                    </div>
                    <div class="info-value">
                        {{ $value }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-3">
        <div class="stat-box stat-blue">
            <i class="fas fa-users"></i> Total Users
            <div class="stat-number">{{ $users_count ?? 0 }}</div>
        </div>

        <div class="stat-box stat-purple">
            <i class="fas fa-history"></i> Attendance Logs
            <div class="stat-number">{{ $attendance_count ?? 0 }}</div>
        </div>

        <div class="stat-box stat-green">
            <i class="fas fa-clock"></i> Server Time
            <div class="stat-number">
                <span id="server-time">{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>

    <!-- Attendance Logs -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-history"></i> Recent Attendance Logs
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Punch Time</th>
                        <th>Punch State</th>
                        <th>Verify Type</th>
                        <th>Terminal SN</th>
                        <th>Temperature</th>
                        <th>Mask</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sample_attendance ?? [] as $log)
                        <tr>
                            <td>{{ $log['emp_code'] ?? '-' }}</td>
                            <td>{{ $log['punch_time'] ?? '-' }}</td>
                            <td>
                                @switch($log['punch_state'] ?? '0')
                                    @case('0')
                                        <span class="badge bg-success">Check-in</span>
                                        @break
                                    @case('1')
                                        <span class="badge bg-danger">Check-out</span>
                                        @break
                                    @case('2')
                                        <span class="badge bg-warning">Break-out</span>
                                        @break
                                    @case('3')
                                        <span class="badge bg-info">Break-in</span>
                                        @break
                                    @case('4')
                                        <span class="badge bg-secondary">Overtime-in</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $log['punch_state'] }}</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($log['verify_type'] ?? 0)
                                    @case(1)
                                        Fingerprint
                                        @break
                                    @case(2)
                                        Face
                                        @break
                                    @case(3)
                                        Card
                                        @break
                                    @case(4)
                                        Password
                                        @break
                                    @default
                                        Unknown
                                @endswitch
                            </td>
                            <td>{{ $log['terminal_sn'] ?? '-' }}</td>
                            <td>{{ $log['temperature'] ?? 'N/A' }}</td>
                            <td>
                                @if(($log['is_mask'] ?? 0) == 1)
                                    <span class="badge bg-success">Yes</span>
                                @elseif(($log['is_mask'] ?? 0) == 0)
                                    <span class="badge bg-danger">No</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No attendance logs found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- API Navigation -->
    <div class="card">
        <div class="card-title">
            <i class="fas fa-link"></i> API Endpoints
        </div>
        <div class="grid grid-3">
            <a href="{{ route('zkbio.employees.index') }}" class="btn btn-primary">
                <i class="fas fa-users"></i> Manage Employees
            </a>
            <a href="{{ route('zkbio.devices.index') }}" class="btn btn-info">
                <i class="fas fa-server"></i> View Devices
            </a>
            <a href="{{ route('zkbio.transactions.index') }}" class="btn btn-success">
                <i class="fas fa-history"></i> View Transactions
            </a>
            <a href="{{ route('zkbio.departments.index') }}" class="btn btn-warning">
                <i class="fas fa-sitemap"></i> Departments
            </a>
            <a href="{{ route('zkbio.areas.index') }}" class="btn btn-secondary">
                <i class="fas fa-map-marker-alt"></i> Areas
            </a>
            <a href="{{ route('zkbio.positions.index') }}" class="btn btn-dark">
                <i class="fas fa-briefcase"></i> Positions
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Update server time every second
    const serverTimeEl = document.getElementById('server-time');
    
    function updateServerTime() {
        fetch('{{ route("zkbio.system.time") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && serverTimeEl) {
                    serverTimeEl.textContent = data.system_time;
                }
            })
            .catch(error => console.error('Error updating time:', error));
    }
    
    // Update every second
    setInterval(updateServerTime, 1000);
});
</script>
@endsection