<?php
session_start();
require_once 'koneksi.php';
require_once 'forum_functions.php';

header('Content-Type: application/json');

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Anda harus login terlebih dahulu'
    ]);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'create_post':
            $content = $_POST['content'] ?? '';
            $mood = $_POST['mood'] ?? null;
            
            if (empty($content)) {
                throw new Exception('Konten postingan tidak boleh kosong');
            }
            
            $post_id = createPost($_SESSION['user_id'], $content, $mood);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Postingan berhasil dibuat',
                'post_id' => $post_id
            ]);
            break;
        
        case 'toggle_like':
            $diskusi_id = $_POST['diskusi_id'] ?? null;
            $action = $_POST['like_action'] ?? null;
            
            if (!$diskusi_id || !$action) {
                throw new Exception('Invalid request');
            }
            
            toggleLike($_SESSION['user_id'], $diskusi_id, $action);
            
            echo json_encode([
                'status' => 'success',
                'message' => $action === 'like' ? 'Postingan disukai' : 'Postingan tidak disukai'
            ]);
            break;
        
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    Logger::getInstance()->logError('FORUM_AJAX_ERROR', $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>