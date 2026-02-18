@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">
            <i class="fas fa-microchip"></i> ZKTeco Device Dashboard
        </h1>

        <!-- Connection Status -->
        <div class="card d-flex align-items-center justify-content-between mb-4" style="flex-direction: row;">
            <h1 class="card-title">
                <i class="fas fa-plug"></i> Connection Status
            </h1>
            <span class="badge {{ ($connection ?? 'Failed') === 'Success' ? 'badge-success' : 'badge-failed' }}">
                <i
                    class="fas {{ ($connection ?? 'Failed') === 'Success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                {{ $connection ?? 'Failed' }}
            </span>
                    <button class="btn btn-sm btn-outline-primary" onclick="restartDevice()">Restart Device</button>
        </div>

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
                <i class="fas fa-clock"></i> Device Time
                <div class="stat-number">
                    <span id="device-time">{{ $device_info['device_time'] ?? '-' }}</span>
                    <button id="sync-time-btn" class="btn-sync">
                        <i class="fas fa-sync-alt"></i> Sync Time
                    </button>
                </div>
            </div>
        </div>
        <!-- Hidden Form for Sync -->
        <form id="sync-form" method="POST" action="{{ route('device.sync.time') }}" style="display: none;">
            @csrf
            <input type="hidden" name="system_time" id="system-time-input" value="">
        </form>

        <!-- Users Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="card-title">
                    <i class="fas fa-users"></i> Users
                </div>
                <a class="btn btn-primary" href="{{ route('zk.users.create') }}">
                    <i class="fas fa-plus"></i> Create User
                </a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>UID</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Fingerprint</th>
                            <th>Face</th>
                            <th>Card Number</th>
                            <th width="200">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sample_users ?? [] as $user)
                            <tr>
                                <td>{{ $user['uid'] ?? '-' }}</td>
                                <td>{{ $user['userid'] ?? '-' }}</td>
                                <td>{{ $user['name'] ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ ($user['role'] ?? 0) == 14 ? 'bg-warning' : 'bg-info' }}">
                                        {{ ($user['role'] ?? 0) == 14 ? 'Admin' : 'User' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($user['fingerprint'] ?? false)
                                        <i class="fas fa-check-circle text-success"></i> Yes
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i> No
                                    @endif
                                </td>
                                <td>
                                    @if ($user['face'] ?? false)
                                        <i class="fas fa-check-circle text-success"></i> Yes
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i> No
                                    @endif
                                </td>
                                <td>{{ $user['cardno'] ?? '-' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-success btn-sm"
                                            onclick="enrollFingerprint({{ $user['uid'] }})">
                                            <i class="fas fa-fingerprint"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="enrollFace({{ $user['uid'] }})">
                                            <i class="fas fa-face-smile"></i>
                                        </button>
                                        <button class="btn btn-info btn-sm" onclick="manageRole({{ $user['uid'] }})">
                                            <i class="fas fa-user-shield"></i>
                                        </button>
                                        <a href="{{ route('zk.users.edit', $user['uid']) }}"
                                            class="btn btn-secondary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteUser({{ $user['uid'] }})" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Attendance Logs -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-history"></i> Attendance Logs
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>UID</th>
                            <th>User ID</th>
                            <th>State</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sample_attendance ?? [] as $log)
                            <tr>
                                <td>{{ $log['uid'] ?? '-' }}</td>
                                <td>{{ $log['id'] ?? '-' }}</td>
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
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No attendance logs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
    const syncBtn = document.getElementById('sync-time-btn');
    const deviceTimeEl = document.getElementById('device-time');
    
    if (!syncBtn) return;

    syncBtn.addEventListener('click', function() {
        // Disable button
        syncBtn.disabled = true;
        syncBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
        
        // Get current system time
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const systemTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        
        // Show loading
        Swal.fire({
            title: 'Syncing Device Time',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Send request
        fetch('{{ route("device.sync.time") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ system_time: systemTime })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            
            if (data.success) {
                // Update device time display
                if (data.device_time) {
                    deviceTimeEl.textContent = data.device_time;
                    
                    // Highlight effect
                    deviceTimeEl.style.transition = 'background-color 0.5s';
                    deviceTimeEl.style.backgroundColor = '#d4edda';
                    setTimeout(() => {
                        deviceTimeEl.style.backgroundColor = '';
                    }, 1000);
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Device time updated to: ' + data.device_time,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to sync device time'
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Could not connect to server'
            });
        })
        .finally(() => {
            syncBtn.disabled = false;
            syncBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync Time';
        });
    });
});

            // Delete User
            function deleteUser(uid) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This user will be permanently deleted from the device!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/zk/users/${uid}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        'User has been deleted.',
                                        'success'
                                    ).then(() => location.reload());
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        data.message || 'Failed to delete user',
                                        'error'
                                    );
                                }
                            });
                    }
                });
            }

            // Enroll Fingerprint
            function enrollFingerprint(uid) {
                Swal.fire({
                    title: 'Enroll Fingerprint',
                    text: 'Please place finger on the device scanner for UID: ' + uid,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Start Enrollment'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Enrolling...',
                            text: 'Please keep finger on scanner',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(`/zk/users/${uid}/enroll-fingerprint`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    finger_id: 1
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Success!',
                                        'Fingerprint enrolled successfully',
                                        'success'
                                    ).then(() => location.reload());
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        data.message || 'Enrollment failed',
                                        'error'
                                    );
                                }
                            });
                    }
                });
            }

            // Enroll Face
            function enrollFace(uid) {
                Swal.fire({
                    title: 'Enroll Face',
                    text: 'Please look at the device camera for UID: ' + uid,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Start Enrollment'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Enrolling...',
                            text: 'Please look at the camera',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(`/zk/users/${uid}/enroll-face`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Success!',
                                        'Face enrolled successfully',
                                        'success'
                                    ).then(() => location.reload());
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        data.message || 'Enrollment failed',
                                        'error'
                                    );
                                }
                            });
                    }
                });
            }

            // Manage Role
            function manageRole(uid) {
                Swal.fire({
                    title: 'Select Role',
                    input: 'select',
                    inputOptions: {
                        0: 'Normal User',
                        14: 'Admin'
                    },
                    inputPlaceholder: 'Select a role',
                    showCancelButton: true,
                    confirmButtonText: 'Update Role'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/zk/users/${uid}/role`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    role: result.value
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Success!',
                                        'Role updated successfully',
                                        'success'
                                    ).then(() => location.reload());
                                }
                            });
                    }
                });
            }
        </script>
    @endsection
