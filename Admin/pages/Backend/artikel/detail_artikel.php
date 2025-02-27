<?php  
require_once  __DIR__ . '/../../../../koneksi.php';
require_once __DIR__ . '/../../../../Auth/auth.php';

// Ambil judul dari URL  
$judul_artikel = isset($_GET['artikel']) ? urldecode($_GET['artikel']) : '';  

try {  
    $db=getConnection();  
    // Query untuk mengambil detail artikel  
    $stmt = $db->prepare("SELECT * FROM artikel WHERE judul = :judul");  
    $stmt->bindParam(':judul', $judul_artikel);  
    $stmt->execute();  
    
    $artikel = $stmt->fetch(PDO::FETCH_ASSOC);  

    if (!$artikel) {  
        header("Location: /moju/Admin/pages/artikel.php");  
    }  
} catch (PDOException $e) {  
    die("Error: " . $e->getMessage());  
}  
?>  
<!DOCTYPE html>  
<html lang="id">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title><?= htmlspecialchars($artikel['judul']) ?></title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
     <!-- Tambahkan Font Awesome -->  
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">   
    <style>  
        :root {  
            --peach-background: #FFDAB9;  
            --content-background: #FFFFFF;  
            --primary-color: #FF6B6B;  
            --secondary-color: #4ECDC4;  
            --text-color: #2C3E50;  
            --heading-font: 'Georgia', serif;  
            --body-font: 'Arial', sans-serif;  
        }  

        * {  
            margin: 0;  
            padding: 0;  
            box-sizing: border-box;  
        }  

        body {  
            font-family: var(--body-font);  
            background-color: var(--peach-background);  
            color: var(--text-color);  
            line-height: 1.7;  
        }  

        .artikel-wrapper {  
            max-width: 800px;  
            margin: 0 auto;  
            padding: 20px;  
        }  

        .artikel-container {  
            background-color: var(--content-background);  
            border-radius: 20px;  
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);  
            overflow: hidden;  
        }  

        .artikel-header {  
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));  
            color: white;  
            padding: 40px 30px;  
            text-align: center;  
        }  

        .artikel-header h1 {  
            font-family: var(--heading-font);  
            font-size: 2.5rem;  
            margin-bottom: 15px;  
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);  
        }  

        .artikel-meta {  
            display: flex;  
            justify-content: center;  
            gap: 20px;  
            margin-top: 15px;  
            color: rgba(255,255,255,0.8);  
        }  

        .artikel-content {  
            padding: 30px;  
        }  

        .artikel-content h2,  
        .artikel-content h3,  
        .artikel-content h4 {  
            color: var(--primary-color);  
            margin-top: 20px;  
            margin-bottom: 15px;  
        }  

        .artikel-content img {  
            max-width: 100%;  
            height: auto;  
            border-radius: 15px;  
            margin: 20px 0;  
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);  
            transition: transform 0.3s ease;  
        }  

        .artikel-content img:hover {  
            transform: scale(1.02);  
        }  

        .video-container {  
            position: relative;  
            padding-bottom: 56.25%;  
            height: 0;  
            overflow: hidden;  
            border-radius: 15px;  
            margin: 20px 0;  
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);  
        }  

        .video-container iframe {  
            position: absolute;  
            top: 0;  
            left: 0;  
            width: 100%;  
            height: 100%;  
            border: none;  
            border-radius: 15px;  
        }  

        .artikel-footer {  
            background-color: var(--peach-background);  
            padding: 20px;  
            text-align: center;  
        }  

        .btn-back {  
            background-color: var(--primary-color);  
            color: white;  
            border: none;  
            padding: 10px 20px;  
            border-radius: 25px;  
            text-decoration: none;  
            transition: all 0.3s ease;  
            display: inline-block;  
        }  

        .btn-back:hover {  
            background-color: var(--secondary-color);  
            transform: translateY(-3px);  
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);  
        } 
        .artikel-content img {  
    max-width: 80%; /* Opsional: batasi lebar */  
    max-height: 500px; /* Opsional: batasi tinggi */  
    width: auto;  
    height: auto;  
    margin: 20px auto;  
    display: block;  
    border-radius: 15px;  
    object-fit: contain; /* Menjaga aspek rasio */  
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);  
    transition: all 0.3s ease;  
}  

.artikel-content img:hover {  
    transform: scale(1.02);  
    box-shadow: 0 15px 25px rgba(0,0,0,0.15);  
}  

