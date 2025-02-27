<?php  
session_start();  
require_once __DIR__ . '/../../Auth/auth.php';

// Pastikan user sudah login  
AuthHelper::requireRole(['admin', 'admin artikel']);   
$currentUser = AuthHelper::getCurrentUser();  
$currentRole = $currentUser['role'] ?? '';  
try {  
    // Query untuk mengambil detail user  
    $userData = selectQuery(  
        "SELECT * FROM user WHERE id_user = ?",   
        [$_SESSION['user_id']],   
        'Fetch current user details'  
    )[0];  

} catch (Exception $e) {  
    // Tangani error  
    die("Gagal mengambil data pengguna: " . $e->getMessage());  
}  

// Proses update profile  
if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    try {  
        // Validasi input  
        $nama = $_POST['nama'] ?? $userData['nama'];  
        $email = $_POST['email'] ?? $userData['email'];  
        $username = $_POST['username'] ?? $userData['username'];  
        $tanggal_lahir = $_POST['tanggal_lahir'] ?? $userData['tanggal_lahir'];  

        // Proses upload foto  
        $foto_profile = $userData['foto_profile'] ?? '../assets/img/Default.png';   
        
        if (isset($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] == 0) {  
            // Gunakan path absolut sesuai dengan struktur Anda  
            $uploadDir = '../../Assets/img/foto-profile/';  
            
            // Buat direktori jika tidak ada  
            if (!file_exists($uploadDir)) {  
                mkdir($uploadDir, 0777, true);  
            }  

            // Validasi tipe file menggunakan getimagesize()  
            $imageInfo = getimagesize($_FILES['foto_profile']['tmp_name']);  
            if ($imageInfo === false) {  
                throw new Exception("File yang diunggah bukan gambar valid.");  
            }  

            // Daftar tipe mime yang diizinkan  
            $allowedTypes = [  
                'image/jpeg' => ['jpg', 'jpeg'],  
                'image/png' => ['png'],  
                'image/gif' => ['gif']  
            ];  

            // Ambil tipe mime aktual  
            $fileType = $imageInfo['mime'];  
            
            // Validasi tipe file  
            if (!array_key_exists($fileType, $allowedTypes)) {  
                throw new Exception("Tipe file tidak diizinkan. Hanya JPEG, PNG, dan GIF yang diperbolehkan.");  
            }  

            // Validasi ukuran file (maksimal 5MB)  
            if ($_FILES['foto_profile']['size'] > 5 * 1024 * 1024) {  
                throw new Exception("Ukuran file terlalu besar. Maksimal 5MB.");  
            }  

            // Generate nama file unik  
            $fileExtension = $allowedTypes[$fileType][0];  
            $namaFile = uniqid() . '_profile.' . $fileExtension;  
            $targetPath = $uploadDir . $namaFile;  

            // Pindahkan file  
            if (move_uploaded_file($_FILES['foto_profile']['tmp_name'], $targetPath)) {  
                // Gunakan path relatif untuk database dan session  
                $foto_profile = 'Assets/img/foto-profile/' . $namaFile;  
                
                // Hapus foto lama jika ada dan bukan foto default   
                if ($userData['foto_profile'] &&   
                    $userData['foto_profile'] != '../../assets/img/Default.png' &&   
                    file_exists('../../Assets/img/foto-profile/' . $userData['foto_profile'])) {  
                    unlink('../../Assets/img/foto-profile/' . $userData['foto_profile']);  
                }  
            } else {  
                throw new Exception("Gagal mengunggah file.");  
            }  
        }  

        // Validasi email  
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
            throw new Exception("Format email tidak valid.");  
        }  

        // Cek apakah email sudah digunakan oleh user lain  
        $emailCheck = selectQuery(  
            "SELECT id_user FROM user WHERE email = ? AND id_user != ?",   
            [$email, $_SESSION['user_id']]  
        );  

        if (!empty($emailCheck)) {  
            throw new Exception("Email sudah digunakan oleh pengguna lain.");  
        }  

        // Update database  
        $updateQuery = "UPDATE user SET   
            nama = ?,   
            email = ?,   
            username = ?,   
            tanggal_lahir = ?,   
            foto_profile = ?  
            WHERE id_user = ?";  

        executeQuery(  
            $updateQuery,   
            [  
                $nama,   
                $email,   
                $username,   
                $tanggal_lahir,   
                $foto_profile,   
                $_SESSION['user_id']  
            ],  
            'Update user profile'  
        );  

        // Update session dengan data baru  
        $_SESSION['username'] = $username;  
        $_SESSION['email'] = $email;  
        if ($foto_profile) {  
            $_SESSION['foto_profile'] = $foto_profile;  
        }  

        // Set session flash untuk sweet alert  
        $_SESSION['profile_update_success'] = true;  
        
        // Redirect untuk menghindari resubmission  
        header("Location: " . $_SERVER['PHP_SELF']);  
        exit();  

    } catch (Exception $e) {  
        // Tangani error  
        $_SESSION['profile_update_error'] = $e->getMessage();  
        
        // Redirect kembali dengan pesan error  
        header("Location: " . $_SERVER['PHP_SELF']);  
        exit();  
    }  
}  
?>
<!-- Dalam bagian <head> tambahkan: -->  

 
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Profile - Admin
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <!-- Flatpickr -->  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">  
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>  
  <style>
    .breadcrumb-item::before{
      color: rgb(255, 255, 255) !important;
    }
    #tanggalLahirInput.editable {  
        cursor: pointer !important;  
        background-color: #f8f9fa;  
        border: 1px solid #ced4da;  
    }  
    
    #tanggalLahirInput.editable:hover {  
        background-color: #e9ecef;  
    }  
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('../assets/img/profile-background.png'); background-position: center; background-size: 100% 100%; background-repeat: no-repeat;">  
    <span class="mask bg-primary opacity-6"></span>  
