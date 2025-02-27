<?php  
require_once __DIR__ . '/../../Auth/auth.php';
AuthHelper::requireRole(['admin', 'admin artikel']); 
$currentUser = AuthHelper::getCurrentUser();  
$currentRole = $currentUser['role'] ?? '';  
?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <title>Artikel - Admin</title>
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
    <!-- Summernote CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css"
      rel="stylesheet"
    />

    <!-- SweetAlert2 CSS -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
    />
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
      .header-text {
        position: relative;
        top: -5px;
        font-size: 20px;
        font-weight: 800;
      }
      .btn-edukasi {
        background-color: #be5ee4;
        color: white;
      }

      .btn-edukasi {
        background-color: #be5ee4 !important;
        color: white !important;
        border-color: #be5ee4 !important;
      }

      .btn-edukasi:hover,
      .btn-edukasi:focus,
      .btn-edukasi:active,
      .btn-edukasi:focus-visible {
        background-color: #be5ee4 !important;
        border-color: #be5ee4 !important;
        outline: none !important;
        box-shadow: none !important;
      }
      .btn-edukasi:active,
      .btn-edukasi:focus-visible {
        color: black !important;
      }
      #usertable-edukasi th:last-child,
      #usertable-edukasi td:last-child {
        text-align: center;
      }
      #usertable-edukasi th:first-child,
      #usertable-edukasi td:first-child {
        text-align: center;
      }
      .btn-group .btn {
        padding: 10px 20px;
      }
    </style>
  </head>

  <body class="g-sidenav-show bg-gray-100">
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
 

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
                    <a class="nav-link active" href="../pages/artikel.php">  
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
                    <a class="nav-link active" href="../pages/artikel.php">  
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
                Artikel
              </li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">Artikel</h6>
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
        <div class="row">
          <div class="col-12">
            <div class="card mb-4">
              <div class="card-header pb-0 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="header-text">Artikel</h6>
                  <button   
    type="button"   
    class="btn btn-primary btn-sm mb-3"   
    id="tambahArtikelBtn"   
    data-bs-toggle="modal"   
    data-bs-target="#tambahArtikelModal"  
>  
    <i class="fas fa-plus"></i> Tambah Artikel  
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
                        <th>Penulis</th>
                        <th>Judul</th>
                        <th>Konten</th>
                        <th>dibuat</th>
                        <th>diperbaharui</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
              
                      <!-- Tambahkan baris lain sesuai kebutuhan -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card mb-4">
              <div class="card-header pb-0 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="header-text">Edukasi</h6>
                  <button
                    class="btn btn-edukasi btn-sm btn-centered-icon"
                    data-bs-toggle="modal"
                    id="tambahEdukasiBtn"
                    data-bs-target="#tambahEdukasiModal"
                  >
                    Tambah Edukasi
                  </button>
                </div>
              </div>
              <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-2">
                  <table
                    id="userTable-edukasi"
                    class="table table-striped table-bordered dt-responsive nowrap"
                    style="width: 100%"
                  >
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Penulis</th>
                        <th>Judul</th>
                        <th>Konten</th>
                        <th>dibuat</th>
                        <th>diperbaharui</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>2</td>
                        <td>Jane Smith</td>
                        <td>blblblblbl</td>
                        <td>blblblblbl</td>
                        <td>082345678901</td>
                        <td>2000</td>
                        <td>
                          <div class="btn-group" role="group">
                            <button
                              class="btn btn-sm btn-info edit-btn"
                              title="Edit"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              class="btn btn-sm btn-danger delete-btn"
                              title="Hapus"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                            <button
                              class="btn btn-sm btn-dark view-btn"
                              title="Lihat Detail"
                            >
                              <i class="fas fa-eye"></i>
                            </button>
                          </div>
                        </td>
                      </tr>

                      <!-- Tambahkan baris lain sesuai kebutuhan -->
                    </tbody>
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

    <!-- Modal Tambah Artikel -->
    <div
      class="modal fade"
      id="tambahArtikelModal"
      tabindex="-1"
      aria-labelledby="tambahArtikelModalLabel"
      aria-hidden="true"
    >
      <div
        class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
      >
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5
              class="modal-title"
              id="tambahArtikelModalLabel"
              style="color: rgb(255, 255, 255)"
            >
              Tambah Artikel Baru
            </h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form id="formTambahArtikel">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="author" class="form-label">Penulis</label>
                  <input
                    type="text"
                    class="form-control"
                    id="author"
                    name="author"
                    placeholder="Masukkan nama author"
                    required
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="judul" class="form-label">Judul Artikel</label>
                  <input
                    type="text"
                    class="form-control"
                    id="judul"
                    name="judul"
                    placeholder="Masukkan judul artikel"
                    required
                  />
                </div>
              </div>
              <div class="mb-3">
                <label for="konten" class="form-label">Konten Artikel</label>
                <textarea id="konten" name="konten"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary btn-sm"
              data-bs-dismiss="modal"
            >
              Batal
            </button>
            <button
              type="button"
              class="btn btn-primary btn-sm"
              id="simpanArtikel"
            >
              Simpan Artikel
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Tambah Artikel -->
    <!-- Modal Tambah Edukasi -->
    <div
      class="modal fade"
      id="tambahEdukasiModal"
      tabindex="-1"
      aria-labelledby="tambahEdukasiModalLabel"
      aria-hidden="true"
    >
      <div
        class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
      >
        <div class="modal-content">
          <div
            class="modal-header bg-primary text-white"
            style="background-color: #be5ee4 !important"
          >
            <h5
              class="modal-title"
              id="tambahEdukasiModalLabel"
              style="color: rgb(255, 255, 255)"
            >
              Tambah Edukasi Baru
            </h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form id="formTambahEdukasi">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="authorEdukasi" class="form-label">Penulis</label>
                  <input
                    type="text"
                    class="form-control"
                    id="authorEdukasi"
                    name="author"
                    placeholder="Masukkan nama author"
                    required
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="judulEdukasi" class="form-label"
                    >Judul Edukasi</label
                  >
                  <input
                    type="text"
                    class="form-control"
                    id="judulEdukasi"
                    name="judul"
                    placeholder="Masukkan judul edukasi"
                    required
                  />
                </div>
              </div>
              <div class="mb-3">
                <label for="kontenEdukasi" class="form-label"
                  >Konten Edukasi</label
                >
                <textarea id="kontenEdukasi" name="konten"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary btn-sm"
              data-bs-dismiss="modal"
            >
              Batal
            </button>
            <button
              type="button"
              class="btn btn-edukasi btn-sm"
              id="simpanEdukasi"
            >
              Simpan Edukasi
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Tambah Artikel -->

    <!--   Core JS Files   -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

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
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/alert/logout.js"></script>
    <script src="Backend/artikel/Artikel.js"></script>
    <script src="Backend/edukasi/Edukasi.js"></script>
 
    <script>
      var win = navigator.platform.indexOf("Win") > -1;
      if (win && document.querySelector("#sidenav-scrollbar")) {
        var options = {
          damping: "0.5",
        };
        Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
      }
    </script>
