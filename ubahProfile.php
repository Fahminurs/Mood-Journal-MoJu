<?php  
require_once 'koneksi.php';  
require_once __DIR__ . '/Auth/auth.php'; 
session_start();  
AuthHelper::requireRole(['user']);    

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = getConnection();
// Ambil data pengguna dari database
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->execute([$user_id]);
$profileData = $stmt->fetch();

if (!$profileData) {
    die("User not found.");
}

// Proses update profile jika ada form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = filter_var($_POST['nama'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $foto_profile = $profileData['foto_profile'];  // Foto tidak berubah jika tidak ada upload baru

    // Validasi input
    $errors = [];
    if (empty($nama)) $errors[] = "Nama harus diisi";
    if (empty($email)) $errors[] = "Email harus diisi";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";
    
    // Jika ada foto baru yang diupload
    if (!empty($_FILES['foto']['name'])) {
        $file = $_FILES['foto'];
        $targetDir = "Assets/img/foto-profile/";
        $fileName = uniqid() . "_" . basename($file['name']);
        $targetFilePath = $targetDir . $fileName;

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            $foto_profile = $targetFilePath;
        } else {
            $errors[] = "Gagal mengupload foto.";
        }
    }

    // Jika tidak ada error, update data pengguna
    if (empty($errors)) {
        try {
            $stmt = $db->prepare(
                "UPDATE user SET nama = ?, email = ?, tanggal_lahir = ?, foto_profile = ? WHERE id_user = ?"
            );
            $stmt->execute([$nama, $email, $tanggal_lahir, $foto_profile, $user_id]);

            echo json_encode(['status' => 'success', 'message' => 'Profil berhasil diperbarui']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem']);
        }
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
        exit;
    }
}
?>


<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Settings Page</title>  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;800&display=swap" rel="stylesheet">  
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">  
    <link rel="stylesheet" href="Assets/css/ubahProfile.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">  
</head>  
<body>  
    <!-- Sidebar dari kode sebelumnya -->  
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
    <h1 class="page-title">Settings</h1>  
    
    <div class="settings-container">
        <div class="profile-pic">
            <img src="<?php echo htmlspecialchars($profileData['foto_profile']); ?>" alt="Profile Picture">
        </div>

        <h1 class="page-title">Ubah Profile</h1>

        <form id="updateProfileForm">
            <div class="form-group">
                <label>Nickname</label>
                <span class="colon">:</span>
                <input type="text" disabled value="<?php echo htmlspecialchars($profileData['username']); ?>" class="disabled-input">
            </div>

            <div class="form-group">
                <label>Nama</label>
                <span class="colon">:</span>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($profileData['nama']); ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <span class="colon">:</span>
                <input type="email" name="email" value="<?php echo htmlspecialchars($profileData['email']); ?>">
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <span class="colon">:</span>
                <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($profileData['tanggal_lahir']); ?>">
            </div>

            <div class="form-group">
    <label>Foto Profile</label>
    <span class="colon">:</span>
    <div class="file-input-wrapper">
        <input type="file" name="foto" id="profile-photo" accept="image/*">
        <label for="profile-photo" class="file-label">
            <i class="fas fa-upload"></i> Pilih File
        </label>
    </div>
    <span class="file-name">Tidak ada file yang dipilih</span> <!-- Menampilkan nama file yang dipilih -->
</div>

            <div class="button-group">
                <button type="button" id="btnCancel" class="btn btn-cancel">Batal</button>
                <button type="submit" class="btn btn-save">Simpan</button>
            </div>
        </form>
    </div>
    <div style="margin-bottom: 20vh;"></div>
            <!-- Navigation Bar -->
            <div class="Navigation-moju">
                <ul>
                    <li class="list-moju ">
                        <a href="home.php">
                            <span class="icon-moju "><i class="fas fa-home"></i></span>
                            <span class="text">Home</span>
                        </a>
                    </li>
                   <li class="list-moju">
                        <a href="#">
                            <span class="icon-moju"><i class="fa-solid fa-comment"></i></span>
                            <span class="text">Diskusi</span>
                        </a>
                    </li>
                   <li class="list-moju active">
                        <a href="#">
                            <span class="icon-moju"><i class="fas fa-cog"></i></span>
                            <span class="text">Settings</span>
                        </a>
                    </li>
        
                    <div class="indikator-moju"></div>
                </ul>
        
            </div>
            
        <!-- End Navigation -->

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
            document.querySelectorAll('.nav-item').forEach(item => {  
            item.addEventListener('click', function() {  
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));  
                this.classList.add('active');  
            });  
        });  

        // Fungsi untuk delete note  
        document.querySelector('.delete-btn').addEventListener('click', function() {  
            const noteCard = this.closest('.note-card');  
            noteCard.style.display = 'none';  
        });  
</script>

    <script>  
document.getElementById('profile-photo').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'Tidak ada file yang dipilih';
    document.querySelector('.file-name').textContent = fileName; // Menampilkan nama file atau teks default
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
<script>
    // JavaScript untuk update form
    document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('ubahProfile.php', {
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
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(() => {
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan sistem.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });

    // JavaScript untuk membatalkan perubahan
    document.getElementById('btnCancel').addEventListener('click', function() {
        Swal.fire({
            title: 'Batalkan Perubahan?',
            text: "Semua perubahan akan dihapus.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Lanjutkan'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'settings.php';
            }
        });
    });

    // Menampilkan nama file yang dipilih
    document.getElementById('profile-photo').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Tidak ada file yang dipilih';
        document.querySelector('.file-name').textContent = fileName;
    });
</script>
</body>  
</html>