</div>  
<aside  
    class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"  
    id="sidenav-main"  
    style="z-index: 20"  
>  
    <div class="sidenav-header">  
        <a class="navbar-brand m-0" href="#">  
            <span class="logo">MOJU</span>  
            <br />  
            <span class="logo-bawah">  
                <?php   
                echo ($currentRole === 'admin artikel') ? 'Admin Artikel' : 'Admin Utama';   
                ?>  
            </span>  
        </a>  
    </div>  
    <hr class="horizontal dark mt-0" />  
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">  
        <ul class="navbar-nav">  
            <?php if ($currentRole === 'admin'): ?>  
                <li class="nav-item">  
                    <a class="nav-link" href="../pages/dashboard.php">  
                        <div  
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center"  
                        >  
                            <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>  
                        </div>  
                        <span class="nav-link-text ms-1">Dashboard</span>  
                    </a>  
                </li>  
                <li class="nav-item">  
                    <a class="nav-link " href="../pages/artikel.php">  
                        <div  
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center"  
                        >  
                            <i  
                                class="ni ni-single-copy-04 text-dark text-sm opacity-10"  
                            ></i>  
                        </div>  
                        <span class="nav-link-text ms-1">Artikel</span>  
                    </a>  
                </li>  
                <li class="nav-item">  
                    <a class="nav-link" href="../pages/akun.php">  
                        <div  
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center"  
                        >  
                            <i class="ni ni-badge text-dark text-sm opacity-10"></i>  
                        </div>  
                        <span class="nav-link-text ms-1">Akun</span>  
                    </a>  
                </li>  
            <?php endif; ?>  

            <?php if ($currentRole === 'admin artikel'): ?>  
                <li class="nav-item">  
                    <a class="nav-link" href="../pages/artikel.php">  
                        <div  
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center"  
                        >  
                            <i  
                                class="ni ni-single-copy-04 text-dark text-sm opacity-10"  
                            ></i>  
                        </div>  
                        <span class="nav-link-text ms-1">Artikel</span>  
                    </a>  
                </li>  
            <?php endif; ?>  

            <li class="nav-item mt-3">  
                <h6  
                    class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6"  
                >  
                    Account pages  
                </h6>  
            </li>  
            <li class="nav-item">  
                <a class="nav-link active" href="../pages/profile.php">  
                    <div  
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center"  
                    >  
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>  
                    </div>  
                    <span class="nav-link-text ms-1">Profile</span>  
                </a>  
            </li>  
            <li class="nav-item">  
                <a class="nav-link" href="#" id="logout">  
                    <div  
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center"  
                    >  
                        <i class="ni ni-collection text-dark text-sm opacity-10"></i>  
                    </div>  
                    <span class="nav-link-text ms-1">Logout</span>  
                </a>  
            </li>  
        </ul>  
    </div>  
