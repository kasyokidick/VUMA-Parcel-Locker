<?php
/**
 * Admin Dashboard
 * Administrative control panel with system overview
 */

require_once '../api/auth/session-check.php';

if (!isAdmin()) {
    header('Location: dashboard-user.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VUMA Parcel Lockers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-box-seam me-2"></i>VUMA Admin
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard-admin.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['name']); ?>
                            <span class="badge bg-danger ms-1">Admin</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>Profile
                            </a></li>
                            <li><a class="dropdown-item" href="index.php">
                                <i class="bi bi-house me-2"></i>Frontend
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-logout>
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <main class="col-12 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Admin Dashboard</h1>
                </div>

                <div class="alert alert-info">
                    <h4 class="alert-heading">
                        <i class="bi bi-shield-check me-2"></i>System Overview
                    </h4>
                    <div class="row">
                        <div class="col-md-3">
                            <i class="bi bi-database me-1"></i>Database:
                            <span class="badge bg-success">Connected</span>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-door-open me-1"></i>Lockers:
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-people me-1"></i>Users:
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-box me-1"></i>Parcels:
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Users
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalUsers">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people fa-2x" style="font-size: 2rem; color: #f59e0b;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Available Lockers
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="availableLockers">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-door-open fa-2x" style="font-size: 2rem; color: #10b981;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Parcels
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingParcels">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clock-history fa-2x" style="font-size: 2rem; color: #f59e0b;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Parcels
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalParcels">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-box fa-2x" style="font-size: 2rem; color: #3b82f6;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">
                            <i class="bi bi-clock-history me-2"></i>Recent Parcels
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tracking #</th>
                                        <th>Status</th>
                                        <th>Customer</th>
                                    </tr>
                                </thead>
                                <tbody id="recentParcelsBody">
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAdminDashboardData();
        });

        async function loadAdminDashboardData() {
            try {
                const response = await fetch('../api/dashboard/admin-data.php');
                const result = await response.json();

                if (result.success) {
                    updateAdminDashboard(result);
                } else {
                    console.error('Admin dashboard error:', result.error);
                }
            } catch (error) {
                console.error('Load error:', error);
            }
        }

        function updateAdminDashboard(data) {
            document.getElementById('totalUsers').textContent = data.stats.total_users;
            document.getElementById('availableLockers').textContent = data.stats.lockers?.available || 0;
            document.getElementById('pendingParcels').textContent = data.stats.parcels?.pending || 0;
            document.getElementById('totalParcels').textContent = data.stats.parcels?.total || 0;

            // Update recent parcels table
            const tbody = document.getElementById('recentParcelsBody');
            if (data.recent_parcels && data.recent_parcels.length > 0) {
                tbody.innerHTML = data.recent_parcels.map(parcel => `
                    <tr>
                        <td><strong>${parcel.tracking_number}</strong></td>
                        <td>${parcel.status_badge || parcel.status}</td>
                        <td>${parcel.recipient_name}</td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No recent parcels</td></tr>';
            }
        }
    </script>
</body>
</html>
