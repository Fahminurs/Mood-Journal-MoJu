<?php
require_once __DIR__ . '/../../Auth/auth.php';
AuthHelper::requireRole(['admin']);  
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <link
      rel="apple-touch-icon"
      sizes="76x76"
      href="../assets/img/apple-icon.png"
    />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <title>Akun - Admin</title>
    <!--     Fonts and icons     -->
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"
      rel="stylesheet"
    />
    <!-- Nucleo Icons -->
    <link
      href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css"
      rel="stylesheet"
    />
    <link
      href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css"
      rel="stylesheet"
    />
    <!-- Font Awesome Icons -->
    <script
      src="https://kit.fontawesome.com/42d5adcbca.js"
      crossorigin="anonymous"
    ></script>
    <!-- CSS Files -->
    <link
      id="pagestyle"
      href="../assets/css/argon-dashboard.css?v=2.1.0"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Table -->
        <link rel="stylesheet" href="../assets/css/datatable-custom.css" />
        <!-- DataTables Bootstrap CSS -->
        <link
          href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"
          rel="stylesheet"
        />
    
        <!-- Responsive DataTables CSS -->
        <link
          href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css"
          rel="stylesheet"
        />
        <style>
              .btn-group .btn {
        padding: 10px 20px;
      }
      .input-group-text {  
            background: none;  
            border: none;  
        }  
        .input-icon-container {  
            position: relative;  
        }  
        .input-icon {  
            position: absolute;  
            left: 10px;  
            top: 50px;  
            transform: translateY(-50%);  
            color: #6c757d;  
            z-index: 10;  
        }  
        .form-control-with-icon {  
            padding-left: 35px;  
        }  
        .password-toggle {  
            position: absolute;  
            right: 10px;  
            top: 50px;  
            transform: translateY(-50%);  
            cursor: pointer;  
            color: #6c757d;  
            z-index: 10;  
        }  
        .password-container {  
            position: relative;  
        }  
        </style>
  </head>

  <body class="g-sidenav-show bg-gray-100">
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    <aside
      class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
      id="sidenav-main"
    >
      <div class="sidenav-header">
        <i
          class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
          aria-hidden="true"
          id="iconSidenav"
        ></i>
        <a class="navbar-brand m-0" href="#">
          <span class="logo">MOJU</span>
          <br />
          <span class="logo-bawah">Admin Utama</span>
        </a>
      </div>
      <hr class="horizontal dark mt-0" />
      <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
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
          <li class="nav-item">
            <a class="nav-link active" href="../pages/akun.php">
              <div
                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
              >
                <i class="ni ni-badge text-dark text-sm opacity-10"></i>
              </div>
              <span class="nav-link-text ms-1">Akun</span>
            </a>
          </li>

          <li class="nav-item mt-3">
            <h6
              class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6"
            >
              Account pages
            </h6>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../pages/profile.php">
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
    <main class="main-content position-relative border-radius-lg">
      <!-- Navbar -->
      <nav
        class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl"
        id="navbarBlur"
        data-scroll="false"
      >
        <div class="container-fluid py-1 px-3">
          <nav aria-label="breadcrumb">
            <ol
              class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5"
            >
              <li class="breadcrumb-item text-sm">
                <a class="opacity-5 text-white" href="javascript:;">Pages</a>
              </li>
              <li
                class="breadcrumb-item text-sm text-white active"
                aria-current="page"
              >
                Akun
              </li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">Akun</h6>
          </nav>
          <div
            class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4"
            id="navbar"
          >
            <ul class="navbar-nav justify-content-end">
              <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a
                  href="javascript:;"
                  class="nav-link text-white p-0"
                  id="iconNavbarSidenav"
                >
                  <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                  </div>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="container-fluid py-4">
        <div class="row mt-8">
          <div class="col-12">
            <div class="card mb-4">
              <div class="card-header pb-0 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="header-text">Akun</h6>
                  <button
                    class="btn btn-primary btn-sm btn-centered-icon"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahAkun"
                  >
                    Tambah Akun
                  </button>
                </div>
              </div>
              <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-2">
                  <table
                    id="userTable"
                    class="table table-striped table-bordered dt-responsive nowrap"
                    style="width: 100%"
                  >
                    <thead>
                      <tr>
                      <th>No</th>  
                      <th>Nama</th>  
                      <th>Tanggal Lahir</th>  
                      <th>Role</th>  
                      <th>Dibuat</th>  
                      <th>Aksi</th>
                    
                      </tr>
                    </thead>
          
                  </table>
                </div>
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
                    <a href="#" class="logo">MOJU</a>
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
                  <a href="#" class="font-weight-bold">Moju Team</a>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </main>
 <!-- Modal Tambah Akun -->  