</aside> 
  <div class="main-content position-relative max-height-vh-100 h-100 ps">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">  
      <div class="container-fluid py-1 px-3">  
        <nav aria-label="breadcrumb" style="background-color: rgba(255, 17, 229, 0.2); border-radius: 10px; padding: 5px;">  
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">  
            <li class="breadcrumb-item text-sm">  
              <a href="javascript:;" style="color: white;">Pages</a>  
            </li>  
            <li class="breadcrumb-item text-sm active" aria-current="page" style="color: white;">  
              Profile  
            </li>  
          </ol>  
          <h6 class="font-weight-bolder mb-0" style="color: white;">Profile</h6>  
        </nav>  
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">  
          <ul class="navbar-nav justify-content-end">  
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center" style="background-color: rgba(255, 17, 229,0.5); border-radius: 6px; padding: 8px 8px 8px 8px !important;">  
              <a href="javascript:;" class="nav-link text-black p-0" id="iconNavbarSidenav">  
                <div class="sidenav-toggler-inner">  
                  <i class="sidenav-toggler-line" style="background-color: white;"></i>  
                  <i class="sidenav-toggler-line" style="background-color: white;"></i>  
                  <i class="sidenav-toggler-line" style="background-color: white;"></i>  
                </div>  
              </a>  
            </li>  
          </ul>
        </div>  
      </div>  
    </nav>
    <!-- End Navbar -->
    <div class="card shadow-lg mx-4 card-profile-bottom">
      <div class="card-body p-3">
        <div class="row gx-4">
          <div class="col-auto">
            <div class="avatar avatar-xl position-relative">
            <img src="../../<?= htmlspecialchars($userData['foto_profile'] ?: '../assets/img/default-profile.jpg') ?>" alt="profile_image" class="w-100 rounded-circle  border-dark shadow-sm">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">
              <?= htmlspecialchars($userData['nama']) ?>  
              </h5>
              <p class="mb-0 font-weight-bold text-sm">
              <?= htmlspecialchars($userData['role']) ?>
              </p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">  
            <div class="nav-wrapper position-relative end-0">  
                <ul role="tablist" class="nav nav-pills nav-fill p-1 bg-danger">  
                    <li class="nav-item" role="presentation" id="editProfileContainer">  
                        <a id="editProfileBtn" data-bs-toggle="tab" href="javascript:;" role="tab" aria-selected="false" tabindex="-1" class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center">  
                            <i class="ni ni-single-02 text-white"></i>  
                            <span class="ms-2 text-white fw-bold">Edit Profile</span>  
                        </a>  
                    </li>  
                    
                    <!-- Container untuk tombol Save dan Cancel (semula tersembunyi) -->  
                    <div id="saveEditContainer" class="d-none w-100 d-flex">  
                        <li class="nav-item w-50" role="presentation">  
                            <a id="saveProfileBtn" href="javascript:;" class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center bg-success">  
                                <i class="ni ni-check-bold text-white"></i>  
                                <span class="ms-2 text-white fw-bold">Save</span>  
                            </a>  
                        </li>  
                        <li class="nav-item w-50" role="presentation">  
                            <a id="cancelEditBtn" href="javascript:;" class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center bg-danger">  
                                <i class="ni ni-fat-remove text-white"></i>  
                                <span class="ms-2 text-white fw-bold">Cancel</span>  
                            </a>  
                        </li>  
                    </div>  
                </ul>  
            </div>  
        </div> 
        </div>
      </div>
    </div>
    <div class="container-fluid py-4">
      <div class="row">
        <div class="">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex align-items-center">
                <p class="mb-0 fw-bold text-black" style="color:black;">Tampilan Profile</p>   
                
              </div>
            </div>
            <div class="card-body">
