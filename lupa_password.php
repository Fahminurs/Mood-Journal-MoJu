<?php  
session_start();   
require 'koneksi.php';  
require 'vendor/autoload.php'; // PHPMailer  

use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $email = trim($_POST['email']);  

    try {  
        // Cek apakah email terdaftar  
        $user = selectQuery(  
            "SELECT * FROM user WHERE email = ?",  
            [$email],  
            'Check if email exists'  
        );  

        if (empty($user)) {  
            $_SESSION['status'] = 'error';  
            $_SESSION['message'] = 'Email tidak ditemukan dalam sistem!';  
            header("Location: forgot.php");  
            exit;  
        }  

        // Generate token unik dan waktu kedaluwarsa  
        $token = bin2hex(random_bytes(32));   
        $expires_token = date("Y-m-d H:i:s", strtotime("+1 hour"));  

        // Update token dan expires_token di database  
        executeQuery(  
            "UPDATE user SET token = ?, expires_token = ? WHERE email = ?",  
            [$token, $expires_token, $email],  
            'Update reset token and expiry'  
        );  

        // Mengecek ip   
        $output = shell_exec('ipconfig');  

        // Cari baris dengan "IPv4 Address"  
        $lines = explode("\n", $output);  
        $ipv4Address = null;  

        foreach ($lines as $line) {  
            if (strpos($line, 'IPv4 Address') !== false) {  
                // Ambil alamat IP setelah tanda ":"  
                $parts = explode(':', $line);  
                if (isset($parts[1])) {  
                    $ipv4Address = trim($parts[1]);  
                }  
                break; // Keluar dari loop setelah menemukan  
            }  
        }  
        
        $ip_get = $ipv4Address;  

        // Kirim email reset password  
        $resetLink = "http://$ip_get/moju/reset_password.php?token=" . urlencode($token);  
        $mail = new PHPMailer(true);  

        try {  
            // Konfigurasi SMTP  
            $mail->isSMTP();  
            $mail->Host = 'smtp.gmail.com'; // Ganti dengan host SMTP Anda  
            $mail->SMTPAuth = true;  
            $mail->Username = 'fahminursafaat@gmail.com'; // Ganti dengan email pengirim  
            $mail->Password = 'ghds jowh msdq rmwy';   // Ganti dengan password email pengirim  
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
            $mail->Port = 587;  

            // Pengirim dan penerima  
            $mail->setFrom('fahminursafaat@gmail.com', 'MoJu Support');  
            $mail->addAddress($email);  

            // Konten email  
            $mail->isHTML(true);  
            $mail->Subject = 'Reset Password Anda';  
            $mail->Body = "  
                <h1>Reset Password</h1>  
                <p>Kami menerima permintaan untuk mereset password akun Anda.</p>  
                <p>Anda telah meminta reset password dari IP: $ip_get.</p>  
                <p>Silakan klik link di bawah ini untuk mereset password Anda:</p>  
                <a href='$resetLink'>$resetLink</a>  
                <p>Link ini hanya berlaku selama 1 jam.</p>  
            ";  

            $mail->send();  

            // Simpan status dan pesan ke session  
            $_SESSION['status'] = 'success';  
            $_SESSION['message'] = 'Link reset password telah dikirim ke email Anda.';  
            header("Location: forgot.php");  // Redirect ke halaman forgot.php  
            exit;  

        } catch (Exception $mailException) {  
            // Error spesifik PHPMailer  
            $_SESSION['status'] = 'error';  
            $_SESSION['message'] = 'Gagal mengirim email. Error: ' . $mailException->getMessage();  
            header("Location: forgot.php");  
            exit;  
        }  

    } catch (Exception $e) {  
        // Error umum  
        $_SESSION['status'] = 'error';  
        $_SESSION['message'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();  
        header("Location: forgot.php");  
        exit;  
    } catch (PDOException $pdoException) {  
        // Error database  
        $_SESSION['status'] = 'error';  
        $_SESSION['message'] = 'Kesalahan database: ' . $pdoException->getMessage();  
        header("Location: forgot.php");  
        exit;  
    }  
}  
?>