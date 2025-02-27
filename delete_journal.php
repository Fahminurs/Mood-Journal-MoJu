<?php  
require_once 'koneksi.php';  
require_once __DIR__ . '/Auth/auth.php';  

// Pastikan hanya user yang login bisa menghapus  
AuthHelper::requireLogin();  

header('Content-Type: application/json');  

try {  
    $id_journal = $_POST['id_journal'] ?? null;  
    
    if (!$id_journal) {  
        throw new Exception('ID jurnal tidak valid');  
    }  
    
    // Dapatkan user yang sedang login  
    $user = AuthHelper::getCurrentUser();  
    
    // Query untuk menghapus journal  
    $delete_query = "DELETE FROM journal WHERE id_journal = ? AND id_user = ?";  
    $stmt = executeQuery($delete_query, [$id_journal, $user['user_id']], 'Delete Journal');  
    
    if ($stmt->rowCount() > 0) {  
        echo json_encode(['success' => true]);  
    } else {  
        echo json_encode(['success' => false, 'message' => 'Jurnal tidak ditemukan atau Anda tidak memiliki izin']);  
    }  
} catch (Exception $e) {  
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);  
}