<!-- Bagian Informasi User -->  
<hr class="horizontal dark">  
<p class="text-uppercase text-sm">Informasi User</p>  

<form id="profileForm" action="" method="POST" enctype="multipart/form-data">  
  <!-- Input fields yang sudah ada sebelumnya -->  
  <div class="row">  
      <div class="col-md-6">  
          <div class="form-group">  
              <label for="namaInput" class="form-control-label">Nama</label>  
              <input id="namaInput" name="nama" class="form-control" type="text" value="<?= htmlspecialchars($userData['nama']) ?>" disabled>  
          </div>  
      </div>  
      <div class="col-md-6">  
          <div class="form-group">  
              <label for="emailInput" class="form-control-label">Email address</label>  
              <input id="emailInput" name="email" class="form-control" type="email" value="<?= htmlspecialchars($userData['email']) ?>" disabled>  
          </div>  
      </div>  
      <div class="col-md-6">  
          <div class="form-group">  
              <label for="usernameInput" class="form-control-label">Username</label>  
              <input id="usernameInput" name="username" class="form-control" type="text" value="<?= htmlspecialchars($userData['username'])  ?>" disabled>  
          </div>  
      </div>  
      <!-- Dimunculkan ketika dalam mode edit dan tidak menampilkan data tapi menyimpan -->
      <div class="col-md-6" id="tanggalLahirEditContainer" style="display: none;">  
        <div class="form-group">  
            <label for="tanggalLahirEditInput" class="form-control-label">Tanggal Lahir</label>  
            <input id="tanggalLahirEditInput"   
                   name="tanggal_lahir"   
                   class="form-control"   
                   type="text"   
                   value="<?= htmlspecialchars($userData['tanggal_lahir'])  ?>" 
                   placeholder="Pilih Tanggal Lahir"   
                   readonly>  
        </div>  
    </div>     
    <!-- Tanggal lahir 2 menampilkan value-->
      <div class="col-md-6" id="tgltampilcontainer">  
        <div class="form-group">  
            <label for="tanggalLahirInput" class="form-control-label">Tanggal Lahir</label>  
            <input id="tgltampilinput"   
                   name="tanggal_lahir"   
                   class="form-control"   
                   type="text"   
                   value="<?= htmlspecialchars($userData['tanggal_lahir'])  ?>" 
                   placeholder="Pilih Tanggal Lahir"   
                   readonly   
                   disabled>  
        </div>  
    </div>    
      
      <div id="fotoProfileContainer" class="col-md-12 mt-3" style="display:none;">  
          <div class="form-group">  
              <label for="fotoProfileInput" class="form-control-label">Foto Profile</label>  
              <input id="fotoProfileInput" name="foto_profile" class="form-control" type="file" disabled>  
          </div>  
      </div>  
  </div>  
</form>  

<hr class="horizontal dark">  
              
              
              
              
              
            </div>
          </div>
        </div>
        
      </div>
      <footer class="footer pt-3 mt-4">
        <div class="container-fluid">
          <div class="row flex-column align-items-center text-center">
      
            <div class="col-12">
              <ul class="nav nav-footer justify-content-center">
                <li class="nav-item">
                  <a
                    href="#"
                    class="logo"
                    >MOJU</a
                  >
                </li>
              </ul>
            </div>
            <div class="col-12 mb-lg-0">
              <div class="copyright text-sm text-muted">
                ©
                <script>
                  document.write(new Date().getFullYear());
                </script>
                made with <i class="fa fa-heart"></i> by
                <a
                  href="#"
                  class="font-weight-bold"
                  >Moju Team</a
                >
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
    
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/alert/logout.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
  <!-- Dalam bagian body, tambahkan script untuk sweet alert -->  