<!-- Modal Tambah Akun -->  
<div class="modal fade" id="modalTambahAkun" tabindex="-1" aria-labelledby="modalTambahAkunLabel" aria-hidden="true">  
    <div class="modal-dialog">  
        <div class="modal-content">  
            <div class="modal-header">  
                <h5 class="modal-title" id="modalTambahAkunLabel">Tambah Akun Baru</h5>  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  
            </div>  
            <div class="modal-body">  
                <form id="formTambahAkun">  
                    <div class="mb-3">  
                        <label for="tambahNamaLengkap" class="form-label">Nama Lengkap</label>  
                        <input type="text" class="form-control" id="tambahNamaLengkap" required>  
                    </div>  
                    <div class="mb-3">  
                        <label for="tambahTanggalLahir" class="form-label">Tanggal Lahir</label>  
                        <input type="date" class="form-control" id="tambahTanggalLahir" required>  
                    </div>  
                    <div class="mb-3">  
                        <label for="tambahRole" class="form-label">Role</label>  
                        <select class="form-select" id="tambahRole" required>  
                            <option value="">Pilih Role</option>  
                            <option value="admin">Admin Utama</option>  
                            <option value="admin artikel">Admin Artikel</option>  
                        </select>  
                    </div>  
                    <div class="mb-3">  
                        <label for="tambahEmail" class="form-label">Email</label>  
                        <input type="email" class="form-control" id="tambahEmail" required>  
                    </div>  
                    <div class="mb-3">  
                        <label for="tambahUsername" class="form-label">Username</label>  
                        <input type="text" class="form-control" id="tambahUsername" required>  
                    </div>  
                    <div class="mb-3 password-container">  
                      <i class="fas fa-lock input-icon"></i>  
                      <label for="password" class="form-label">Password</label>  
                      <input type="password" class="form-control form-control-with-icon" id="tambahPassword" required>  
                      <span class="password-toggle" onclick="togglePassword('password', this)">  
                          <i class="fas fa-eye-slash" id="passwordToggleIcon"></i>  
                      </span>  
                  </div>  
                  <div class="mb-3 password-container">  
                      <i class="fas fa-lock input-icon"></i>  
                      <label for="konfirmasiPassword" class="form-label">Konfirmasi Password</label>  
                      <input type="password" class="form-control form-control-with-icon" id="tambahKonfirmasiPassword" required>  
                      <span class="password-toggle" onclick="togglePassword('konfirmasiPassword', this)">  
                          <i class="fas fa-eye-slash" id="konfirmasiPasswordToggleIcon"></i>  
                      </span>  
                  </div>   
                </form>  
            </div>  
            <div class="modal-footer">  
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>  
                <button type="button" class="btn btn-primary" id="btnSimpanAkun">Simpan</button>  
            </div>  
        </div>  
    </div>  