.back-icon {  
    background-color: #b3eaf5;
    color: #c56a6a;
            border-radius: 50%;  
            width: 50px;  
            height: 50px;  
            display: flex;  
            align-items: center;  
            justify-content: center;  
            position: absolute;  
            top: 20px;  
            left: 20px;  
            transition: all 0.3s ease;  
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);  
        }  

        .back-icon:hover {  
            background-color: var(--secondary-color);  
            transform: scale(1.1) rotate(-10deg);  
            box-shadow: 0 6px 8px rgba(0,0,0,0.2);  
        }  

        /* Positioning header relative for absolute icon */  
        .artikel-header {  
            position: relative;  
        }  
        


        /* Responsive Adjustments */  
        @media (max-width: 768px) { 
            .artikel-content img {  
        max-width: 100%;  
    }   
    .back-icon {  
        width: 40px;  
        height: 40px;  
        top: 10px;  
        left: 10px;  
    }  

    .back-icon i {  
        font-size: 1.2em;  
    }  
            .artikel-wrapper {  
                padding: 10px;  
            }  
            .artikel-header h1 {  
                font-size: 1.8rem;  
            }  
            .artikel-content {  
                padding: 20px;  
            }  
            .artikel-meta {  
                flex-direction: column;  
                align-items: center;  
            }  
        }  

        /* Animasi */  
        @keyframes fadeIn {  
            from {   
                opacity: 0;   
                transform: translateY(20px);   
            }  
            to {   
                opacity: 1;   
                transform: translateY(0);   
            }  
        }  

        .artikel-content {  
            animation: fadeIn 0.8s ease-out;  
        }  

        /* Blockquote Style */  
        blockquote {  
            border-left: 5px solid var(--primary-color);  
            padding-left: 15px;  
            font-style: italic;  
            color: #666;  
            margin: 20px 0;  
        }  

        /* Highlight */  
        mark {  
            background-color: rgba(255, 107, 107, 0.2);  
            color: var(--text-color);  
            padding: 2px 5px;  
            border-radius: 3px;  
        }  
    </style>  
</head>  
<body>  
    <div class="artikel-wrapper">  
        <div class="artikel-container">  
        <div class="artikel-header">  
                <!-- Tambahkan icon back -->  
                <a href="javascript:history.back()" class="back-icon" style="text-decoration:none;">  
                    <i class="fas fa-arrow-left fa-2x"></i>  
                </a>  

                <h1><?= htmlspecialchars($artikel['judul']) ?></h1>  
                <div class="artikel-meta">  
                    <span>  
                        <i class="bi bi-pencil-fill me-2"></i>  
                        <?= htmlspecialchars($artikel['author']) ?>  
                    </span>  
                    <span>  
                        <i class="bi bi-calendar-check me-2"></i>  
                        <?= date('d M Y', strtotime($artikel['create_at'])) ?>  
                    </span>  
                </div>  
            </div>   

            <div class="artikel-content">  
                <?php   
                $modified_content = preg_replace_callback(  
                    '/<iframe[^>]+>.*?<\/iframe>/is',   
                    function($matches) {  
                        return '<div class="video-container">' . $matches[0] . '</div>';  
                    },   
                    $artikel['content']  
                );  
                echo $modified_content;  
                ?>  
            </div>  

            
        </div>  
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>  
    <script>  
        // Zoom gambar dengan lightbox  
        document.addEventListener('DOMContentLoaded', () => {  
            document.querySelectorAll('.artikel-content img').forEach(img => {  
                img.style.cursor = 'zoom-in';  
                img.addEventListener('click', () => {  
                    const lightbox = document.createElement('div');  
                    lightbox.style.cssText = `  
                        position: fixed;  
                        top: 0;  
                        left: 0;  
                        width: 100%;  
                        height: 100%;  
                        background: rgba(0,0,0,0.8);  
                        display: flex;  
                        justify-content: center;  
                        align-items: center;  
                        z-index: 1000;  
                        cursor: zoom-out;  
                    `;  
                    
                    const imgClone = img.cloneNode(true);  
                    imgClone.style.maxWidth = '90%';  
                    imgClone.style.maxHeight = '90%';  
                    imgClone.style.objectFit = 'contain';  
                    
                    lightbox.appendChild(imgClone);  
                    document.body.appendChild(lightbox);  
                    
                    lightbox.addEventListener('click', () => {  
                        document.body.removeChild(lightbox);  
                    });  
                });  
            });  
        });  
    </script>  
</body>  
</html>  