<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Pastikan hanya admin yang bisa update user  
AuthHelper::requireRole(['admin']);  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $data = json_decode(file_get_contents('php://input'), true);  

    try {  
        // Validasi input  
        if (empty($data['userId'])) {  
            throw new Exception("ID User tidak valid");  
        }  

        // Validasi input yang wajib  
        $requiredFields = [  
            'namaLengkap', 'tanggalLahir', 'role',   
            'email', 'username'  
        ];  

        foreach ($requiredFields as $field) {  
            if (empty($data[$field])) {  
                throw new Exception("Field $field wajib diisi");  
            }  
        }  

        // Validasi email  
       

        // Cek apakah user exist  
        $checkQuery = "SELECT id_user FROM user WHERE id_user = ?";  
        $existingUser = selectQuery($checkQuery, [$data['userId']]);  
        
        if (empty($existingUser)) {  
            throw new Exception("User tidak ditemukan");  
        }  

        // Cek konflik username/email (kecuali user saat ini)  
        $conflictQuery = "SELECT id_user FROM user WHERE (username = ? OR email = ?) AND id_user != ?";  
        $conflictUser = selectQuery($conflictQuery, [  
            $data['username'],   
            $data['email'],   
            $data['userId']  
        ]);  
        
        if (!empty($conflictUser)) {  
            throw new Exception("Username atau email sudah terdaftar");  
        }  

        // Siapkan query untuk update  
        $params = [  
            $data['namaLengkap'],  
            $data['tanggalLahir'],  
            $data['role'] == 'admin_utama' ? 'admin' : 'admin artikel',  
            $data['email'],  
            $data['username'],  
            $data['userId']  
        ];  

        // Query update tanpa password  
        $query = "UPDATE user SET   
            nama = ?,   
            tanggal_lahir = ?,   
            role = ?,   
            email = ?,   
            username = ?  
            WHERE id_user = ?";  

        // Handle password update jika ada  
        if (!empty($data['password'])) {  
            // Validasi password  
            if ($data['password'] !== $data['konfirmasiPassword']) {  
                throw new Exception("Konfirmasi password tidak cocok");  
            }  

            // Hash password  
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);  
            
            // Update query dengan password  
            $query = "UPDATE user SET   
                nama = ?,   
                tanggal_lahir = ?,   
                role = ?,   
                email = ?,   
                username = ?,  
                password = ?  
                WHERE id_user = ?";  
            
            // Tambahkan password di parameter  
            $params = [  
                $data['namaLengkap'],  
                $data['tanggalLahir'],  
                $data['role'] == 'admin_utama' ? 'admin' : 'admin artikel',  
                $data['email'],  
                $data['username'],  
                $hashedPassword,  
                $data['userId']  
            ];  
        }  

        // Eksekusi query  
        $stmt = executeQuery($query, $params, 'Update user');  

        // Log aktivitas  
        $logger = Logger::getInstance();  
        $logger->logActivity('USER_MANAGEMENT', 'Berhasil update user', [  
            'username' => $data['username'],  
            'user_id' => $data['userId']  
        ]);  

        echo json_encode([  
            'status' => 'success',   
            'message' => 'User berhasil diupdate'  
        ]);  

    } catch (Exception $e) {  
        // Log error  
        $logger = Logger::getInstance();  
        $logger->logError('USER_UPDATE_ERROR', 'Gagal update user', $e);  

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