</div>
<!-- end Modal Tambah Akun -->  
<!-- Modal Update Akun -->  
<div class="modal fade" id="modalUpdateAkun" tabindex="-1" aria-labelledby="modalUpdateAkunLabel" aria-hidden="true">  
    <div class="modal-dialog">  
        <div class="modal-content">  
            <div class="modal-header">  
                <h5 class="modal-title" id="modalUpdateAkunLabel">Update Akun</h5>  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>  
            </div>  
            <div class="modal-body">  
                <form id="formUpdateAkun">  
                    <input type="hidden" id="updateUserId">  
                    <div class="mb-3">  
                        <label for="updateNamaLengkap" class="form-label">Nama Lengkap</label>  
                        <input type="text" class="form-control" id="updateNamaLengkap" required>  
                    </div>  
                    <div class="mb-3">  
                        <label for="updateTanggalLahir" class="form-label">Tanggal Lahir</label>  
                        <input type="date" class="form-control" id="updateTanggalLahir" required>  
                    </div>  
                    <div class="mb-3">  
                        <label for="updateRole" class="form-label">Role</label>  
                        <select class="form-select" id="updateRole" required>  
                            <option value="">Pilih Role</option>  
                            <option value="admin">Admin Utama</option>  
                            <option value="admin artikel">Admin Artikel</option>  
                        </select>  
                    </div>  
                    <div class="mb-3">  
                        <label for="updateEmail" class="form-label">Email</label>  
                        <input type="email" class="form-control" id="updateEmail" required>  
                    </div>  
                    <div class="mb-3">  
                        <label for="updateUsername" class="form-label">Username</label>  
                        <input type="text" class="form-control" id="updateUsername" required>  
                    </div>  
                    <div id="passwordInfo" class="alert alert-info small mt-2">  
                <i class="fas fa-info-circle"></i>   
                Isi password jika ingin mengubah. Kosongkan jika tidak ingin mengganti.  
            </div> 
                    <div class="mb-3 password-container">  
                      <i class="fas fa-lock input-icon"></i>  
                      <label for="password" class="form-label">Password</label>  
                      <input type="password" class="form-control form-control-with-icon" id="password" required>  
                      <span class="password-toggle" onclick="togglePassword('password', this)">  
                          <i class="fas fa-eye-slash" id="passwordToggleIcon"></i>  
                      </span>  
                  </div>  
                  <div class="mb-3 password-container">  
                      <i class="fas fa-lock input-icon"></i>  
                      <label for="konfirmasiPassword" class="form-label">Konfirmasi Password</label>  
                      <input type="password" class="form-control form-control-with-icon" id="konfirmasiPassword" required>  
                      <span class="password-toggle" onclick="togglePassword('konfirmasiPassword', this)">  
                          <i class="fas fa-eye-slash" id="konfirmasiPasswordToggleIcon"></i>  
                      </span>  
                  </div>
              
                </form>  
            </div>  
            <div class="modal-footer">  
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>  
                <button type="button" class="btn btn-primary" id="btnUpdateAkun">Update</button>  
            </div>  
        </div>  
    </div>  
