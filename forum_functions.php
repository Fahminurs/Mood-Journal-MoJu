<?php
require_once 'koneksi.php';

function createPost($id_user, $content, $mood = null) {
    $logger = Logger::getInstance();
    try {
        $db = getConnection();
        
        // Insert ke tabel diskusi
        $sql = "INSERT INTO diskusi (id_user, content, mood, id_comment) VALUES (?, ?, ?, NULL)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_user, $content, $mood]);
        
        // Dapatkan ID diskusi yang baru saja dibuat
        $id_diskusi = $db->lastInsertId();
        
        // Log aktivitas untuk mencatat pembuatan postingan
        $logger->logActivity('FORUM', 'New post created', [
            'user_id' => $id_user,
            'diskusi_id' => $id_diskusi
        ]);
        
        return $id_diskusi;
    } catch (PDOException $e) {
        // Log error jika terjadi kesalahan
        $logger->logError('POST_ERROR', 'Failed to create post', $e);
        throw $e;
    }
}


function getPosts($limit = 10, $offset = 0, $id_user) {
     $db = getConnection();
    $logger = Logger::getInstance();
    try {
        // Validasi nilai LIMIT dan OFFSET untuk mencegah SQL Injection
        if (!is_numeric($limit) || !is_numeric($offset)) {
            throw new InvalidArgumentException("Limit dan offset harus berupa angka.");
        }

        $sql = "SELECT 
                    d.id_diskusi, 
                    d.content, 
                    u.username, 
                    u.foto_profile,
                    -- Total likes menghitung semua like per id_diskusi
                    (SELECT COUNT(*) 
                     FROM like_unlike l_sub 
                     WHERE l_sub.id_diskusi = d.id_diskusi AND l_sub.like > 0
                    ) AS total_likes,
                    COUNT(DISTINCT c.id_comment) AS total_comments,
                    -- Cek jika id_user sudah melakukan like
                    CASE 
                        WHEN l_like.id_user IS NOT NULL AND l_like.like > 0 THEN 1 
                        ELSE 0 
                    END AS dilike,
                    -- Cek jika id_user sudah melakukan unlike
                    CASE 
                        WHEN l_like.id_user IS NOT NULL AND l_like.unlike > 0 THEN 1 
                        ELSE 0 
                    END AS diunlike,
                    -- Tambahkan kolom untuk cek kepemilikan postingan
                    CASE 
                        WHEN d.id_user = ? THEN 1 
                        ELSE 0 
                    END AS is_owner,
                    DATE_FORMAT(d.create_at, '%d-%m-%Y, %H:%i') AS tanggal
                FROM diskusi d
                JOIN user u ON d.id_user = u.id_user
                LEFT JOIN like_unlike l_like ON d.id_diskusi = l_like.id_diskusi AND l_like.id_user = ?
                LEFT JOIN comment c ON d.id_diskusi = c.id_diskusi
                GROUP BY d.id_diskusi, d.content, u.username, u.foto_profile, d.create_at
                ORDER BY d.create_at DESC
                LIMIT $limit OFFSET $offset;";

                 

        $stmt = $db->prepare($sql);
        
        $stmt->execute([$id_user, $id_user]);

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $posts;
    } catch (PDOException $e) {
        $logger->logError('GET_POSTS_ERROR', $e->getMessage(), $e);
        throw $e;
    }
}


function toggleLike($id_user, $id_diskusi, $action) {
    $logger = Logger::getInstance();
    try {
        $db = getConnection();
        
        // Cek apakah sudah pernah like/unlike sebelumnya
        $checkSql = "SELECT * FROM like_unlike
                     WHERE id_user = ? AND id_diskusi = ?";
        $existing = selectQuery($checkSql, [$id_user, $id_diskusi]);

        if (!empty($existing)) {
            // Jika sudah ada, tentukan apakah toggle untuk membatalkan
            $isLiked = $existing[0]['like'] == 1;
            $isUnliked = $existing[0]['unlike'] == 1;

            if (($action === 'like' && $isLiked) || ($action === 'unlike' && $isUnliked)) {
                // Jika sudah like/unlike, batalkan
                $deleteSql = "DELETE FROM like_unlike WHERE id_user = ? AND id_diskusi = ?";
                executeQuery($deleteSql, [$id_user, $id_diskusi], 'Undo like/unlike');
            } else {
                // Update untuk mengganti tindakan sebelumnya
                $updateSql = "UPDATE like_unlike 
                              SET `like` = CASE WHEN ? = 'like' THEN 1 ELSE 0 END,
                                  `unlike` = CASE WHEN ? = 'unlike' THEN 1 ELSE 0 END
                              WHERE id_user = ? AND id_diskusi = ?";
                executeQuery($updateSql, [
                    $action, $action, $id_user, $id_diskusi
                ], 'Update like/unlike');
            }
        } else {
            // Belum pernah like/unlike, tambahkan record baru
            $insertSql = "INSERT INTO like_unlike 
                          (id_user, id_diskusi, `like`, `unlike`) 
                          VALUES (?, ?, ?, ?)";
            $likeValue = $action === 'like' ? 1 : 0;
            $unlikeValue = $action === 'unlike' ? 1 : 0;
            executeQuery($insertSql, [
                $id_user, $id_diskusi, $likeValue, $unlikeValue
            ], 'First time like/unlike');
        }

        $logger->logActivity('LIKE', "User $id_user $action post $id_diskusi");
        return true;
    } catch (PDOException $e) {
        $logger->logError('LIKE_ERROR', 'Failed to toggle like', $e);
        throw $e;
    }
}


function getUserLikeStatus($id_user, $id_diskusi) {
    $sql = "SELECT 
                COALESCE(`like`, 0) as is_liked, 
                COALESCE(`unlike`, 0) as is_unliked 
            FROM like_unlike 
            WHERE id_user = ? AND id_diskusi = ?";
    
    $result = selectQuery($sql, [$id_user, $id_diskusi]);
    return !empty($result) ? $result[0] : ['is_liked' => 0, 'is_unliked' => 0];
}
?>