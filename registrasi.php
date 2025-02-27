<?php  
require_once 'koneksi.php';  
session_start();  

// Fungsi untuk generate username unik
function generateUsername($pdo) {
    do {
        // Generate angka random
        $randomNumber = rand(1000, 9999);
        $username = "Anonymous#" . $randomNumber;

        // Cek di database apakah username sudah ada
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch();

        // Jika count = 0, username unik
    } while ($result['count'] > 0);

    return $username;
}

// Proses registrasi  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $nama = filter_var($_POST['nama'], FILTER_SANITIZE_STRING);  
    $tanggal_lahir = $_POST['tanggal_lahir'];  
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  
    $password = $_POST['password'];  
    $confirm_password = $_POST['confirm_password'];  
    
    try {  
        // Validasi input  
        $errors = [];  
        
        if (empty($nama)) $errors[] = "Nama harus diisi";  
        if (empty($tanggal_lahir)) $errors[] = "Tanggal lahir harus diisi";  
        if (empty($email)) $errors[] = "Email harus diisi";  
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";  
        if (empty($password)) $errors[] = "Password harus diisi";  
        if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter";  
        if ($password !== $confirm_password) $errors[] = "Password tidak cocok";  
        
        // Cek email sudah terdaftar  
        $db = getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();  
        if ($result['count'] > 0) {  
            $errors[] = "Email sudah terdaftar";  
        }  
        
        if (!empty($errors)) {  
            echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);  
            exit;  
        }  
        
        // Hash password  
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);  

        // Generate username unik
        $username = generateUsername($db);
        $foto_profile = "Assets/img/foto-profile/Default.png";

        // Insert user baru  
        $stmt = $db->prepare(
            "INSERT INTO user (nama, tanggal_lahir, email, password, username,foto_profile, created_at) 
            VALUES (?, ?, ?, ?, ?, ?,NOW())"
        );
        $stmt->execute([$nama, $tanggal_lahir, $email, $hashed_password, $username, $foto_profile]);
        
        echo json_encode(['status' => 'success', 'username' => $username]);  
        exit;  
        
    } catch (PDOException $e) {  
        Logger::getInstance()->logError('REGISTRATION_ERROR', 'User registration failed', $e);
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem']);  
        exit;  
    }  
}  
?>
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Moju Registrasi</title>  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">  
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">  
    <link rel="stylesheet" href="Assets/css/registrasi.css">  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">  
</head>  
<body>  
    <div class="outer-container">  
        <div class="Registrasi-container"></div>  
        <div class="gambar-ilustrasi">  
            <img src="Assets/img/image 1.png" alt="Ilustrasi">  
        </div>  
        <div class="Registrasi-card">  
            <form id="registrationForm" method="POST">  
            <div class="Registrasi-form">  
                <div class="logo">MOJU</div>  
                <h2>Selamat Datang!</h2>  
                <p class="subtitle">Silahkan Registrasi Untuk Melanjutkan</p>  
                
                    <!-- Baris 1: Nama dan Tanggal Lahir -->  
                    <div class="form-row">  
                        <div class="input-group">  
                            <i class="fa-solid fa-user"></i>  
                            <input type="text" name="nama" placeholder="Nama" required>  
                        </div>  
                        
                        <div class="input-group" style="flex: 1;">  
                            <i class="fa-solid fa-calendar"></i>  
                            <input type="date" name="tanggal_lahir" required>  
                        </div>  
                    </div>  
                    
                    <!-- Baris 2: Email -->  
                    <div class="input-group full-width">  
                        <i class="fa-solid fa-at"></i>  
                        <input type="email" name="email" placeholder="Email" required>  
                    </div>  
                    
                    <!-- Baris 3: Password dan Konfirmasi -->  
                    <div class="form-row">  
                        <div class="input-group">  
                            <i class="fa-solid fa-lock"></i>  
                            <input type="password" name="password" id="password" placeholder="Password" required>  
                            <i class="fa-solid fa-eye toggle-password"></i>  
                        </div>  
                        
                        <div class="input-group">  
                            <i class="fa-solid fa-lock"></i>  
                            <input type="password" name="confirm_password" id="confirm-password" placeholder="Konfirmasi Password" required>  
                            <i class="fa-solid fa-eye toggle-confirm-password"></i>  
                        </div>  
                    </div>  
                    
                    <div class="links">  
                        <a href="login.php">Sudah mempunyai akun? Masuk Disini</a>  
                    </div> 
                    
                    <button type="submit">Registrasi</button>  
                </div>  
            </form>  
        </div>  
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
    
    <script>  
        // Toggle password visibility  
        document.querySelectorAll('.toggle-password, .toggle-confirm-password').forEach(toggle => {  
            toggle.addEventListener('click', function() {  
                const input = this.previousElementSibling;  
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';  
                input.setAttribute('type', type);  
                
                this.classList.toggle('fa-eye');  
                this.classList.toggle('fa-eye-slash');  
            });  
        });  

        // Form submission  
        document.getElementById('registrationForm').addEventListener('submit', function(e) {  
            e.preventDefault();  
            
            const password = document.getElementById('password').value;  
            const confirmPassword = document.getElementById('confirm-password').value;  
            
            if (password !== confirmPassword) {  
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
                        text: 'Registrasi berhasil! Silahkan login.',  
                        icon: 'success',  
                        confirmButtonText: 'OK',  
                        customClass: { container: 'my-swal' }  
                    }).then(() => {  
                        window.location.href = 'login.php';  
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
    </script>  

    <style>  
        .my-swal {  
            font-family: 'Poppins', sans-serif;  
        }  

        @media (max-width: 768px) {  
            .swal2-popup {  
                font-size: 0.8em !important;  
                width: 90% !important;  
            }  
        }  

        @media (max-width: 480px) {  
            .swal2-popup {  
                font-size: 0.7em !important;  
                padding: 1em !important;  
            }  
            .swal2-title { font-size: 1.5em !important; }  
            .swal2-content { font-size: 1.2em !important; }  
        }  

        @media (min-width: 768px) and (max-width: 1024px) {  
            .swal2-popup { width: 60% !important; }  
        }  
    </style>  
</body>  
</html>