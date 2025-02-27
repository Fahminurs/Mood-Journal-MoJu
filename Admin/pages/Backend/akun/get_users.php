<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php'; 

// Pastikan hanya admin utama yang bisa mengakses  
AuthHelper::requireRole(['admin']);  

// Parameter dari DataTables  
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;  
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;  
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;  

// Batasi maksimal 100 data  
$length = min($length, 100);  

$searchValue = $_GET['search']['value'] ?? '';  
$orderColumn = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;  
$orderDir = isset($_GET['order'][0]['dir']) && in_array(strtoupper($_GET['order'][0]['dir']), ['ASC', 'DESC'])   
    ? strtoupper($_GET['order'][0]['dir'])   
    : 'ASC';  

// Kolom yang bisa diurutkan  
$columns = ['id_user', 'nama','email', 'tanggal_lahir', 'role', 'created_at'];  

try {  
    // Ambil ID user saat ini  
    $currentUserId = $_SESSION['user_id'] ?? null;  

    // Validasi kolom pengurutan dengan escape karakter  
    $orderColumnName = isset($columns[$orderColumn])   
        ? "`" . $columns[$orderColumn] . "`"   
        : "`nama`";  

    // Query untuk menghitung total data  
    $totalQuery = "SELECT COUNT(*) as total FROM user WHERE role IN ('admin', 'admin artikel')";  
    $totalResult = selectQuery($totalQuery)[0]['total'];  

    // Siapkan query dengan parameter yang aman  
    $query = "SELECT id_user, nama,email , tanggal_lahir, role, created_at  
              FROM user   
              WHERE (nama LIKE ? OR role LIKE ?)   
              AND role IN ('admin', 'admin artikel')  
              ORDER BY " . $orderColumnName . " " . $orderDir . "  
              LIMIT ? OFFSET ?";  

    // Siapkan parameter dengan casting eksplisit  
    $params = [  
        "%{$searchValue}%",   // Search nama  
        "%{$searchValue}%",   // Search role  
        (int)$length,         // Limit dengan casting integer  
        (int)$start           // Offset dengan casting integer  
    ];  

    // Eksekusi query dengan parameter  
    $data = selectQuery($query, $params, 'Fetch admin users with pagination');  

    // Hitung total yang difilter  
    $filterQuery = "SELECT COUNT(*) as total   
                    FROM user   
                    WHERE (nama LIKE ? OR role LIKE ?)   
                    AND role IN ('admin', 'admin artikel')";  
    $filterParams = [  
        "%{$searchValue}%",  
        "%{$searchValue}%"  
    ];  
    $filterTotal = selectQuery($filterQuery, $filterParams, 'Count filtered admin users')[0]['total'];  

    // Siapkan response untuk DataTables  
    $response = [  
        'draw' => $draw,  
        'recordsTotal' => $totalResult,  
        'recordsFiltered' => $filterTotal,  
        'data' => []  
    ];  

    // Format data untuk DataTables  
    $nomor = $start + 1;  
    foreach ($data as $row) {  
        // Tentukan apakah tombol hapus harus ditampilkan  
        $canDelete = $row['id_user'] != $currentUserId;  

        $aksiButtons = $canDelete   
            ? "  
                <div class='btn-group' role='group'>  
                    <button class='btn btn-info btn-sm edit-btn' data-id='{$row['id_user']}'>  
                        <i class='fas fa-edit'></i>  
                    </button>  
                    <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id_user']}'>  
                        <i class='fas fa-trash'></i>  
                    </button>  
                </div>  
            "   
            : "  
               
                    <p >Pengguna Saat Ini</p>
                
            ";  

        $response['data'][] = [  
            'no' => $nomor++,  
            'nama' => htmlspecialchars($row['nama']),  
            'email' => htmlspecialchars($row['email']),  
            'tanggal_lahir' => $row['tanggal_lahir'] ? date('d M Y', strtotime($row['tanggal_lahir'])) : '-',  
            'role' => htmlspecialchars($row['role']),  
            'created_at' => $row['created_at'] ? date('d M Y H:i', strtotime($row['created_at'])) : '-',  
            'aksi' => $aksiButtons  
        ];  
    }  

    echo json_encode($response);  

} catch (Exception $e) {  
    // Tangani error  
    $logger = Logger::getInstance();  
    $logger->logError('USER_QUERY_ERROR', 'Failed to fetch users', $e);  
    
    http_response_code(500);  
    echo json_encode([  
        'error' => 'Terjadi kesalahan saat memproses data',  
        'details' => $e->getMessage()  
    ]);  
}