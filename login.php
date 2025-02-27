<?php  
require_once 'koneksi.php';  
require_once 'auth/auth.php';  

session_start();  

// Redirect if already logged in  
// if(isset($_SESSION['user_id'])) {  
//     if($_SESSION['role'] == 'admin') {  
//         header("Location: /Admin/pages/dashboard.php");  
//     } elseif ($_SESSION['role'] == 'admin artikel'){
//         header("Location: /Admin/pages/artikel.php"); 
//     }else {  
//         header("Location: home.php");  
//     }  
//     exit;  
// }  

// Handle login  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    try {  
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  
        $password = $_POST['password'];  
        
        // Validation  
        $errors = [];  
        if (empty($email)) $errors[] = "Email harus diisi";  
        if (empty($password)) $errors[] = "Password harus diisi";  
        
        if (empty($errors)) {  
            $stmt = executeQuery(  
                "SELECT id_user, username, email,foto_profile, password, role FROM user WHERE email = ?",   
                [$email]  
            );  
            $user = $stmt->fetch(PDO::FETCH_ASSOC);  
            
            if ($user && password_verify($password, $user['password'])) {  
                // Login successful  
                $_SESSION['user_id'] = $user['id_user'];  
                $_SESSION['username'] = $user['username'];  
                $_SESSION['email'] = $user['email'];  
                $_SESSION['foto_profile'] = $user['foto_profile'];  
                $_SESSION['password'] = $user['password'];  
                $_SESSION['role'] = $user['role'];  

   // Insert or update `islogin` table  
   $id_user = $user['id_user'];  
   $current_timestamp = date("Y-m-d H:i:s");

   // Check if user already has an entry in the `islogin` table
   $checkStmt = executeQuery("SELECT id_islogin FROM islogin WHERE id_user = ?", [$id_user]);  
   $existingLogin = $checkStmt->fetch(PDO::FETCH_ASSOC);  

   if ($existingLogin) {  
       // Update existing record
       $updateStmt = executeQuery(  
           "UPDATE islogin SET login_at = ? WHERE id_user = ?",  
           [$current_timestamp, $id_user]  
       );  
   } else {  
       // Insert new record  
       $insertStmt = executeQuery(  
           "INSERT INTO islogin (id_user, login_at) VALUES (?, ?)",  
           [$id_user, $current_timestamp]  
       );  
   }

                // Set redirect URL based on role  
                $redirects = [  
                    'admin' => '/moju/Admin/pages/dashboard.php',  
                    'admin artikel' => '/moju/Admin/pages/artikel.php', // Tambahkan redirect untuk admin artikel  
                    'user' => '/moju/home.php'  
                ];  
                
                $redirect = $redirects[$user['role']] ?? '/home.php';  
                
                echo json_encode([  
                    'status' => 'success',  
                    'redirect' => $redirect  
                ]);  
                exit;  
            } else {  
                echo json_encode([  
                    'status' => 'error',  
                    'message' => 'Email atau password salah'  
                ]);  
                exit;  
            }  
        } else {  
            echo json_encode([  
                'status' => 'error',  
                'message' => implode("<br>", $errors)  
            ]);  
            exit;  
        }  
        
    } catch (Exception $e) {  
        echo json_encode([  
            'status' => 'error',  
            'message' => 'Terjadi kesalahan sistem'  
        ]);  
        exit;  
    }  
}  
?> 
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Moju Login</title>  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">  
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">  
    <link rel="stylesheet" href="Assets/css/login.css">  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">  
</head>  
<body>  
    <div class="outer-container">  
        <div class="login-container"></div>  
        <div class="gambar-ilustrasi">  
            <img src="./Assets/img/image 1.png" alt="Ilustrasi">  
        </div>  
        <div class="login-card">  
            <form id="loginForm" method="POST">  
            <div class="login-form">  
                <div class="logo">MOJU</div>  
                <h2>Selamat Datang kembali!</h2>  
                <p class="subtitle">Silahkan Login untuk Melanjutkan</p>  
                
                    <div class="input-group">  
                        <i class="fa-solid fa-at"></i>  
                        <input type="email" name="email" placeholder="Email" required>  
                    </div>  
                    
                    <div class="input-group">  
                        <i class="fa-solid fa-lock"></i>  
                        <input type="password" name="password" id="password" placeholder="Password" required>  
                        <i class="fa-solid fa-eye toggle-password"></i>  
                    </div>  
                    
                    <div class="links">  
                        <a href="registrasi.php">Registrasi?</a>  
                        <a href="forgot.php">Lupa Password?</a>  
                    </div>  
                    
                    <button type="submit">Masuk</button>  
                </div>  
            </form>  
        </div>  
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
    
    <script>  
 document.getElementById('loginForm').addEventListener('submit', function(e) {  
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
                text: 'Login berhasil',  
                icon: 'success',  
                showConfirmButton: false,  
                timer: 1500  
            }).then(() => {  
                window.location.href = data.redirect;  
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
            text: 'Terjadi kesalahan sistem',  
            icon: 'error',  
            confirmButtonText: 'OK'  
        });  
    });  
}); 
    </script>  

 
</body>  
</html>