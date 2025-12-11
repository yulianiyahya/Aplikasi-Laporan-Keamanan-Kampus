<?php
require_once '../config.php';

if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    redirect('login.php');
}

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $nim_nip = clean_input($_POST['nim_nip']);
    $nama = clean_input($_POST['nama']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $role = clean_input($_POST['role']);
    $phone = clean_input($_POST['phone']);
    $department = clean_input($_POST['department']);
    
    // Check if NIM/NIP already exists
    $sql = "SELECT id FROM users WHERE nim_nip = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nim_nip]);
    
    if ($stmt->fetch()) {
        $_SESSION['error_message'] = "NIM/NIP sudah terdaftar!";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (nim_nip, nama, email, password, role, phone, department, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nim_nip, $nama, $email, $hashed_password, $role, $phone, $department]);
            
            $_SESSION['success_message'] = "User berhasil ditambahkan!";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Gagal menambahkan user: " . $e->getMessage();
        }
    }
    header('Location: manage_user.php');
    exit();
}

// Handle Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = (int)$_POST['user_id'];
    $nama = clean_input($_POST['nama']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $department = clean_input($_POST['department']);
    $status = clean_input($_POST['status']);
    
    try {
        $sql = "UPDATE users SET nama = ?, email = ?, phone = ?, department = ?, status = ?, updated_at = NOW() 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $email, $phone, $department, $status, $user_id]);
        
        $_SESSION['success_message'] = "User berhasil diupdate!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal update user: " . $e->getMessage();
    }
    header('Location: manage_user.php');
    exit();
}

// Handle Reset Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $user_id = (int)$_POST['user_id'];
    $new_password = $_POST['new_password'];
    
    try {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$hashed_password, $user_id]);
        
        $_SESSION['success_message'] = "Password berhasil direset!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal reset password: " . $e->getMessage();
    }
    header('Location: manage_user.php');
    exit();
}

<<<<<<< HEAD
// Handle Soft Delete User
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];

    // Tidak boleh hapus akun sendiri
=======
// Handle Delete User
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Don't allow deleting own account
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error_message'] = "Tidak dapat menghapus akun sendiri!";
    } else {
        try {
<<<<<<< HEAD
            // Soft delete → ubah status menjadi inactive + isi deleted_at
            $sql = "UPDATE users 
                    SET status = 'inactive', deleted_at = NOW() 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id]);

            $_SESSION['success_message'] = "User berhasil dinonaktifkan (soft delete)!";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Gagal melakukan soft delete: " . $e->getMessage();
        }
    }

=======
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id]);
            
            $_SESSION['success_message'] = "User berhasil dihapus!";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Gagal menghapus user: " . $e->getMessage();
        }
    }
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
    header('Location: manage_user.php');
    exit();
}

// Get filter
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

<<<<<<< HEAD
// Pagination setup
$records_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $records_per_page;

// Build query for counting total records
$count_sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
$params = [];

