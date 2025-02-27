<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php'; 

AuthHelper::requireRole(['admin']);  

if (isset($_GET['id'])) {  
    $userId = $_GET['id'];  

    try {  
        // Cek apakah user memiliki password  
        $query = "SELECT   
            nama,   
            tanggal_lahir,   
            role,   
            email,   
            username,  
            password IS NOT NULL AS has_password  
        FROM user   
        WHERE id_user = ?";  
        
        $user = selectQuery($query, [$userId]);  

        if (empty($user)) {  
            throw new Exception("User tidak ditemukan");  
        }  

        // Hapus kolom password asli untuk keamanan  
        unset($user[0]['password']);  

        echo json_encode($user[0]);  
    } catch (Exception $e) {  
        http_response_code(404);  
        echo json_encode(['error' => $e->getMessage()]);  
    }  
}