</div>
<!-- end Modal update Akun -->  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

  <!-- Responsive DataTables JS -->
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

  <!-- Font Awesome untuk icon -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />

    <!--   Core JS Files   -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/alert/logout.js"></script>

    <!-- Bootstrap 5 JS dan Popper.js -->  
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>  

    <script>  
        function togglePassword(inputId, toggleElement) {  
            const passwordInput = document.getElementById(inputId);  
            const toggleIcon = toggleElement.querySelector('i');  
            
            if (passwordInput.type === 'password') {  
                passwordInput.type = 'text';  
                toggleIcon.classList.remove('fa-eye-slash');  
                toggleIcon.classList.add('fa-eye');  
            } else {  
                passwordInput.type = 'password';  
                toggleIcon.classList.remove('fa-eye');  
                toggleIcon.classList.add('fa-eye-slash');  
            }  
        }  
    </script> 
    <script>
      $(document).ready(function() {  
    // Tombol Tambah Akun  
  
});
    </script>
 <script>
  $(document).ready(function () {  
    const table = $("#userTable").DataTable({  
        processing: true,  
        serverSide: true,  
        ajax: {  
            url: 'Backend/akun/get_users.php',  
            type: 'GET',  
            error: function(xhr, error, thrown) {  
                Swal.fire({  
                    icon: 'error',  
                    title: 'Gagal memuat data',  
                    text: xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan'  
                });  
            }  
        },  
        columns: [  
            { data: 'no', width: '5%', className: 'text-center' },  
            { data: 'nama' },  
            { data: 'tanggal_lahir' },  
            { data: 'role' },  
            { data: 'created_at' },  
            {   
                data: 'aksi',   
                orderable: false,   
                className: 'text-center',  
                width: '10%'   
            }  
        ],  
        language: {  
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',  
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",  
            infoEmpty: "Tidak ada data yang ditampilkan",  
            infoFiltered: "(disaring dari _MAX_ total entri)",  
            lengthMenu: "_MENU_",  
            search: "",  
            searchPlaceholder: "Cari Data",  
            zeroRecords: "Tidak ada data yang cocok ditemukan",  
            paginate: {  
                first: "<i class='fas fa-angle-double-left'></i>",  
                last: "<i class='fas fa-angle-double-right'></i>",  
                next: "<i class='fas fa-angle-right'></i>",  
                previous: "<i class='fas fa-angle-left'></i>",  
            },  
        },  
        lengthMenu: [  
            [5, 10, 25, 50, 100],  
            [  
                "5 Data",  
                "10 Data",  
                "25 Data",  
                "50 Data",  
                "100 Data",  
            ],  
        ],  
        responsive: true,  
        order: [[1, 'asc']]  
    });  

    // Event handler untuk tombol hapus  
    $('#userTable tbody').on('click', '.delete-btn', function() {  
        const userId = $(this).data('id');  
        
        Swal.fire({  
            title: 'Apakah Anda Yakin?',  
            text: 'Anda tidak akan dapat mengembalikan akun ini!',  
            icon: 'warning',  
            showCancelButton: true,  
            confirmButtonColor: '#3085d6',  
            cancelButtonColor: '#d33',  
            confirmButtonText: 'Ya, Hapus!',  
            cancelButtonText: 'Batal'  
        }).then((result) => {  
            if (result.isConfirmed) {  
                // AJAX request untuk menghapus  
                $.ajax({  
                    url: 'Backend/akun/hapus_user.php',  
                    method: 'POST',  
                    data: { id: userId },  
                    success: function(response) {  
                        Swal.fire('Berhasil!', 'Akun telah dihapus.', 'success');  
                        table.ajax.reload();  
                    },  
                    error: function() {  
                        Swal.fire('Gagal!', 'Tidak dapat menghapus akun.', 'error');  
                    }  
                });  
            }  
        });  
    });  
    $('#tombolTambahAkun').on('click', function() {  
        // Reset form tambah  
        $('#formTambahAkun')[0].reset();  
        $('#modalTambahAkun').modal('show');  
    });  

    // Tombol Simpan Akun Baru  
    $('#btnSimpanAkun').on('click', function() {  
        // Validasi form tambah  
        const data = {  
            namaLengkap: $('#tambahNamaLengkap').val().trim(),  
            tanggalLahir: $('#tambahTanggalLahir').val(),  
            role: $('#tambahRole').val(),  
            email: $('#tambahEmail').val().trim(),  
            username: $('#tambahUsername').val().trim(),  
            password: $('#tambahPassword').val(),  
            konfirmasiPassword: $('#tambahKonfirmasiPassword').val()  
        };  

        // Validasi data  
        const errors = validateForm(data, 'tambah');  
        if (errors.length > 0) {  
            Swal.fire({  
                icon: 'warning',  
                title: 'Validasi Gagal',  
                html: errors.map(error => `<p>${error}</p>`).join('')  
            });  
            return;  
        }  

        // Kirim data via AJAX  
        $.ajax({  
            url: 'Backend/akun/tambah_user.php',  
            method: 'POST',  
            contentType: 'application/json',  
            data: JSON.stringify(data),  
            success: function(response) {  
                const resp = JSON.parse(response);  
                Swal.fire({  
                    icon: 'success',  
                    title: 'Berhasil',  
                    text: resp.message  
                });  
                $('#modalTambahAkun').modal('hide');  
                table.ajax.reload(); // Reload tabel  
                
            },  
            error: function(xhr) {  
                const resp = JSON.parse(xhr.responseText);  
                Swal.fire({  
                    icon: 'error',  
                    title: 'Gagal',  
                    text: resp.message  
                });  
            }  
        });  
    });  

    // Tombol Edit Akun  
    $('#userTable tbody').on('click', '.edit-btn', function() {  
        const userId = $(this).data('id');  

        // Ambil detail user via AJAX  
        $.ajax({  
            url: 'Backend/akun/get_user_detail.php',  
            method: 'GET',  
            data: { id: userId },  
            success: function(response) {  
                const user = JSON.parse(response);  

                // Isi form update  
                $('#updateUserId').val(userId);  
                $('#updateNamaLengkap').val(user.nama);  
                $('#updateTanggalLahir').val(user.tanggal_lahir);  
                $('#updateRole').val(user.role === 'admin' ? 'admin ' : 'admin artikel');  
                $('#updateEmail').val(user.email);  
                $('#updateUsername').val(user.username);  

                // Reset password  
                $('#updatePassword, #updateKonfirmasiPassword').val('');  

                // Tampilkan modal  
                $('#modalUpdateAkun').modal('show');  
            },  
            error: function(xhr) {  
                Swal.fire({  
                    icon: 'error',  
                    title: 'Gagal',  
                    text: 'Tidak dapat mengambil data user'  
                });  
            }  
        });  
    });  

    // Tombol Update Akun  
    $('#btnUpdateAkun').on('click', function() {  
        // Ambil data update  
        const data = {  
            userId: $('#updateUserId').val(),  
            namaLengkap: $('#updateNamaLengkap').val().trim(),  
            tanggalLahir: $('#updateTanggalLahir').val(),  
            role: $('#updateRole').val(),  
            email: $('#updateEmail').val().trim(),  
            username: $('#updateUsername').val().trim(),  
            password: $('#updatePassword').val(),  
            konfirmasiPassword: $('#updateKonfirmasiPassword').val()  
        };  

        // Validasi data  
        const errors = validateForm(data, 'update');  
        if (errors.length > 0) {  
            Swal.fire({  
                icon: 'warning',  
                title: 'Validasi Gagal',  
                html: errors.map(error => `<p>${error}</p>`).join('')  
            });  
            return;  
        }  

        // Kirim data via AJAX  
        $.ajax({  
            url: 'Backend/akun/update_user.php',  
            method: 'POST',  
            contentType: 'application/json',  
            data: JSON.stringify(data),  
            success: function(response) {  
                const resp = JSON.parse(response);  
                Swal.fire({  
                    icon: 'success',  
                    title: 'Berhasil',  
                    text: resp.message  
                });  
                
                $('#modalUpdateAkun').modal('hide');  
                table.ajax.reload(); // Reload tabel  
            },  
            error: function(xhr) {  
                const resp = JSON.parse(xhr.responseText);  
                Swal.fire({  
                    icon: 'error',  
                    title: 'Gagal',  
                    text: resp.message  
                });  
            }  
        });  
    });  

    // Fungsi Validasi Form  
    function validateForm(data, mode) {  
        const errors = [];  

        // Validasi nama lengkap  
        if (!data.namaLengkap) errors.push('Nama Lengkap harus diisi');  

        // Validasi tanggal lahir  
        if (!data.tanggalLahir) errors.push('Tanggal Lahir harus diisi');  

        // Validasi role  
        if (!data.role) errors.push('Role harus dipilih');  

        // Validasi email  
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;  
        if (!data.email) {  
            errors.push('Email harus diisi');  
        } else if (!emailRegex.test(data.email)) {  
            errors.push('Format email tidak valid');  
        }  

        // Validasi username  
        if (!data.username) errors.push('Username harus diisi');  

        // Validasi password untuk mode tambah  
        if (mode === 'tambah') {  
            if (!data.password) errors.push('Password harus diisi');  
            if (!data.konfirmasiPassword) errors.push('Konfirmasi Password harus diisi');  
            if (data.password !== data.konfirmasiPassword) {  
                errors.push('Password dan Konfirmasi Password tidak cocok');  
            }  
        }  

        // Validasi password untuk mode update (jika diisi)  
        if (mode === 'update' && data.password) {  
            if (!data.konfirmasiPassword) {  
                errors.push('Konfirmasi Password harus diisi');  
            }  
            if (data.password !== data.konfirmasiPassword) {  
                errors.push('Password dan Konfirmasi Password tidak cocok');  
            }  
        }  

        return errors;  
    } 
});
 </script>
    <script>
      var win = navigator.platform.indexOf("Win") > -1;
      if (win && document.querySelector("#sidenav-scrollbar")) {
        var options = {
          damping: "0.5",
        };
        Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
      }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
  </body>
</html>
