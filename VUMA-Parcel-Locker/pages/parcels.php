<?php
/**
 * Parcels Management Page
 * Displays parcel information with filtering and search
 */

require_once '../api/auth/session-check.php';

$isAdmin = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcels - VUMA Parcel Lockers</title>
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
                        <a class="nav-link" href="<?php echo $isAdmin ? 'dashboard-admin.php' : 'dashboard-user.php'; ?>">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="parcels.php">
                            <i class="bi bi-box me-1"></i>Parcels
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

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="bi bi-box me-2"></i>Parcel Management
                </h2>
                <p class="text-muted mb-0">
                    <?php echo $isAdmin ? 'Manage all parcels in the system' : 'Track and manage your parcels'; ?>
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Parcels List
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tracking #</th>
                                <th>Recipient</th>
                                <th>Status</th>
                                <th>Locker</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="parcelsTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading parcels...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading parcel information...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadParcels();
        });

        async function loadParcels() {
            try {
                const response = await fetch('../api/parcels/list.php');
                const result = await response.json();

                if (result.success) {
                    displayParcels(result.parcels);
                } else {
                    console.error('Load parcels error:', result.error);
                }
            } catch (error) {
                console.error('Load parcels error:', error);
            }
        }

        function displayParcels(parcels) {
            const tbody = document.getElementById('parcelsTableBody');

            if (parcels.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No parcels found</h5>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = parcels.map(parcel => `
                <tr>
                    <td>
                        <strong>${parcel.tracking_number}</strong>
                    </td>
                    <td>
                        <div>${parcel.recipient_name}</div>
                        <small class="text-muted">${parcel.recipient_email}</small>
                    </td>
                    <td>${parcel.status_badge}</td>
                    <td>${parcel.locker_info}</td>
                    <td>
                        <div>${parcel.created_date}</div>
                        <small class="text-muted">${parcel.created_time}</small>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="viewParcelDetails('${parcel.id}')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function viewParcelDetails(parcelId) {
            alert('Parcel details feature coming soon!');
        }
    </script>
</body>
</html>