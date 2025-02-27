<?php  
require_once 'koneksi.php';  

// Ambil artikel edukasi untuk slider  
$edukasi_query = "SELECT * FROM artikel   
                  WHERE jenis = 'edukasi'   
                  ORDER BY COALESCE(update_at, create_at) DESC";  
$edukasis = selectQuery($edukasi_query, [], 'Get Edukasi Articles');  

// Ambil semua artikel  
$artikel_query = "SELECT * FROM artikel   
                  WHERE jenis = 'artikel'   
                  ORDER BY COALESCE(update_at, create_at) DESC";  
$artikels = selectQuery($artikel_query, [], 'Get All Artikel');  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel Kesehatan Mental</title>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.5.1/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Assets/css/Artikel.css">
    <style>  
        /* Tambahkan style untuk loading */  
        .loading-overlay {  
            position: relative;  
            width: 100%;  
            height: auto;  
            display: flex;  
            justify-content: center;  
            align-items: center;  
            z-index: 10;  
            opacity: 0;  
            visibility: hidden;  
            transition: opacity 0.3s ease, visibility 0.3s ease;  
            margin-top: 20px;  
        }  

        .loading-spinner {  
            width: 50px;  
            height: 50px;  
            border: 5px solid #f3f3f3;  
            border-top: 5px solid #3498db;  
            border-radius: 50%;  
            animation: spin 1s linear infinite;  
            margin: 20px 0;  
        }   

        @keyframes spin {  
            0% { transform: rotate(0deg); }  
            100% { transform: rotate(360deg); }  
        }  

        .loading-overlay.show {  
            opacity: 1;  
            visibility: visible;  
        }  
        .loading-overlay p {  
    color: #333;  
    margin-top: 10px;  
    text-align: center;  
}
    </style> 


</head>
<body>
    <nav class="navbar" style="position: fixed; width:100%; z-index:9999;">
        <div class="container-fluid">
            <div style="display: flex; align-items: center;">
                <a href="home.php" style="text-decoration: none; color: inherit; padding: 0; margin: 0;"><span class="back-button"><i class="fi fi-rr-arrow-small-left"></i></span></a>
                <h2>Artikel</h2>
            </div>
        </div>
    </nav>

    <div class="content">
    <div style="margin-top: 50px;"></div>
        <div class="section-header">
            
            <h2>Edukasi Kesehatan Mental</h2>
            <button class="layout-toggle" onclick="toggleLayout()">
                <i class="fi fi-rr-apps"></i>
            </button>
        </div>
        <div class="education-container">
            <div class="education-slider">
            <div class="education-slider">  
                <?php foreach($edukasis as $edukasi): ?>  
                <div class="education-card" data-edukasi-judul="<?= $edukasi['judul'] ?>">  
                    <img src="https://cdn-icons-png.flaticon.com/512/4207/4207247.png">  
                    <h3><?= htmlspecialchars($edukasi['judul']) ?></h3>  
                    <p><?= substr(strip_tags($edukasi['content']), 0, 100) ?>...</p>  
                </div>  
                <?php endforeach; ?>  
            </div> 
            </div>
        </div>

        <h2>Artikel Terbaru</h2>
        <div class="timeline" id="artikel-container">
        <?php foreach($artikels as $artikel): ?>  
            <div class="timeline-item" data-artikel-judul="<?= $artikel['judul'] ?>">  
                <div class="timeline-content">  
                    <h3><?= htmlspecialchars($artikel['judul']) ?></h3>  
                    <p><?= substr(strip_tags($artikel['content']), 0, 200) ?>...</p>  
                    <span class="date"><?= date('d F Y', strtotime($artikel['create_at'])) ?></span>  
                </div>  
            </div>  
            <?php endforeach; ?>  
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>  
document.addEventListener('DOMContentLoaded', function() {  
    // Tambahkan kelas show ke semua item yang sudah ada saat halaman dimuat  
    const initialTimelineItems = document.querySelectorAll('.timeline-item');  
    initialTimelineItems.forEach((item, index) => {  
        setTimeout(() => {  
            item.classList.add('show');  
        }, index * 200);  
    });  

    // Event listener untuk artikel  
    document.querySelectorAll('.timeline-item').forEach(item => {  
        item.addEventListener('click', function() {  
            const artikelJudul = this.getAttribute('data-artikel-judul');  
            window.location.href = `detail_artikel.php?artikel=${artikelJudul}`;  
        });  
    });  

    // Event listener untuk edukasi card  
    document.querySelectorAll('.education-card').forEach(card => {  
        card.addEventListener('click', function() {  
            const edukasiId = this.getAttribute('data-edukasi-judul');  
            window.location.href = `detail_edukasi.php?edukasi=${edukasiId}`;  
        });  
    });  

    function toggleLayout() {  
        const container = document.querySelector('.education-container');  
        const toggleButton = document.querySelector('.layout-toggle i');  

        container.classList.toggle('grid');  
        
        if (container.classList.contains('grid')) {  
            toggleButton.classList.remove('fi-rr-apps');  
            toggleButton.classList.add('fi-rr-list');  
        } else {  
            toggleButton.classList.remove('fi-rr-list');  
            toggleButton.classList.add('fi-rr-apps');  
        }  
    }  

    // Tambahkan event listener untuk toggle layout  
    const toggleButton = document.querySelector('.layout-toggle');  
    if (toggleButton) {  
        toggleButton.addEventListener('click', toggleLayout);  
    }  
});  
</script>
</body>
</html>
