<!-- resources/views/zk/users/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="zk-container">
    <!-- Header with title and action buttons -->
    <div class="page-header">
        <div class="title-section">
            <h1 class="page-title">
                <i class="fas fa-users-cog"></i>
                Device Users
            </h1>
            <span class="record-badge">Total: {{ count($users) }} users</span>
        </div>
        <div class="action-buttons">
            <a href="{{ route('zk.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                Add New User
            </a>
            <button class="btn btn-secondary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Search and filter bar (like in screenshot) -->
    <div class="search-filter-bar">
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Search by name, ID or UID..." class="search-input" id="userSearch">
        </div>
        <div class="filter-options">
            <select class="filter-select" id="roleFilter">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
                <option value="manager">Manager</option>
            </select>
            <span class="filter-hint">
                <i class="fas fa-filter"></i>
                Filter
            </span>
        </div>
    </div>

    <!-- Modern table card -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-header-left">
                <i class="fas fa-database"></i>
                <span>User Management</span>
            </div>
            <div class="table-header-right">
                <span class="record-info">Records: {{ count($users) }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th width="80">UID</th>
                        <th width="120">User ID</th>
                        <th>Name</th>
                        <th width="120">Role</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @forelse($users as $user)
                    <tr class="user-row" data-role="{{ $user['role'] }}" data-name="{{ strtolower($user['name']) }}" data-userid="{{ $user['userid'] }}" data-uid="{{ $user['uid'] }}">
                        <td><span class="uid-badge">{{ $user['uid'] }}</span></td>
                        <td><span class="user-id">{{ $user['userid'] }}</span></td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ substr($user['name'], 0, 1) }}
                                </div>
                                <span class="user-name">{{ $user['name'] }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge role-{{ $user['role'] }}">
                                <i class="fas fa-{{ $user['role'] == 'admin' ? 'crown' : 'user' }}"></i>
                                {{ ucfirst($user['role']) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-cell">
                                <a href="{{ route('zk.users.edit', $user['uid']) }}" class="btn-icon btn-edit" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                    <span class="btn-text">Edit</span>
                                </a>
                                
                                <form action="{{ route('zk.users.destroy', $user['uid']) }}" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-delete" title="Delete User">
                                        <i class="fas fa-trash-alt"></i>
                                        <span class="btn-text">Delete</span>
                                    </button>
                                </form>

                                <button class="btn-icon btn-more" title="More options">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            <div class="empty-state-content">
                                <i class="fas fa-user-slash"></i>
                                <h3>No Users Found</h3>
                                <p>Get started by adding your first device user.</p>
                                <a href="{{ route('zk.users.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i>
                                    Add User
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table footer with pagination and info (like screenshot) -->
        <div class="table-footer">
            <div class="footer-left">
                <span class="record-counter">
                    <i class="fas fa-list"></i>
                    Record: <span id="visibleCount">{{ count($users) }}</span>
                </span>
                <span class="login-user">
                    <i class="fas fa-user-circle"></i>
                    Login User: {{ Auth::user()->name ?? 'Admin' }}
                </span>
            </div>
            <div class="footer-right">
                <div class="pagination">
                    <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <span class="page-info">Page 1 of 1</span>
                    <button class="page-btn" disabled><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Modern ZK BioTime.Net System Styling */
    :root {
        --primary: #1e3a5f;
        --primary-light: #2b4c7c;
        --primary-soft: #e8f0fe;
        --secondary: #5f6b7a;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --bg-light: #f3f4f6;
        --border-light: #e2e8f0;
        --text-dark: #1e293b;
        --text-soft: #64748b;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.02);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
    }

    .zk-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
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
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
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

    .btn-sm {
        padding: 0.375rem 1rem;
        font-size: 0.813rem;
    }

    /* Search and Filter Bar */
    .search-filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 100px;
        border: 1px solid var(--border-light);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
        max-width: 400px;
    }

    .search-icon {
        color: var(--text-soft);
        font-size: 1rem;
    }

    .search-input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 0.938rem;
        color: var(--text-dark);
    }

    .search-input::placeholder {
        color: var(--text-soft);
    }

    .filter-options {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .filter-select {
        padding: 0.5rem 2rem 0.5rem 1rem;
        border-radius: 100px;
        border: 1px solid var(--border-light);
        background: white;
        color: var(--text-dark);
        font-size: 0.875rem;
        outline: none;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
    }

    .filter-hint {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-soft);
        font-size: 0.875rem;
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 20px;
        border: 1px solid var(--border-light);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--border-light);
    }

    .table-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        color: var(--primary);
    }

    .table-header-right .record-info {
        background: white;
        padding: 0.375rem 1rem;
        border-radius: 100px;
        font-size: 0.875rem;
        color: var(--text-soft);
        border: 1px solid var(--border-light);
    }

    /* Modern Table */
    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table thead tr {
        background: #f1f5f9;
        border-bottom: 2px solid var(--border-light);
    }

    .modern-table th {
        text-align: left;
        padding: 1rem 1.5rem;
        font-weight: 600;
        font-size: 0.813rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-soft);
    }

    .modern-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-light);
        color: var(--text-dark);
    }

    .modern-table tbody tr:hover {
        background: var(--primary-soft);
    }

    /* User Info */
    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.938rem;
        text-transform: uppercase;
    }

    .user-name {
        font-weight: 500;
    }

    .uid-badge {
        background: var(--primary-soft);
        padding: 0.25rem 0.75rem;
        border-radius: 100px;
        font-size: 0.813rem;
        font-weight: 600;
        color: var(--primary);
        border: 1px solid var(--border-light);
    }

    .user-id {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: var(--text-soft);
    }

    /* Role Badges */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 1rem;
        border-radius: 100px;
        font-size: 0.813rem;
        font-weight: 600;
        border: 1px solid transparent;
    }

    .role-admin {
        background: #fef3c7;
        color: #92400e;
        border-color: #f59e0b;
    }

    .role-user {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #38bdf8;
    }

    .role-manager {
        background: #dcfce7;
        color: #166534;
        border-color: #4ade80;
    }

    /* Action Cell */
    .action-cell {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 0.75rem;
        border-radius: 100px;
        font-size: 0.813rem;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit {
        color: var(--info);
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .btn-edit:hover {
        background: var(--info);
        color: white;
    }

    .btn-delete {
        color: var(--danger);
        background: #fee2e2;
        border-color: #fecaca;
    }

    .btn-delete:hover {
        background: var(--danger);
        color: white;
    }

    .btn-more {
        color: var(--text-soft);
        background: var(--bg-light);
        border-color: var(--border-light);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-content {
        max-width: 300px;
        margin: 0 auto;
    }

    .empty-state-content i {
        font-size: 3rem;
        color: var(--text-soft);
        margin-bottom: 1rem;
    }

    .empty-state-content h3 {
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .empty-state-content p {
        color: var(--text-soft);
        margin-bottom: 1.5rem;
    }

    /* Table Footer */
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-top: 1px solid var(--border-light);
    }

    .footer-left {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .record-counter {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        padding: 0.375rem 1rem;
        border-radius: 100px;
        border: 1px solid var(--border-light);
        font-weight: 500;
        color: var(--primary);
    }

    .login-user {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-soft);
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .page-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-light);
        background: white;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .page-btn:not(:disabled):hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-info {
        font-size: 0.875rem;
        color: var(--text-soft);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-filter-bar {
            flex-direction: column;
            gap: 1rem;
            border-radius: 20px;
        }

        .search-box {
            max-width: 100%;
        }

        .filter-options {
            width: 100%;
            justify-content: space-between;
        }

        .action-cell {
            flex-wrap: wrap;
        }

        .footer-left {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Live search functionality
    document.getElementById('userSearch').addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('#userTableBody .user-row');
        let visibleCount = 0;

        rows.forEach(row => {
            let name = row.dataset.name;
            let userid = row.dataset.userid.toLowerCase();
            let uid = row.dataset.uid;

            if (name.includes(searchTerm) || userid.includes(searchTerm) || uid.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('visibleCount').textContent = visibleCount;
    });

    // Role filter
    document.getElementById('roleFilter').addEventListener('change', function() {
        let role = this.value.toLowerCase();
        let rows = document.querySelectorAll('#userTableBody .user-row');
        let visibleCount = 0;

        rows.forEach(row => {
            if (!role || row.dataset.role === role) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('visibleCount').textContent = visibleCount;
    });
</script>
@endpush
@endsection