<?php
/**
 * Lockers Management Page
 * Displays locker status and provides management functions
 */

require_once '../api/auth/session-check.php';

$isAdmin = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lockers - VUMA Parcel Lockers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .locker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .locker-card {
            text-align: center;
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .locker-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .locker-card.available {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-color: #10b981;
        }

        .locker-card.occupied {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-color: #ef4444;
        }

        .locker-card.maintenance {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border-color: #f59e0b;
        }

        .locker-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .locker-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .locker-status {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .locker-location {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 0.5rem;
        }

        .stats-card {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .edit-mode {
            background: rgba(59, 130, 246, 0.1);
            border: 2px dashed #3b82f6;
        }

        @media (max-width: 768px) {
            .locker-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }

            .locker-icon {
                font-size: 2rem;
            }

            .locker-number {
                font-size: 1.2rem;
            }
        }
    </style>
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
                        <a class="nav-link" href="lockers.php">
                            <i class="bi bi-door-open me-1"></i>Lockers
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
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="bi bi-door-open me-2"></i>Locker Management
                </h2>
                <p class="text-muted mb-0">
                    <?php echo $isAdmin ? 'Manage all lockers in the system' : 'View locker availability and status'; ?>
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
                <?php if ($isAdmin): ?>
                <button type="button" class="btn btn-primary ms-2" id="editModeBtn" onclick="toggleEditMode()">
                    <i class="bi bi-pencil-square me-1"></i>Edit Mode
                </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isAdmin): ?>
        <!-- Admin Stats -->
        <div class="row mb-4" id="adminStats">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number" id="totalLockers">0</div>
                    <div>Total Lockers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number" id="availableLockers">0</div>
                    <div>Available</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number" id="occupiedLockers">0</div>
                    <div>Occupied</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number" id="maintenanceLockers">0</div>
                    <div>Maintenance</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Legend -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-2">
                        <small class="text-muted">
                            <strong>Legend:</strong>
                            <span class="badge bg-success me-2"><i class="bi bi-check-circle me-1"></i>Available</span>
                            <span class="badge bg-danger me-2"><i class="bi bi-x-circle me-1"></i>Occupied</span>
                            <span class="badge bg-warning"><i class="bi bi-tools me-1"></i>Maintenance</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lockers Grid -->
        <div class="card">
            <div class="card-body">
                <div id="lockersContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading lockers...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading locker information...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script>
        let lockersData = [];
        let editMode = false;
        const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            loadLockers();
        });

        async function loadLockers() {
            try {
                const response = await fetch('../api/lockers/list.php');
                const result = await response.json();

                if (result.success) {
                    lockersData = result.lockers;
                    displayLockers(result.lockers);

                    if (isAdmin && result.stats) {
                        updateAdminStats(result.stats);
                    }
                } else {
                    console.error('Load lockers error:', result.error);
                }
            } catch (error) {
                console.error('Load lockers error:', error);
            }
        }

        function displayLockers(lockers) {
            const container = document.getElementById('lockersContainer');

            if (lockers.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">No lockers found</h5>
                    </div>
                `;
                return;
            }

            const lockersHtml = lockers.map(locker => `
                <div class="locker-card ${locker.status}" data-locker-id="${locker.id}"
                     onclick="handleLockerClick(${locker.id}, '${locker.status}')">
                    <div class="locker-icon">
                        ${getLockerIcon(locker.status)}
                    </div>
                    <div class="locker-number">${locker.locker_number}</div>
                    <div class="locker-status">${locker.status_display || locker.status}</div>
                    <div class="locker-location">${locker.location}</div>
                    ${locker.parcel_count > 0 ? `<div class="mt-2"><small>${locker.parcel_count} parcel(s)</small></div>` : ''}

                    ${isAdmin ? `
                    <div class="admin-controls" style="position: absolute; top: 10px; right: 10px; opacity: 0; transition: opacity 0.3s ease;">
                        <button type="button" class="btn btn-sm btn-light" onclick="event.stopPropagation(); editLocker(${locker.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    ` : ''}
                </div>
            `).join('');

            container.innerHTML = `<div class="locker-grid">${lockersHtml}</div>`;
        }

        function getLockerIcon(status) {
            const icons = {
                'available': '<i class="bi bi-door-open"></i>',
                'occupied': '<i class="bi bi-door-closed"></i>',
                'maintenance': '<i class="bi bi-tools"></i>'
            };
            return icons[status] || '<i class="bi bi-door-open"></i>';
        }

        function updateAdminStats(stats) {
            document.getElementById('totalLockers').textContent = stats.total;
            document.getElementById('availableLockers').textContent = stats.available;
            document.getElementById('occupiedLockers').textContent = stats.occupied;
            document.getElementById('maintenanceLockers').textContent = stats.maintenance;
        }

        function handleLockerClick(lockerId, status) {
            if (!isAdmin || !editMode) {
                showLockerDetails(lockerId);
                return;
            }

            editLocker(lockerId);
        }

        function showLockerDetails(lockerId) {
            const locker = lockersData.find(l => l.id === lockerId);
            if (!locker) return;

            alert(`
                <strong>Locker ${locker.locker_number}</strong><br>
                Status: ${locker.status_display}<br>
                Location: ${locker.location}
                ${locker.parcel_count > 0 ? `<br><strong>Parcels:</strong> ${locker.parcel_count}` : ''}
            `);
        }

        function toggleEditMode() {
            editMode = !editMode;
            const btn = document.getElementById('editModeBtn');
            const cards = document.querySelectorAll('.locker-card');

            if (editMode) {
                btn.innerHTML = '<i class="bi bi-x-square me-1"></i>Exit Edit';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-danger');
                cards.forEach(card => card.classList.add('edit-mode'));
            } else {
                btn.innerHTML = '<i class="bi bi-pencil-square me-1"></i>Edit Mode';
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-primary');
                cards.forEach(card => card.classList.remove('edit-mode'));
            }
        }

        function editLocker(lockerId) {
            const locker = lockersData.find(l => l.id === lockerId);
            if (!locker) return;

            const newStatus = prompt(`Update status for ${locker.locker_number}:\\n\\nCurrent: ${locker.status_display}\\n\\nEnter new status (available/occupied/maintenance):`, locker.status);

            if (newStatus && ['available', 'occupied', 'maintenance'].includes(newStatus)) {
                updateLocker(lockerId, { status: newStatus });
            }
        }

        async function updateLocker(lockerId, updates) {
            try {
                const response = await fetch('../api/lockers/update.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        locker_id: lockerId,
                        ...updates
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Locker updated successfully!');
                    loadLockers(); // Reload data
                } else {
                    throw new Error(result.error || 'Failed to update locker');
                }

            } catch (error) {
                console.error('Update locker error:', error);
                alert('Failed to update locker: ' + error.message);
            }
        }
    </script>
</body>
</html>
