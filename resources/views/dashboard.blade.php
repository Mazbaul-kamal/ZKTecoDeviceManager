@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">
            <i class="fas fa-microchip"></i> ZKTeco Device Manager Dashboard
        </h1>

        <!-- Connection Status -->
        <div class="card d-flex align-items-center justify-content-between mb-4" style="flex-direction: row;">
            <h1 class="card-title d-flex align-items-center gap-2 mb-0">
                <div><i class="fas fa-plug"></i> Connection Status</div>
                <span class="badge {{ ($connection ?? 'Failed') === 'Success' ? 'badge-success' : 'badge-failed' }}">
                    <i
                        class="fas {{ ($connection ?? 'Failed') === 'Success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                    {{ $connection ?? 'Failed' }}
                </span>
            </h1>
            <div>
                <form method="POST" action="/device/restart" class="d-inline">
                    @csrf
                    <button class="btn btn-warning">Restart Device</button>
                </form>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-3 mb-4">
            <div class="stat-box p-0">
                <div class="sync-tree-card">
                    <div class="tree-header">
                        <i class="fas fa-info-circle"></i> Device Information
                    </div>
                    <div class="mgmt-options">
                        @foreach ($device_info ?? [] as $key => $value)
                            <div class="mgmt-row">
                                <span class="left-label"><i class="fas fa-tag"></i> {{ str_replace('_', ' ', $key) }}
                                    :</span>
                                <span class="value-placeholder">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="stat-box p-0">
                <div class="device-mgmt-block">
                    <h3><i class="fas fa-gear"></i> Device Management</h3>

                    <div class="mgmt-options">

                        <div class="mgmt-row">
                            <span class="left-label">
                                <i class="fas fa-rotate"></i> Sync Time
                            </span>
                            <a href="javascript:void(0)" class="value-placeholder"
                                onclick="deviceAction('sync-time')">Now</a>
                        </div>

                        <div class="mgmt-row">
                            <span class="left-label">
                                <i class="fas fa-lock"></i> Clear Admin Privilege
                            </span>
                            <a href="javascript:void(0)" class="value-placeholder"
                                onclick="deviceAction('clear-admin')">Now</a>
                        </div>

                        <div class="mgmt-row">
                            <span class="left-label">
                                <i class="fas fa-fingerprint"></i> Delete All Fingerprint
                            </span>
                            <a href="javascript:void(0)" class="value-placeholder"
                                onclick="deviceAction('delete-fingerprint')">Now</a>
                        </div>

                        <div class="mgmt-row">
                            <span class="left-label">
                                <i class="fas fa-clock-rotate-left"></i> Delete All Transaction
                            </span>
                            <a href="javascript:void(0)" class="value-placeholder"
                                onclick="deviceAction('delete-transaction')">Now</a>
                        </div>

                        <div class="mgmt-row">
                            <span class="left-label">
                                <i class="fas fa-user-xmark"></i> Delete All Employee
                            </span>
                            <a href="javascript:void(0)" class="value-placeholder"
                                onclick="deviceAction('delete-employee')">Now</a>
                        </div>

                        <div class="mgmt-row">
                            <span class="left-label">
                                <i class="fas fa-database"></i> Backup All Employee
                            </span>
                            <a href="javascript:void(0)" class="value-placeholder"
                                onclick="deviceAction('backup-employee')">Now</a>
                        </div>

                        <div class="mgmt-row">
                            <span class="left-label">
                                <i class="fas fa-database"></i> Delete All Data
                            </span>
                            <a href="javascript:void(0)" class="value-placeholder"
                                onclick="deviceAction('delete-all')">Now</a>
                        </div>

                        <div class="mgmt-row">
                            <span class="left-label">
                                <i class="fas fa-database"></i> Backup All Data
                            </span>
                            <a href="javascript:void(0)" class="value-placeholder"
                                onclick="deviceAction('backup-all')">Now</a>
                        </div>

                    </div>
                </div>

            </div>

            <div class="stat-box p-0">
                <div class="sync-tree-card">
                    <div class="tree-header">
                        <i class="fas fa-arrow-right-arrow-left"></i> Sync Data
                    </div>
                    <div class="tree-item">
                        <div class="area1-row">
                            <i class="fas fa-network-wired"></i></i> Device IP:
                            <span class="mac-addr"> {{ $device_info['Device IP'] ?? '-' }}</span>
                        </div>
                        <ul class="sync-options">
                            <li><i class="fas fa-users"></i> Sync Employee Information</li>
                            <li><i class="fas fa-fingerprint"></i> Sync Fingerprint Template</li>
                            <li><i class="fas fa-face-smile"></i> Sync Face Template</li>
                            <li><i class="fas fa-hand"></i> Sync Palm Template</li>
                            <li><i class="fas fa-hand-back-fist"></i> Sync Finger Vein Template</li>
                            <li><i class="fas fa-pen-to-square"></i> Overwrite Software Employee</li>
                            <li><i class="fas fa-camera"></i> Sync Employee Photo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


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
                                    @if (!empty($user['fingerprints']) && count($user['fingerprints']) > 0)
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
            function deviceAction(action) {

                Swal.fire({
                    title: 'Confirm Action',
                    text: 'This will execute a device-level operation.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Proceed'
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(`/zk/device/${action}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();

                            if (data.success) {
                                Swal.fire('Success', data.message, 'success');
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Server communication failed.', 'error');
                        });
                });
            }

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

            function enrollFingerprint(uid) {
                Swal.fire({
                    title: 'Enroll Fingerprint',
                    text: 'Please place your finger on the device scanner.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Start Enrollment'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Enrolling...',
                            text: 'Keep your finger on the scanner',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
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
                                    Swal.fire('Success!', 'Fingerprint enrolled successfully.', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error!', data.message || 'Enrollment failed', 'error');
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
