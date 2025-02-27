<?php
session_start();
require_once 'koneksi.php';
require_once 'forum_functions.php';
require_once __DIR__ . '/Auth/auth.php';
AuthHelper::requireRole(['user']);    

header('Content-Type: application/json');

try {
    // Pastikan user sudah login
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Anda harus login terlebih dahulu'
        ]);
        exit;
    }

    // Ambil posts
    $currentUserId = $_SESSION['user_id'] ?? null;
    $posts = getPosts(10, 0, $currentUserId);


    // Tambahkan status like untuk setiap post
    foreach ($posts as &$post) {
        $likeStatus = getUserLikeStatus($_SESSION['user_id'], $post['id_diskusi']);
        $post['user_like_status'] = $likeStatus;
    }
    

    echo json_encode($posts);
} catch (Exception $e) {
    Logger::getInstance()->logError('GET_POSTS_ERROR', $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil postingan'
    ]);
}
?>