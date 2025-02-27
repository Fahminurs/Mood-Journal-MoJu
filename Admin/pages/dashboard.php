<?php  
// Pastikan hanya admin yang bisa mengakses  
require_once __DIR__ . '/../../Auth/auth.php';

// Cek login dan role admin  
AuthHelper::requireRole(['admin']);  

// Dapatkan informasi pengguna yang login  
$user = AuthHelper::getCurrentUser();  

// Fungsi untuk mendapatkan statistik  
function getStatistics() {  
    try {  
        // Total Pengguna Moju (user)  
        $totalUsers = selectQuery("SELECT COUNT(*) as count FROM user WHERE role = 'user'")[0]['count'];  

        // Edukasi Moju (artikel dengan jenis edukasi)  
        $totalEdukasi = selectQuery("SELECT COUNT(*) as count FROM artikel WHERE jenis = 'edukasi'")[0]['count'];  

        // Admin Moju  
        $totalAdmins = selectQuery("SELECT COUNT(*) as count FROM user WHERE role = 'admin'")[0]['count'];  

        // Artikel Moju  
        $totalArtikel = selectQuery("SELECT COUNT(*) as count FROM artikel WHERE jenis = 'artikel'")[0]['count'];  

        return [  
            'totalUsers' => $totalUsers,  
            'totalEdukasi' => $totalEdukasi,  
            'totalAdmins' => $totalAdmins,  
            'totalArtikel' => $totalArtikel  
        ];  
    } catch (Exception $e) {  
        // Log error  
        return [  
            'totalUsers' => 0,  
            'totalEdukasi' => 0,  
            'totalAdmins' => 0,  
            'totalArtikel' => 0  
        ];  
    }  
}  

// Ambil statistik  
$stats = getStatistics();  

// Fungsi untuk mendapatkan data login tracker mingguan  
function getLoginTrackerMingguan() {  
    $query = "  
        SELECT   
            DAYNAME(login_at) as hari,   
            COUNT(*) as total_login   
        FROM islogin i  
        JOIN user u ON i.id_user = u.id_user  
        WHERE login_at >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)  
        GROUP BY DAYNAME(login_at)  
        ORDER BY FIELD(hari, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')  
    ";  
    
    return selectQuery($query);  
}  

// Fungsi untuk mendapatkan data login tracker bulanan  
function getLoginTrackerBulanan() {  
    $query = "  
        SELECT   
            MONTHNAME(login_at) as bulan,   
            COUNT(*) as total_login   
        FROM islogin i  
        JOIN user u ON i.id_user = u.id_user  
        WHERE login_at >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)  
        GROUP BY MONTHNAME(login_at)  
        ORDER BY MONTH(login_at)  
    ";  
    
    return selectQuery($query);  
}  

// Fungsi untuk mendapatkan data user mingguan  
function getUserMingguan() {  
    $query = "  
        SELECT   
            DAYNAME(created_at) as hari,   
            COUNT(*) as total_user   
        FROM user  
        WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)  
        AND role = 'user'  
        GROUP BY DAYNAME(created_at)  
        ORDER BY FIELD(hari, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')  
    ";  
    
    return selectQuery($query);  
}  