<script>  
document.addEventListener('DOMContentLoaded', function() {  
    <?php if(isset($_SESSION['profile_update_success']) && $_SESSION['profile_update_success']): ?>  
        Swal.fire({  
            icon: 'success',  
            title: 'Profil Berhasil Diperbarui',  
            text: 'Data profil Anda telah berhasil diupdate',  
            confirmButtonText: 'OK'  
        });  
        <?php unset($_SESSION['profile_update_success']); ?>  
    <?php endif; ?>  

    <?php if(isset($_SESSION['profile_update_error'])): ?>  
        Swal.fire({  
            icon: 'error',  
            title: 'Gagal Memperbarui Profil',  
            text: '<?php echo $_SESSION['profile_update_error']; ?>',  
            confirmButtonText: 'Coba Lagi'  
        });  
        <?php unset($_SESSION['profile_update_error']); ?>  
    <?php endif; ?>  
});  
</script>


  <!-- Github buttons -->

  <script>  
    document.addEventListener('DOMContentLoaded', function() {  
        const editProfileBtn = document.getElementById('editProfileBtn');  
        const saveProfileBtn = document.getElementById('saveProfileBtn');  
        const cancelEditBtn = document.getElementById('cancelEditBtn');  
        const editProfileContainer = document.getElementById('editProfileContainer');  
        const saveEditContainer = document.getElementById('saveEditContainer');  
        const fotoProfileContainer = document.getElementById('fotoProfileContainer');  
        const profileForm = document.getElementById('profileForm');  
        const tanggalLahirInput2 = document.getElementById('tanggalLahirInput');  
         // Pilih input tanggal lahir yang tidak disabled  

         const tglTampilContainer = document.getElementById('tgltampilcontainer');  
         const tglTampilInput = document.getElementById('tgltampilinput'); 
    // Container edit tanggal lahir  
    const tanggalLahirEditContainer = document.getElementById('tanggalLahirEditContainer');  
    const tanggalLahirEditInput = document.getElementById('tanggalLahirEditInput');  
    
        // Input yang dapat di-edit  
        const inputs = [  
            document.getElementById('namaInput'),  
            document.getElementById('emailInput'),  
            document.getElementById('usernameInput'),  

        ];  
    
        // Fungsi untuk mengaktifkan edit mode  
        editProfileBtn.addEventListener('click', function() {  
            // Sembunyikan tombol Edit Profile  
            editProfileContainer.classList.add('d-none');  
            
            // Tampilkan tombol Save dan Cancel  
            saveEditContainer.classList.remove('d-none');  
            saveEditContainer.classList.add('d-flex');  
    
            // Aktifkan input  
            inputs.forEach(input => {  
                input.disabled = false;  
                input.classList.add('is-filled');  
            });  
                    // Sembunyikan container tampilan  
        if (tglTampilContainer) {  
            tglTampilContainer.style.display = 'none';  
        }  
            if (tanggalLahirEditContainer) {  
            tanggalLahirEditContainer.style.display = 'block';  
            tanggalLahirEditContainer.style.display = 'block';  
        }  
    
            // Tampilkan input foto profil  
            fotoProfileContainer.style.display = 'block';  
            document.getElementById('fotoProfileInput').disabled = false;  
        });  
    
        // Fungsi Cancel  
        cancelEditBtn.addEventListener('click', function() {  
            // Tampilkan kembali tombol Edit Profile  
            editProfileContainer.classList.remove('d-none');  
            
            // Sembunyikan tombol Save dan Cancel  
            saveEditContainer.classList.remove('d-flex');  
            saveEditContainer.classList.add('d-none');  
    
            // Nonaktifkan input  
            inputs.forEach(input => {  
                input.disabled = true;  
                input.classList.remove('is-filled');  
            });  
            if (tglTampilContainer) {  
            tglTampilContainer.style.display = 'block';  
        } 
            if (tanggalLahirEditContainer) {  
            tanggalLahirEditContainer.style.display = 'none';  
        }  
    
            // Sembunyikan input foto profil  
            fotoProfileContainer.style.display = 'none';  
            document.getElementById('fotoProfileInput').disabled = true;  
        });  
    
        // Fungsi Save dengan SweetAlert  
        saveProfileBtn.addEventListener('click', function(e) {  
            e.preventDefault();  
            
            Swal.fire({  
                title: 'Simpan Perubahan?',  
                text: "Anda yakin ingin menyimpan perubahan profil?",  
                icon: 'warning',  
                showCancelButton: true,  
                confirmButtonColor: '#3085d6',  
                cancelButtonColor: '#d33',  
                confirmButtonText: 'Ya, Simpan!',  
                cancelButtonText: 'Batal'  
            }).then((result) => {  
                if (result.isConfirmed) {  
                    // Submit form  
                    profileForm.submit();  
                }  
            });  
        });  
    
        // Inisialisasi Datepicker (menggunakan flatpickr)  
        const datepicker = flatpickr(tanggalLahirEditInput, {  
        dateFormat: "Y-m-d",       // Format untuk disimpan  
        altInput: true,             // Membuat input alternatif  
        altFormat: "d F Y",         // Format tampilan yang lebih mudah dibaca  
        maxDate: "today",           // Tidak bisa memilih tanggal di masa depan  
        locale: {  
            firstDayOfWeek: 1       // Mulai minggu dari hari Senin  
        },  
        onReady: function(selectedDates, dateStr, instance) {  
            // Tambahkan kelas custom jika diperlukan  
            instance.input.classList.add('form-control');  
        },  
        onChange: function(selectedDates, dateStr, instance) {  
            // Update value yang akan dikirim  
            tanggalLahirInput.value = dateStr;  
        }  
    });  

    // Fungsi untuk mengaktifkan edit mode  
       editProfileBtn.addEventListener('click', function() {  
        // Sembunyikan tombol Edit Profile  
        editProfileContainer.classList.add('d-none');  
        
        // Tampilkan tombol Save dan Cancel  
        saveEditContainer.classList.remove('d-none');  
        saveEditContainer.classList.add('d-flex');  

        // Aktifkan input  
        inputs.forEach(input => {  
            input.disabled = false;  
            input.classList.add('is-filled');  
        });  

        // Khusus untuk tanggal lahir  
        tanggalLahirInput.disabled = false;  
        tanggalLahirInput.removeAttribute('readonly');  
        
        // Aktifkan datepicker  
        datepicker.set('enable', true);  

        // Tambahkan event listener untuk membuka datepicker  
        tanggalLahirInput.addEventListener('click', openDatepicker);  

        // Tampilkan input foto profil  
        fotoProfileContainer.style.display = 'block';  
        document.getElementById('fotoProfileInput').disabled = false;  
    });  

    // Modifikasi fungsi Cancel  
    cancelEditBtn.addEventListener('click', function() {  
        // Tampilkan kembali tombol Edit Profile  
        editProfileContainer.classList.remove('d-none');  
        
        // Sembunyikan tombol Save dan Cancel  
        saveEditContainer.classList.remove('d-flex');  
        saveEditContainer.classList.add('d-none');  

        // Nonaktifkan input  
        inputs.forEach(input => {  
            input.disabled = true;  
            input.classList.remove('is-filled');  
        });  

        // Khusus untuk tanggal lahir  
        tanggalLahirInput.disabled = true;  
        tanggalLahirInput.setAttribute('readonly', true);  
        
        // Nonaktifkan datepicker  
        datepicker.set('enable', false);  
        
        // Hapus event listener  
        tanggalLahirInput.removeEventListener('click', openDatepicker);  

        // Sembunyikan input foto profil  
        fotoProfileContainer.style.display = 'none';  
        document.getElementById('fotoProfileInput').disabled = true;  
    });  

    // Fungsi untuk membuka datepicker  
    function openDatepicker() {  
        datepicker.open();  
    }  

    // Nonaktifkan datepicker di awal  
    datepicker.set('enable', false);

    // Nonaktifkan datepicker di awal  
    datepicker.set('enable', false);  
  
    });  
    </script>  
      <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script> 
 
</body>

</html>