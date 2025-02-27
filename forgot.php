
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Moju Lupa Password</title>  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">  
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link rel="stylesheet" href="Assets/css/forgot.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-container {  
    display: flex;  
    flex-direction: column;  
    justify-content: center;  
    align-items: center;  
    position: relative;
    right: -25px;
}  

/* Form */  
#forgot-form {  
    display: flex;  
    flex-direction: column;  
    gap: 15px; /* Jarak antar elemen dalam form */  
    width: 100%; /* Pastikan form memenuhi container */  
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
            <div class="forgot-form">  
                <div class="logo">MOJU</div>  
                <h1 class="forgot-title">Lupa Password?</h1>  
                <p class="forgot-subtitle">Tenang MoJu dapat mengirimkan link untuk mengubah password anda</p>  
                <div class="form-container">
                <form action="lupa_password.php" method="post" id="forgot-form">
                    <div class="input-email">
                        <i class="fa-solid fa-at"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <button class="send-link-btn" type="submit">Kirim Link</button>
                </form>
                <a href="login.php" class="Registrasi-link">Ingat Email/Password ? Masuk disini</a>  
                </div>
                
            </div>  
        </div>  
    </div>
    
    <script>
        // Fungsi untuk menampilkan alert loading saat form dikirim
        document.getElementById('forgot-form').addEventListener('submit', function(event) {
            event.preventDefault();  // Mencegah pengiriman form secara langsung

            // Menampilkan SweetAlert dengan tipe loading
            Swal.fire({
                title: 'Tunggu Sebentar...',
                text: 'Sedang memproses permintaan Anda...',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim form setelah menunggu sebentar
            this.submit();
        });


    </script>
    <script>
    // Menampilkan alert berdasarkan session
    <?php
    session_start();
    if (isset($_SESSION['status']) && isset($_SESSION['message'])) {
        $status = $_SESSION['status'];
        $message = $_SESSION['message'];
        echo "
        Swal.fire({
            title: '" . ($status === 'success' ? 'Berhasil!' : 'Gagal!') . "',
            text: '$message',
            icon: '$status'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika OK diklik, arahkan ke login.php
                window.location.href = 'login.php';
            }
        });
        ";

        // Menghapus session setelah menampilkan alert
        unset($_SESSION['status']);
        unset($_SESSION['message']);
    }
    ?>
</script>

</body>  
</html>