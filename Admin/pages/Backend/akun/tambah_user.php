<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Pastikan hanya admin yang bisa menambah user  
AuthHelper::requireRole(['admin']);  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $data = json_decode(file_get_contents('php://input'), true);  

    try {  
        // Validasi input  
        $requiredFields = [  
            'namaLengkap', 'tanggalLahir', 'role',   
            'email', 'username', 'password', 'konfirmasiPassword'  
        ];  

        foreach ($requiredFields as $field) {  
            if (empty($data[$field])) {  
                throw new Exception("Field $field wajib diisi");  
            }  
        }  

        // Validasi password  
        if ($data['password'] !== $data['konfirmasiPassword']) {  
            throw new Exception("Konfirmasi password tidak cocok");  
        }  

        // Validasi email  
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {  
            throw new Exception("Format email tidak valid");  
        }  

        // Cek apakah username atau email sudah ada  
        $checkQuery = "SELECT id_user FROM user WHERE username = ? OR email = ?";  
        $existingUser = selectQuery($checkQuery, [$data['username'], $data['email']]);  
        
        if (!empty($existingUser)) {  
            throw new Exception("Username atau email sudah terdaftar");  
        }  

        // Hash password  
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);  

        // Siapkan query untuk insert  
        $query = "INSERT INTO user (  
            nama, tanggal_lahir, role, email,   
            username, password,foto_profile, created_at  
        ) VALUES (?, ?, ?, ?, ?, ?,?, NOW())";  

        $params = [  
            $data['namaLengkap'],  
            $data['tanggalLahir'],  
            $data['role'] == 'admin_utama' ? 'admin' : 'admin artikel',  
            $data['email'],  
            $data['username'],  
            $hashedPassword,
            'Assets/img/foto-profile/Default.png'  
        ];  

        // Eksekusi query  
        $stmt = executeQuery($query, $params, 'Tambah user baru');  

        // Log aktivitas  
        $logger = Logger::getInstance();  
        $logger->logActivity('USER_MANAGEMENT', 'Berhasil menambah user baru', [  
            'username' => $data['username'],  
            'role' => $data['role']  
        ]);  

        echo json_encode([  
            'status' => 'success',   
            'message' => 'User berhasil ditambahkan'  
        ]);  

    } catch (Exception $e) {  
        // Log error  
        $logger = Logger::getInstance();  
        $logger->logError('USER_ADD_ERROR', 'Gagal menambah user', $e);  

        http_response_code(400);  
        echo json_encode([  
            'status' => 'error',   
            'message' => $e->getMessage()  
        ]);  
    }  
} else {  
    // Metode request tidak valid  
    http_response_code(405);  
    echo json_encode([  
        'status' => 'error',  
        'message' => 'Metode tidak diizinkan'  
    ]);  
}