if ($role_filter !== 'all') {
    $count_sql .= " AND role = ?";
    $params[] = $role_filter;
=======
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = $page < 1 ? 1 : $page;
$offset = ($page - 1) * $limit;

// Count total records (untuk pagination)
$count_sql = "SELECT COUNT(*) FROM users WHERE 1=1";
$count_params = [];

if ($role_filter !== 'all') {
    $count_sql .= " AND role = ?";
    $count_params[] = $role_filter;
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
}

if ($status_filter !== 'all') {
    $count_sql .= " AND status = ?";
<<<<<<< HEAD
    $params[] = $status_filter;
=======
    $count_params[] = $status_filter;
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
}

if (!empty($search)) {
    $count_sql .= " AND (nim_nip LIKE ? OR nama LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
<<<<<<< HEAD
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

// Get total records
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetch()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Build query for fetching users with pagination
=======
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
}

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($count_params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Get users with pagination
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($role_filter !== 'all') {
    $sql .= " AND role = ?";
    $params[] = $role_filter;
}

if ($status_filter !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $sql .= " AND (nim_nip LIKE ? OR nama LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

<<<<<<< HEAD
$sql .= " ORDER BY created_at DESC LIMIT $records_per_page OFFSET $offset";
=======
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get statistics
$stats_sql = "SELECT 
              COUNT(*) as total,
              SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_count,
              SUM(CASE WHEN role = 'security' THEN 1 ELSE 0 END) as security_count,
              SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as user_count,
              SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
              FROM users";
$stmt = $pdo->query($stats_sql);
$stats = $stmt->fetch();

<<<<<<< HEAD
// Function to build pagination URL
function build_pagination_url($page_num) {
    $params = $_GET;
    $params['page'] = $page_num;
    return '?' . http_build_query($params);
}

=======
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
$page_title = "Manajemen Pengguna";
include_once '../includes/header.php';
include_once '../includes/navbar_admin.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-users me-2"></i>Manajemen Pengguna</h2>
                    <p class="text-muted mb-0">Kelola semua pengguna sistem</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-1"></i> Tambah User
                </button>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total User</h6>
                            <h2 class="mb-0"><?= $stats['total'] ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Admin</h6>
                            <h2 class="mb-0"><?= $stats['admin_count'] ?></h2>
                        </div>
                        <i class="fas fa-user-shield fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Petugas Keamanan</h6>
                            <h2 class="mb-0"><?= $stats['security_count'] ?></h2>
                        </div>
                        <i class="fas fa-user-tie fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">User Aktif</h6>
                            <h2 class="mb-0"><?= $stats['active_count'] ?></h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $role_filter === 'all' ? 'selected' : '' ?>>Semua Role</option>
                        <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="security" <?= $role_filter === 'security' ? 'selected' : '' ?>>Security</option>
                        <option value="user" <?= $role_filter === 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cari</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari NIM/NIP, nama, email..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        <?php if (!empty($search) || $role_filter !== 'all' || $status_filter !== 'all'): ?>
                            <a href="manage_user.php" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>NIM/NIP</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Departemen/Fakultas</th>
                            <th>Status</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-users-slash fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">Tidak ada user ditemukan</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($user['nim_nip']) ?></strong></td>
                                    <td><?= htmlspecialchars($user['nama']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php
                                        $role_badge = '';
                                        switch($user['role']) {
                                            case 'admin':
                                                $role_badge = 'danger';
                                                break;
                                            case 'security':
                                                $role_badge = 'warning';
                                                break;
                                            case 'user':
                                                $role_badge = 'info';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $role_badge ?>"><?= ucfirst($user['role']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($user['department']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= $user['status'] === 'active' ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td><small><?= date('d M Y H:i', strtotime($user['created_at'])) ?></small></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-warning" onclick='editUser(<?= json_encode($user) ?>)' title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-info" onclick="resetPassword(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nama']) ?>')" title="Reset Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="?delete=1&id=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
<<<<<<< HEAD
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Halaman <?= $page ?> dari <?= $total_pages ?> (Total: <?= $total_records ?> records)
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Button -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page > 1 ? build_pagination_url($page - 1) : '#' ?>" aria-label="Previous">
=======
</tbody>
                </table>
            </div>
            
            <!-- >>> TAMBAHKAN KODE PAGINATION DI SINI <<< -->
            <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-4 px-3 pb-3">
                    <div class="text-muted">
                        Menampilkan <?= min($offset + 1, $total_records) ?> - <?= min($offset + $limit, $total_records) ?> dari <?= $total_records ?> user
                    </div>
                    
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&role=<?= $role_filter ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php
<<<<<<< HEAD
                            // Calculate page range to show
                            $range = 2; // Number of pages to show on each side of current page
                            $start = max(1, $page - $range);
                            $end = min($total_pages, $page + $range);
                            
                            // Show first page and ellipsis if needed
                            if ($start > 1) {
                                echo '<li class="page-item"><a class="page-link" href="' . build_pagination_url(1) . '">1</a></li>';
                                if ($start > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            
                            // Show page numbers
                            for ($i = $start; $i <= $end; $i++) {
                                $active = $i == $page ? 'active' : '';
                                echo '<li class="page-item ' . $active . '">';
                                echo '<a class="page-link" href="' . build_pagination_url($i) . '">' . $i . '</a>';
                                echo '</li>';
                            }
                            
                            // Show ellipsis and last page if needed
                            if ($end < $total_pages) {
                                if ($end < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="' . build_pagination_url($total_pages) . '">' . $total_pages . '</a></li>';
                            }
                            ?>
                            
                            <!-- Next Button -->
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page < $total_pages ? build_pagination_url($page + 1) : '#' ?>" aria-label="Next">
=======
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1&role=<?= $role_filter ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">1</a>
                                </li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&role=<?= $role_filter ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $total_pages ?>&role=<?= $role_filter ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">
                                        <?= $total_pages ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&role=<?= $role_filter ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
<<<<<<< HEAD
=======
            <!-- >>> SAMPAI SINI <<< -->
            
>>>>>>> f466f6fc8eb3f3cc2ab1997d876b55e420963798
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="add_user" value="1">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah User Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIM/NIP *</label>
                            <input type="text" name="nim_nip" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role *</label>
                            <select name="role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="security">Security Officer</option>
                                <option value="user">User (Mahasiswa/Dosen)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" minlength="6" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departemen/Fakultas</label>
                            <input type="text" name="department" class="form-control" placeholder="Nama Departemen">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="edit_user" value="1">
                <input type="hidden" name="user_id" id="edit_user_id">
                
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIM/NIP</label>
                        <input type="text" id="edit_nim_nip" class="form-control" disabled>
                        <small class="text-muted">NIM/NIP tidak dapat diubah</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status *</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departemen</label>
                            <input type="text" name="department" id="edit_department" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="reset_password" value="1">
                <input type="hidden" name="user_id" id="reset_user_id">
                
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-key me-2"></i>Reset Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Reset password untuk: <strong id="reset_user_name"></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru *</label>
                        <input type="password" name="new_password" class="form-control" minlength="6" required>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-key me-1"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_nim_nip').value = user.nim_nip;
    document.getElementById('edit_nama').value = user.nama;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_status').value = user.status;
    document.getElementById('edit_phone').value = user.phone || '';
    document.getElementById('edit_department').value = user.department || '';
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function resetPassword(userId, userName) {
    document.getElementById('reset_user_id').value = userId;
    document.getElementById('reset_user_name').textContent = userName;
    
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}
</script>

<?php include_once '../includes/footer.php'; ?>