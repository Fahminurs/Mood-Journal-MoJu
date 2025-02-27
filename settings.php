<?php
require_once 'koneksi.php';
require_once __DIR__ . '/Auth/auth.php'; 
session_start();
AuthHelper::requireRole(['user']);    

// Redirect jika pengguna belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: /moju/login.php");
    exit;
}

// Mendapatkan data pengguna
try {
    $stmt = executeQuery(
        "SELECT username, nama, email, tanggal_lahir , foto_profile FROM user WHERE id_user = ?",
        [$_SESSION['user_id']]
    );
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo "Pengguna tidak ditemukan.";
        exit;
    }
} catch (Exception $e) {
    echo "Terjadi kesalahan saat mengambil data pengguna.";
    exit;
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
    <link rel="stylesheet" href="Assets/css/settings.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
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

    <h1 class="page-title">Settings</h1>
    
    <div class="settings-container">
        <div class="profile-pic">
            <img src="<?= htmlspecialchars($user['foto_profile']); ?>" alt="Profile Picture">
        </div>

        <!-- Form Settings -->
       
            <div class="form-group">
                <label>Nickname</label>
                <span class="colon">:</span>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Nama</label>
                <span class="colon">:</span>
                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Email</label>
                <span class="colon">:</span>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <span class="colon">:</span>
                <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($user['tanggal_lahir']); ?>" disabled>
            </div>

            <div class="button-group">
                <a href="logout.php" style="text-decoration: none;" id="logout">
                <button type="button" class="btn btn-logout" >  
                    <i class="fas fa-sign-out-alt"></i> Log Out  
                </button>  
                </a>
                <a href="ubahPassword.php" style="text-decoration: none;">
                <button type="button" class="btn btn-password">  
                    <i class="fas fa-lock"></i> Ubah Password  
                </button>  
                </a>
                <a href="ubahProfile.php" style="text-decoration: none;">
                <button type="submit" class="btn btn-edit">  
                    <i class="fas fa-edit"></i> edit  
                </button>
                </a>
            </div>
     
    </div>
    <div style="margin-bottom: 150px;"></div>
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

 
    <script>
    // Add event listener to the logout button
    document.getElementById('logout').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default action

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Anda akan keluar dari akun Anda.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the logout URL if confirmed
                window.location.href = 'logout.php';
            }
        });
    });
</script>

    <script>  
        // Fungsi untuk toggle active class pada nav items  
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