// Fungsi untuk mendapatkan data user bulanan  
function getUserBulanan() {  
    $query = "  
        SELECT   
            MONTHNAME(created_at) as bulan,   
            COUNT(*) as total_user   
        FROM user  
        WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)  
        AND role = 'user'  
        GROUP BY MONTHNAME(created_at)  
        ORDER BY MONTH(created_at)  
    ";  
    
    return selectQuery($query);  
}  
?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <link rel="icon" type="image/png" href="../assets/img/Moju-icon.png" />
    <title>Dashboard - Admin</title>
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
      href="../assets/css/argon-dashboard.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
            /* Responsiveness dan Custom Styling */  
            @media (max-width: 576px) {  
            .chart-container {  
                height: 250px !important;  
            }  
        }  
        
        @media (min-width: 577px) and (max-width: 992px) {  
            .chart-container {  
                height: 300px !important;  
            }  
        }  
        
        @media (min-width: 993px) {  
            .chart-container {  
                height: 400px !important;  
            }  
        }  
        
        /* Efek hover pada card */  
        .card {  
            transition: all 0.3s ease;  
        }  
        
        .card:hover {  
            transform: translateY(-10px);  
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);  
        }  
    </style>
  </head>

  <body class="g-sidenav-show bg-gray-100">
    <div class="min-height-300 bg-moju position-absolute w-100"></div>
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
        <a
          class="navbar-brand m-0"
          href="#"
        >
          <span class="logo">MOJU</span>
          <br />
          <span class="logo-bawah">Admin Utama </span>
        </a>
      </div>
      <hr class="horizontal dark mt-0" />
      <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" href="../pages/dashboard.php">
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
            <a class="nav-link" href="../pages/akun.php">
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
                Dashboard
              </li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">Dashboard</h6>
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
        <div class="row justify-content-center align-items-center mt-4">  
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">  
                <div class="card">  
                    <div class="card-body p-3">  
                        <div class="row">  
                            <div class="col-8">  
                                <div class="numbers">  
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">  
                                        Pengguna Moju  
                                    </p>  
                                    <h5 class="font-weight-bolder"><?php echo $stats['totalUsers']; ?></h5>  
                                </div>  
                            </div>  
                            <div class="col-4 text-end">  
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">  
                                    <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>  
                                </div>  
                            </div>  
                        </div>  
                    </div>  
                </div>  
            </div>  
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">  
                <div class="card">  
                    <div class="card-body p-3">  
                        <div class="row">  
                            <div class="col-8">  
                                <div class="numbers">  
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">  
                                        Edukasi Moju  
                                    </p>  
                                    <h5 class="font-weight-bolder"><?php echo $stats['totalEdukasi']; ?></h5>  
                                </div>  
                            </div>  
                            <div class="col-4 text-end">  
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">  
                                    <i class="ni ni-hat-3 text-lg opacity-10" aria-hidden="true"></i>  
                                </div>  
                            </div>  
                        </div>  
                    </div>  
                </div>  
            </div>  
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">  
                <div class="card">  
                    <div class="card-body p-3">  
                        <div class="row">  
                            <div class="col-8">  
                                <div class="numbers">  
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">  
                                        Admin Moju  
                                    </p>  
                                    <h5 class="font-weight-bolder"><?php echo $stats['totalAdmins']; ?></h5>  
                                </div>  
                            </div>  
                            <div class="col-4 text-end">  
                                <div class="icon icon-shape text-center rounded-circle" style="background-color: aqua">  
                                    <i class="ni ni-badge text-lg opacity-10" aria-hidden="true"></i>  
                                </div>  
                            </div>  
                        </div>  
                    </div>  
                </div>  
            </div>  
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">  
                <div class="card">  
                    <div class="card-body p-3">  
                        <div class="row">  
                            <div class="col-8">  
                                <div class="numbers">  
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">  
                                        Artikel Moju  
                                    </p>  
                                    <h5 class="font-weight-bolder"><?php echo $stats['totalArtikel']; ?></h5>  
                                </div>  
                            </div>  
                            <div class="col-4 text-end">  
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">  
                                    <i class="ni ni-collection text-lg opacity-10" aria-hidden="true"></i>  
                                </div>  
                            </div>  
                        </div>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div> 


    <div class="p-2">
     <!-- Login Tracker Moju Mingguan -->  
     <div class="row mt-4">  
      <div class="mb-lg-0">  
          <div class="card z-index-2 h-100">  
              <div class="card-header pb-0 pt-3 bg-transparent">  
                  <h6 class="text-capitalize">Login Tracker Moju Mingguan</h6>  
              </div>  
              <div class="card-body p-3">  
                  <div class="chart">  
                      <canvas id="chartLoginMingguan" class="chart-canvas" height="300"></canvas>  
                  </div>  
              </div>  
          </div>  
      </div>  
  </div>  

  <!-- Login Tracker Moju Bulanan -->  
  <div class="row mt-4">  
      <div class="mb-lg-0">  
          <div class="card z-index-2 h-100">  
              <div class="card-header pb-0 pt-3 bg-transparent">  
                  <h6 class="text-capitalize">Login Tracker Moju Bulanan</h6>  
              </div>  
              <div class="card-body p-3">  
                  <div class="chart">  
                      <canvas id="chartLoginBulanan" class="chart-canvas" height="300"></canvas>  
                  </div>  
              </div>  
          </div>  
      </div>  
  </div>  

  <!-- User Moju Mingguan -->  
  <div class="row mt-4">  
      <div class="mb-lg-0 mb-4">  
          <div class="card z-index-2 h-100">  
              <div class="card-header pb-0 pt-3 bg-transparent">  
                  <h6 class="text-capitalize">User yang menggunakan Moju Mingguan</h6>  
              </div>  
              <div class="card-body p-3">  
                  <div class="chart">  
                      <canvas id="chartUserMingguan" class="chart-canvas" height="300"></canvas>  
                  </div>  
              </div>  
          </div>  
      </div>  
  </div>  

  <!-- User Moju Bulanan -->  
  <div class="row mt-4">  
      <div class="mb-lg-0 mb-4">  
          <div class="card z-index-2 h-100">  
              <div class="card-header pb-0 pt-3 bg-transparent">  
                  <h6 class="text-capitalize">User yang menggunakan Moju Bulanan</h6>  
              </div>  
              <div class="card-body p-3">  
                  <div class="chart">  
                      <canvas id="chartUserBulanan" class="chart-canvas" height="300"></canvas>  
                  </div>  
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
    </main>

    <!--   Core JS Files   -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/chartjs.min.js"></script>
    <script src="../assets/js/alert/logout.js"></script>
    <script>  
    // Konfigurasi warna dan gaya chart  
    const CHART_COLORS = {  
        loginMingguan: {  
            border: 'rgb(75, 192, 192)',  
            background: 'rgba(75, 192, 192, 0.2)'  
        },  
        loginBulanan: {  
            border: 'rgb(255, 99, 132)',  
            background: 'rgba(255, 99, 132, 0.2)'  
        },  
        userMingguan: {  
            border: 'rgb(54, 162, 235)',  
            background: 'rgba(54, 162, 235, 0.2)'  
        },  
        userBulanan: {  
            border: 'rgb(255, 206, 86)',  
            background: 'rgba(255, 206, 86, 0.2)'  
        }  
    };  

    // Fungsi umum untuk membuat chart  
    function createResponsiveLineChart(canvasId, labels, data, colorConfig) {  
        const ctx = document.getElementById(canvasId).getContext('2d');  
        
        // Gradien background  
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);  
        gradient.addColorStop(0, colorConfig.background);  
        gradient.addColorStop(1, 'transparent');  

        return new Chart(ctx, {  
            type: 'line',  
            data: {  
                labels: labels,  
                datasets: [{  
                    label: data.label,  
                    data: data.values,  
                    borderColor: colorConfig.border,  
                    backgroundColor: gradient,  
                    borderWidth: 2,  
                    tension: 0.4,  
                    fill: true,  
                    pointBackgroundColor: colorConfig.border,  
                    pointRadius: 5,  
                    pointHoverRadius: 7  
                }]  
            },  
            options: {  
                responsive: true,  
                maintainAspectRatio: false,  
                animation: {  
                    duration: 2000,  
                    easing: 'easeOutQuart'  
                },  
                scales: {  
                    y: {  
                        beginAtZero: true,  
                        grid: {  
                            color: 'rgba(0,0,0,0.05)'  
                        }  
                    },  
                    x: {  
                        grid: {  
                            display: false  
                        }  
                    }  
                },  
                plugins: {  
                    legend: {  
                        display: true,  
                        position: 'top'  
                    },  
                    tooltip: {  
                        mode: 'index',  
                        intersect: false  
                    }  
                }  
            }  
        });  
    }  

    document.addEventListener('DOMContentLoaded', () => {  
        // Login Tracker Mingguan  
        const loginMingguanData = <?php   
            $loginMingguan = getLoginTrackerMingguan();  
            echo json_encode(array_column($loginMingguan, 'total_login'));   
        ?>;  
        const loginMingguanLabels = <?php   
            echo json_encode(array_column($loginMingguan, 'hari'));   
        ?>;  

        // Login Tracker Bulanan  
        const loginBulananData = <?php   
            $loginBulanan = getLoginTrackerBulanan();  
            echo json_encode(array_column($loginBulanan, 'total_login'));   
        ?>;  
        const loginBulananLabels = <?php   
            echo json_encode(array_column($loginBulanan, 'bulan'));   
        ?>;  

        // User Mingguan  
        const userMingguanData = <?php   
            $userMingguan = getUserMingguan();  
            echo json_encode(array_column($userMingguan, 'total_user'));   
        ?>;  
        const userMingguanLabels = <?php   
            echo json_encode(array_column($userMingguan, 'hari'));   
        ?>;  

        // User Bulanan  
        const userBulananData = <?php   
            $userBulanan = getUserBulanan();  
            echo json_encode(array_column($userBulanan, 'total_user'));   
        ?>;  
        const userBulananLabels = <?php   
            echo json_encode(array_column($userBulanan, 'bulan'));   
        ?>;  

        // Inisialisasi Chart  
        // Login Tracker Mingguan  
        createResponsiveLineChart(  
            'chartLoginMingguan',   
            loginMingguanLabels.length > 0 ? loginMingguanLabels : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],  
            {  
                label: 'Login Mingguan',  
                values: loginMingguanData.length > 0 ? loginMingguanData : [65, 59, 80, 81, 56, 55, 40]  
            },  
            CHART_COLORS.loginMingguan  
        );  

        // Login Tracker Bulanan  
        createResponsiveLineChart(  
            'chartLoginBulanan',   
            loginBulananLabels.length > 0 ? loginBulananLabels : [  
                'Januari', 'Februari', 'Maret', 'April',   
                'Mei', 'Juni', 'Juli', 'Agustus',   
                'September', 'Oktober', 'November', 'Desember'  
            ],  
            {  
                label: 'Login Bulanan',  
                values: loginBulananData.length > 0 ? loginBulananData : [120, 190, 150, 200,120, 190, 150, 200,120, 190, 150, 200]  
            },  
            CHART_COLORS.loginBulanan  
        );  

        // User Moju Mingguan  
        createResponsiveLineChart(  
            'chartUserMingguan',   
            userMingguanLabels.length > 0 ? userMingguanLabels : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],  
            {  
                label: 'User Aktif',  
                values: userMingguanData.length > 0 ? userMingguanData : [45, 52, 60, 55, 48, 50, 42]  
            },  
            CHART_COLORS.userMingguan  
        );  

        // User Moju Bulanan  
        createResponsiveLineChart(  
            'chartUserBulanan',   
            userBulananLabels.length > 0 ? userBulananLabels : [  
                'Januari', 'Februari', 'Maret', 'April',   
                'Mei', 'Juni', 'Juli', 'Agustus',   
                'September', 'Oktober', 'November', 'Desember'  
            ],  
            {  
                label: 'Total User',  
                values: userBulananData.length > 0 ? userBulananData : [100, 150, 130, 180,100, 150, 130, 180,100, 150, 130, 180]  
            },  
            CHART_COLORS.userBulanan  
        );  
    });  

    // Handler resize (Opsional)  
    window.addEventListener('resize', () => {  
        // Implementasi logika resize jika diperlukan  
        // Contoh: memanggil ulang chart dengan ukuran baru  
        // Namun Chart.js sudah responsive secara default  
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