<script>
  $(document).ready(function () {  
    // Inisialisasi Summernote  
    $("#konten").summernote({  
        placeholder: "Tulis konten artikel di sini...",  
        tabsize: 2,  
        height: 400,  
        toolbar: [  
            ["style", ["style"]],  
            ["font", ["bold", "italic", "underline", "clear"]],  
            ["color", ["color"]],  
            ["para", ["ul", "ol", "paragraph"]],  
            ["table", ["table"]],  
            ["insert", ["link", "picture", "video"]],  
            ["view", ["fullscreen", "codeview"]],  
        ],  
    });  

    // Fungsi Validasi  
    function validateForm() {  
        let author = $("#author").val().trim();  
        let judul = $("#judul").val().trim();  
        let konten = $("#konten").summernote("code");  
        let plainTextKonten = konten.replace(/<\/?[^>]+(>|$)/g, "").trim();  

        let errors = [];  

        if (author === "") errors.push("Penulis harus diisi");  
        if (judul === "") errors.push("Judul harus diisi");  
        if (plainTextKonten === "") errors.push("Konten harus diisi");  

        return {  
            isValid: errors.length === 0,  
            errors: errors  
        };  
    }  

    // Tombol Simpan Artikel  
    $("#simpanArtikel").on("click", function () {  
        // Validasi Form  
        let validationResult = validateForm();  

        // Tampilkan pesan error jika validasi gagal  
        if (!validationResult.isValid) {  
            Swal.fire({  
                icon: "warning",  
                title: "Validasi Gagal",  
                html: validationResult.errors.map(error => `<p>${error}</p>`).join(''),  
            });  
            return;  
        }  

        // Ambil value dari form  
        let author = $("#author").val().trim();  
        let judul = $("#judul").val().trim();  
        let konten = $("#konten").summernote("code");  

        // Konfirmasi Simpan  
        Swal.fire({  
            title: "Simpan Artikel?",  
            text: "Pastikan semua informasi sudah benar",  
            icon: "question",  
            showCancelButton: true,  
            confirmButtonColor: "#3085d6",  
            cancelButtonColor: "#d33",  
            confirmButtonText: "Ya, Simpan!",  
        }).then((result) => {  
            if (result.isConfirmed) {  
                // Disable tombol selama proses  
                $("#simpanArtikel").prop('disabled', true).html('Menyimpan...');  

                // Kirim data via AJAX  
                $.ajax({  
                    url: "Backend/artikel/proses_artikel.php",  
                    method: "POST",  
                    data: {  
                        author: author,  
                        judul: judul,  
                        content: konten,  
                        jenis: 'artikel',  
                    },  
                    dataType: "json",  
                    success: function (response) {  
                        Swal.fire({  
                            icon: "success",  
                            title: "Berhasil!",  
                            text: "Artikel telah disimpan",  
                            timer: 2000,  
                            showConfirmButton: false  
                        }).then(() => {  
                            // Reset form  
                            $("#formTambahArtikel")[0].reset();  
                            $("#konten").summernote("code", "");  
                            $("#tambahArtikelModal").modal("hide");  
                            
                            // Reload tabel jika ada  
                            if ($.fn.DataTable.isDataTable('#userTable')) {  
                                $('#userTable').DataTable().ajax.reload(null, false);  
                            }  
                        });  
                    },  
                    error: function (xhr, status, error) {  
                        console.error("Full error response:", xhr.responseText);  
                        
                        let errorMessage = "Terjadi kesalahan";  
                        try {  
                            let errorResponse = JSON.parse(xhr.responseText);  
                            errorMessage = errorResponse.message || errorMessage;  
                        } catch (e) {  
                            errorMessage = xhr.responseText || "Terjadi kesalahan";  
                        }  

                        // Log error ke console  
                        console.error("Error Details:", {  
                            status: status,  
                            error: error,  
                            responseText: xhr.responseText  
                        });  

                        Swal.fire({  
                            icon: "error",  
                            title: "Kesalahan",  
                            text: errorMessage,  
                        });  
                    },  
                    complete: function() {  
                        // Kembalikan tombol ke kondisi semula  
                        $("#simpanArtikel").prop('disabled', false).html('Simpan');  
                    }  
                });  
            }  
        });  
    });  
});
</script>
  

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
  </body>
</html>
