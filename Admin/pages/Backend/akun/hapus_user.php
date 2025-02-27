<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Pastikan hanya admin yang bisa menghapus  
AuthHelper::requireRole(['admin']);     

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $userId = $_POST['id'];  

    try {  
        $query = "DELETE FROM user WHERE id_user = ? AND role IN ('admin', 'admin artikel')";  
        $result = executeQuery($query, [$userId], 'Hapus user');  

        if ($result) {  
            echo json_encode(['status' => 'success']);  
        } else {  
            throw new Exception('Gagal menghapus user');  
        }  
    } catch (Exception $e) {  
        http_response_code(500);  
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);  
    }  
}
?>