<?php
/**
 * User Dashboard
 * Customer portal for parcel management and tracking
 */

require_once '../api/auth/session-check.php';

if (isAdmin()) {
    header('Location: dashboard-admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VUMA Parcel Lockers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-box-seam me-2"></i>VUMA
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard-user.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="parcels.php">
                            <i class="bi bi-box me-1"></i>My Parcels
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>Profile
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>Settings
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
                    <h1 class="h2">My Dashboard</h1>
                    <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>

                <div class="alert alert-primary">
                    <h4 class="alert-heading">
                        <i class="bi bi-person me-2"></i>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!
                    </h4>
                    <p class="mb-0">Here's an overview of your parcels and recent activity.</p>
                </div>

                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Waiting for Pickup
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="waitingCount">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clock-history fa-2x text-gray-300" style="font-size: 2rem; color: #f59e0b;"></i>
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
                                            Delivered
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="deliveredCount">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-check-circle fa-2x text-gray-300" style="font-size: 2rem; color: #10b981;"></i>
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
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCount">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-box fa-2x text-gray-300" style="font-size: 2rem; color: #3b82f6;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-purple shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                            Active OTP Codes
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeOtpCount">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-key fa-2x text-gray-300" style="font-size: 2rem; color: #8b5cf6;"></i>
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
                            <table class="table table-bordered" id="recentParcelsTable">
                                <thead>
                                    <tr>
                                        <th>Tracking #</th>
                                        <th>Status</th>
                                        <th>Locker</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="recentParcelsBody">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            Loading recent parcels...
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
        let dashboardData = {};

        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });

        async function loadDashboardData() {
            try {
                const response = await fetch('../api/dashboard/user-data.php');
                const result = await response.json();

                if (result.success) {
                    dashboardData = result;
                    updateDashboard();
                } else {
                    console.error('Dashboard data error:', result.error);
                }
            } catch (error) {
                console.error('Load error:', error);
            }
        }

        function updateDashboard() {
            document.getElementById('waitingCount').textContent = dashboardData.stats.waiting || 0;
            document.getElementById('deliveredCount').textContent = dashboardData.stats.delivered || 0;
            document.getElementById('totalCount').textContent = dashboardData.stats.total || 0;

            const tbody = document.getElementById('recentParcelsBody');
            if (!dashboardData.parcels || dashboardData.parcels.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No parcels found</td></tr>';
                return;
            }

            tbody.innerHTML = dashboardData.parcels.map(parcel => `
                <tr>
                    <td><strong>${parcel.tracking_number}</strong><br><small class="text-muted">${parcel.created_date}</small></td>
                    <td>${parcel.status_badge}</td>
                    <td>${parcel.locker_info}</td>
                    <td><div>${parcel.created_date}</div><small class="text-muted">${parcel.created_time}</small></td>
                    <td>
                        <button class="btn btn-sm btn-outline-info" onclick="alert('Details: ${parcel.tracking_number}')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }
</script>
</body>
</html>