<?php
require 'koneksi.php';
// require_once __DIR__ . '/Auth/auth.php'; 
// AuthHelper::requireRole(['user']);    

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        echo json_encode(['status' => 'error', 'message' => 'Token tidak ditemukan.']);
        exit;
    }

    $token = $_GET['token'];

    $user = selectQuery(
        "SELECT * FROM user WHERE token = ? AND expires_token > NOW()",
        [$token],
        'Validate reset token'
    );

    if (empty($user)) {
        echo json_encode(['status' => 'error', 'message' => 'Token tidak valid atau sudah kedaluwarsa.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        echo json_encode(['status' => 'error', 'message' => 'Token tidak ditemukan.']);
        exit;
    }

    $token = $_GET['token'];

    $user = selectQuery(
        "SELECT * FROM user WHERE token = ? AND expires_token > NOW()",
        [$token],
        'Validate reset token'
    );

    if (empty($user)) {
        echo json_encode(['status' => 'error', 'message' => 'Token tidak valid atau sudah kedaluwarsa.']);
        exit;
    }

    $password = $_POST['password'] ?? null;
    $confirmPassword = $_POST['confirm_password'] ?? null;

    if (empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password baru tidak boleh kosong.']);
        exit;
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Password baru tidak cocok.']);
        exit;
    }

    $newPassword = password_hash($password, PASSWORD_DEFAULT);

    $updateResult = executeQuery(
        "UPDATE user SET password = ?, token = NULL, expires_token = NULL WHERE token = ?",
        [$newPassword, $token],
        'Update user password'
    );

    if ($updateResult) {
        echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah. Silakan login.']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat memperbarui password.']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">  
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">  
    <link rel="stylesheet" href="Assets/css/registrasi.css">  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .input-group input[type="password"] {
    padding-right: 160px;
}  
.input-group{
    margin-left:30px ;

}
button{
    margin-left:30px ;
}

@media screen and (max-width: 480px) {
    .input-group{
    margin-left:0 ;

}
button{
    margin-left:0 ;
}

}
</style>
</head>
<body>
    <div class="outer-container">
    <div class="Registrasi-container"></div>  
        <div class="gambar-ilustrasi">  
            <img src="Assets/img/image 1.png" alt="Ilustrasi">  
        </div>  
        <div class="Registrasi-card">
            <div class="Registrasi-form">
                <div class="logo">MOJU</div>
                <h2>Reset Password</h2>
                <p class="subtitle">Masukkan Password Baru Anda</p>
                <form  id="resetPasswordForm" method="POST" >
                    <!-- Password Baru -->  
                    <div class="input-group full-width">  
                        <i class="fa-solid fa-lock"></i>  
                        <input type="password" name="password" id="password" placeholder="Password Baru" required>  
                        <i class="fa-solid fa-eye toggle-password"></i>  
                    </div>  
                    
                    <!-- Konfirmasi Password Baru -->  
                    <div class="input-group full-width">  
                        <i class="fa-solid fa-lock"></i>  
                        <input type="password" name="confirm_password" id="confirm-password" placeholder="Konfirmasi Password Baru" required>  
                        <i class="fa-solid fa-eye toggle-confirm-password"></i>  
                    </div>  
                    
                  
                    <button type="submit" >Reset Password</button>  
                </form>  
            </div>
        </div>
    </div>
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
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {  
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
        }); 
    </script>
  <script>
        document.getElementById('resetPasswordForm').addEventListener('submit', function (e) {
            e.preventDefault();
            
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
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                    icon: 'error'
                });
            });
        });
    </script>
</body>
</html>
