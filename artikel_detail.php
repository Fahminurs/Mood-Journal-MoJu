<?php  
require_once 'koneksi.php';  

// Ambil ID artikel dari URL  
$artikel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;  

// Query untuk mendapatkan detail artikel  
$artikel_query = "SELECT * FROM artikel WHERE id_artikel = ?";  
$artikel_params = [$artikel_id];  
$artikel = selectQuery($artikel_query, $artikel_params, 'Get Artikel Detail');  

// Jika artikel tidak ditemukan  
if (empty($artikel)) {  
    die("Artikel tidak ditemukan");  
}  

$artikel = $artikel[0]; // Ambil data pertama  
?>  
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title><?= htmlspecialchars($artikel['judul']) ?></title>  
    <!-- Tambahkan CSS yang sesuai -->  
</head>  
<body>  
    <div class="container">  
        <h1><?= htmlspecialchars($artikel['judul']) ?></h1>  
        <div class="artikel-meta">  
            <span>Penulis: <?= htmlspecialchars($artikel['author']) ?></span>  
            <span>Tanggal: <?= date('d F Y', strtotime($artikel['create_at'])) ?></span>  
        </div>  
        <div class="artikel-content">  
            <?= $artikel['content'] ?>  
        </div>  
    </div>  
</body>  
</html>