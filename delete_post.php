<?php
session_start();
require_once 'koneksi.php';
require_once __DIR__ . '/Auth/auth.php'; 
AuthHelper::requireRole(['user']);    

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_diskusi']) || empty($_POST['id_diskusi'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID diskusi tidak ditemukan dalam permintaan.']);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User tidak login.']);
        exit;
    }

    $diskusiId = intval($_POST['id_diskusi']); // Validasi sebagai integer
    $userId = intval($_SESSION['user_id']); // Validasi sebagai integer

    try {
        $db = getConnection();

        if (!$db) {
            echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal.']);
            exit;
        }

        // Pastikan postingan dimiliki oleh user yang sedang login
        $query = "DELETE FROM diskusi WHERE id_diskusi = :id_diskusi AND id_user = :id_user";
        $stmt = $db->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id_diskusi', $diskusiId, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);

        // Eksekusi query
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Postingan berhasil dihapus.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Tidak ada baris yang dihapus. Pastikan Anda adalah pemilik postingan.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Kesalahan saat menghapus postingan.']);
        }
    } catch (PDOException $e) {
        // Log error
        Logger::getInstance()->logError('DELETE_POST_ERROR', 'Kesalahan saat menghapus postingan', $e);
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada sistem.']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Hanya metode POST yang diizinkan.']);
    exit;
}
