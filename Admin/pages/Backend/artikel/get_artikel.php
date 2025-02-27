<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Kode lainnya di sini

header('Content-Type: application/json');  

try {  
    // Dapatkan role dan ID user saat ini  
    AuthHelper::requireRole(['admin', 'admin artikel']);  
    $user = AuthHelper::getCurrentUser();  
    $currentUserRole = $user['role'] ?? '';  
    $currentUserId = $user['user_id'] ?? null;  

    // Parameter dari DataTables  
    $draw = $_GET['draw'] ?? 1;  
    $start = $_GET['start'] ?? 0;  
    $length = $_GET['length'] ?? 10;  
    $searchValue = $_GET['search']['value'] ?? '';  
    $orderColumn = $_GET['order'][0]['column'] ?? 0;  
    $orderDirection = $_GET['order'][0]['dir'] ?? 'asc';  

    // Kolom yang bisa diurutkan  
    $columns = [  
        'id_artikel',   
        'author',   
        'judul',   
        'content',   
        'create_at',   
        'update_at'  
    ];  

    // Query dasar  
    $baseQuery = "FROM artikel a   
                  LEFT JOIN user u ON a.id_user = u.id_user   
                  WHERE a.jenis = 'artikel'";  

    // Tambahkan kondisi pencarian  
    if (!empty($searchValue)) {  
        $baseQuery .= " AND (  
            a.author LIKE :search OR   
            a.judul LIKE :search OR   
            a.content LIKE :search  
        )";  
    }  

    // Tambahkan pengurutan  
    $orderColumnName = $columns[$orderColumn] ?? 'create_at';  
    $baseQuery .= " ORDER BY $orderColumnName $orderDirection";  

    // Query untuk total data  
    $totalQuery = "SELECT COUNT(*) as total " . $baseQuery;  
    $totalStmt = getConnection()->prepare(str_replace(':search', "'%$searchValue%'", $totalQuery));  
    $totalStmt->execute();  
    $totalData = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];  

    // Query untuk data  
    $dataQuery = "SELECT   
                    a.id_artikel,   
                    a.id_user as artikel_user_id,  
                    a.author,   
                    a.judul,   
                    SUBSTRING(a.content, 1, 100) as content_preview,  
                    a.create_at,   
                    a.update_at   
                  " . $baseQuery . "   
                  LIMIT :start, :length";  

    $stmt = getConnection()->prepare($dataQuery);  
    
    // Bind parameters  
    if (!empty($searchValue)) {  
        $searchParam = "%$searchValue%";  
        $stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);  
    }  
    $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);  
    $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);  

    $stmt->execute();  
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);  

    // Siapkan response DataTables  
    $response = [  
        'draw' => intval($draw),  
        'recordsTotal' => $totalData,  
        'recordsFiltered' => $totalData,  
        'data' => array_map(function($row, $index) use ($start, $currentUserRole, $currentUserId) {  
            // Cek kepemilikan artikel untuk admin artikel  
            $canEditDelete = false;  
            
            if ($currentUserRole === 'admin') {  
                // Admin selalu bisa  
                $canEditDelete = true;  
            } elseif ($currentUserRole === 'admin artikel') {  
                // Untuk admin artikel, cek kepemilikan di database  
                try {  
                    // Query untuk memeriksa apakah artikel milik user yang sedang login  
                    $checkOwnerQuery = "SELECT COUNT(*) as is_owner   
                                        FROM artikel   
                                        WHERE id_artikel = :artikel_id   
                                        AND id_user = :current_user_id";  
                    
                    $checkStmt = getConnection()->prepare($checkOwnerQuery);  
                    $checkStmt->bindValue(':artikel_id', $row['id_artikel'], PDO::PARAM_INT);  
                    $checkStmt->bindValue(':current_user_id', $currentUserId, PDO::PARAM_INT);  
                    $checkStmt->execute();  
                    
                    $ownerResult = $checkStmt->fetch(PDO::FETCH_ASSOC);  
                    $canEditDelete = $ownerResult['is_owner'] > 0;  
                } catch (Exception $e) {  
                    // Log error jika query gagal  
                    error_log("Error checking artikel ownership: " . $e->getMessage());  
                    $canEditDelete = false;  
                }  
            }  

            // Tentukan tombol aksi  
            $aksiButtons = $canEditDelete   
                ? '<div class="btn-group" role="group">  
                    <button class="btn btn-sm btn-info edit-btn-artikel" data-id="' . $row['id_artikel'] . '" title="Edit">  
                        <i class="fas fa-edit"></i>  
                    </button>  
                    <button class="btn btn-sm btn-danger delete-btn-artikel" data-id="' . $row['id_artikel'] . '" title="Hapus">  
                        <i class="fas fa-trash"></i>  
                    </button>  
                    <button class="btn btn-sm btn-dark view-btn-artikel" data-judul="' . htmlspecialchars($row['judul']) . '"  title="Lihat Detail">  
                        <i class="fas fa-eye"></i>  
                    </button>  
                  </div>'  
                : '<p style="font-size: 12px;">Bukan Pemilik artikel</p>';  

            return [  
                $start + $index + 1, // Nomor  
                htmlspecialchars($row['author']),  
                htmlspecialchars($row['judul']),  
                htmlspecialchars($row['content_preview'] . '...'),  
                date('d M Y H:i', strtotime($row['create_at'])),  
                date('d M Y H:i', strtotime($row['update_at'])),  
                $aksiButtons  
            ];  
        }, $data, array_keys($data))  
    ];  

    echo json_encode($response);  

} catch (Exception $e) {  
    // Error handling  
    http_response_code(500);  
    echo json_encode([  
        'error' => true,  
        'message' => $e->getMessage()  
    ]);  
}