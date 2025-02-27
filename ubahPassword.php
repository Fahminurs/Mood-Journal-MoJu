<?php
session_start();  // Mulai sesi
require_once 'koneksi.php';  // Koneksi ke database
require_once __DIR__ . '/Auth/auth.php'; 
AuthHelper::requireRole(['user']);    

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validasi input
    $errors = [];
    if (empty($currentPassword)) $errors[] = "Password saat ini harus diisi.";
    if (empty($newPassword)) $errors[] = "Password baru harus diisi.";
    if ($newPassword !== $confirmPassword) $errors[] = "Konfirmasi password tidak sesuai.";

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
        exit;
    }

    try {
        // Ambil id_user dari sesi
        $userId = $_SESSION['user_id'];  // ID user yang login
        $db = getConnection();
        
        // Cek password lama dengan query yang benar menggunakan id_user
        $stmt = $db->prepare("SELECT password FROM user WHERE id_user = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => "User tidak ditemukan."]);
            exit;
        }

        // Verifikasi apakah password saat ini yang dimasukkan cocok
        if (!password_verify($currentPassword, $user['password'])) {
            // Jika password tidak cocok
            echo json_encode(['status' => 'error', 'message' => "Password saat ini salah."]);
            exit;
        }

        // Hash password baru dan update di database
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE user SET password = ? WHERE id_user = ?");
        $stmt->execute([$hashedPassword, $userId]);

        // Jika berhasil mengubah password
        echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah.']);
        exit;

    } catch (PDOException $e) {
        // Tangani error jika ada masalah dengan database
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/css/ubahPassword.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="logo">MOJU</div>
        <div class="nav-divider"></div>
        <a href="home.php" style="text-decoration: none;">
        <div class="nav-item"><i class="fas fa-home"></i></div>
        </a>
        <a href="forumdiskusi.php" style="text-decoration: none;">
        <div class="nav-item"><i class="fa-solid fa-comment"></i></div>
        </a>
        <div class="nav-item active"><i class="fas fa-cog"></i></div>
        <div class="nav-divider"></div>
    </nav>

    <!-- Content -->
    <h1 class="page-title">Ubah Password</h1>

    <div class="settings-container">
    <form id="changePasswordForm" method="post" action="ubahPassword.php">
            <div class="form-group password-group">
                <label>Password Saat Ini</label>
                <span class="colon">:</span>
                <div class="password-input">
                    <input type="password" name="currentPassword" id="currentPassword" required>
                    <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('currentPassword')"></i>
                </div>
            </div>

            <div class="form-group password-group">
                <label>Password Baru</label>
                <span class="colon">:</span>
                <div class="password-input">
                    <input type="password" name="newPassword" id="newPassword" required>
                    <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('newPassword')"></i>
                </div>
            </div>

            <div class="form-group password-group">
                <label>Konfirmasi Password</label>
                <span class="colon">:</span>
                <div class="password-input">
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('confirmPassword')"></i>
                </div>
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-cancel" id="btnCancel">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
    <div style="margin-bottom: 100px;"></div>
    <div class="Navigation-moju">
        <ul>
            <li class="list-moju ">
                <a href="home.php">
                    <span class="icon-moju "><i class="fas fa-home"></i></span>
                    <span class="text">Home</span>
                </a>
            </li>
           <li class="list-moju ">
                <a href="forumdiskusi.php">
                    <span class="icon-moju"><i class="fa-solid fa-comment"></i></span>
                    <span class="text">Diskusi</span>
                </a>
            </li>
           <li class="list-moju active">
                <a href="settings.php">
                    <span class="icon-moju"><i class="fas fa-cog"></i></span>
                    <span class="text">Settings</span>
                </a>
            </li>

            <div class="indikator-moju"></div>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Password tidak cocok!',
                    icon: 'error',
                    customClass: { container: 'my-swal' }
                });
                return;
            }

            const formData = new FormData(this);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
    Swal.fire({
        title: 'Berhasil!',
        text: 'Password berhasil diubah.',
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Home',
        cancelButtonText: 'Settings',
        customClass: { container: 'my-swal' }
    }).then((result) => {
        if (result.isConfirmed) {
            // Navigasi ke halaman Home jika tombol "Home" ditekan
            window.location.href = 'home.php';  // Sesuaikan dengan URL halaman home Anda
        } else if (result.isDismissed) {
            // Navigasi ke halaman Settings jika tombol "Settings" ditekan
            window.location.href = 'settings.php';  // Sesuaikan dengan URL halaman settings Anda
        }
    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        html: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: { container: 'my-swal' }
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: { container: 'my-swal' }
                });
            });
        });

        // Handle Cancel button with SweetAlert2
        document.getElementById('btnCancel').addEventListener('click', function() {
            Swal.fire({
                title: 'Batalkan Perubahan?',
                text: "Semua perubahan akan dihapus.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Lanjutkan'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'settings.php';
                }
            });
        });
    </script>
 
         <!-- Navigation -->
         <script>  
            const list = document.querySelectorAll('.list-moju');  
            const indikator = document.querySelector('.indikator-moju');  
            
            function updateIndicatorPosition(activeItem) {  
                if (activeItem) {  
                    const ul = activeItem.parentElement;  
                    const ulLeftOffset = ul.offsetLeft;  
                    const liWidth = activeItem.offsetWidth;  
                    const liLeftOffset = activeItem.offsetLeft;  
                    const correctOffset = liLeftOffset - ulLeftOffset;  
            
                    indikator.style.width = `${liWidth}px`;  
                    indikator.style.transform = `translateX(${correctOffset}px)`;  
                }  
            }  
            
            function activeLink() {  
                list.forEach((item) => {  
                    item.classList.remove('active');  
                });  
                
                this.classList.add('active');  
                updateIndicatorPosition(this);  
            }  
            
            list.forEach((item) => {  
                item.addEventListener('click', activeLink);  
            });  
            
            document.addEventListener('DOMContentLoaded', () => {  
                const activeItem = document.querySelector('.list-moju.active');  
                if (activeItem) {  
                    updateIndicatorPosition(activeItem);  
                }  
            });  
            
            window.addEventListener('resize', () => {  
                const activeItem = document.querySelector('.list-moju.active');  
                if (activeItem) {  
                    updateIndicatorPosition(activeItem);  
                }  
            });  
            </script> 
</body>  
</html>






