<?php  
// Pastikan hanya admin yang bisa mengakses  

require_once 'koneksi.php';  
require_once __DIR__ . '/Auth/auth.php';

// Cek login dan role admin  
AuthHelper::requireRole(['user']);  

// Dapatkan informasi pengguna yang login  
$user = AuthHelper::getCurrentUser();  

// Dapatkan mood dominan dari journal pengguna yang login
$mood_query = "SELECT mood, COUNT(*) as mood_count 
               FROM journal 
               WHERE id_user = ? 
               GROUP BY mood 
               ORDER BY mood_count DESC 
               LIMIT 1";
$mood_params = [$user['user_id']];
$dominant_mood_result = selectQuery($mood_query, $mood_params, 'Get Dominant Mood');

$dominant_mood = $dominant_mood_result ? $dominant_mood_result[0]['mood'] : 'Senang';

// Mapping mood ke nama file emoji
$mood_emoji_map = [
    'Berpikir' => 'Berpikir atau bingung.png',
    'Bingung' => 'Berpikir atau bingung.png',
    'Cemas' => 'Cemas.png',
    'Cinta' => 'Cinta atau sayang.png',
    'Sayang' => 'Cinta atau sayang.png',
    'Lucu' => 'Lucu.png',
    'Marah' => 'Marah.png',
    'Ngantuk' => 'Ngantuk atau lelah.png',
    'Lelah' => 'Ngantuk atau lelah.png',
    'Percaya Diri' => 'Percaya Diri.png',
    'Ragu' => 'Ragu atau curiga.png',
    'Curiga' => 'Ragu atau curiga.png',
    'Sedih' => 'Sedih.png',
    'Senang' => 'Senang.png',
    'Takut' => 'Takut.png',
    'Terkejut' => 'Terkejut.png'
];

$emoji_file = $mood_emoji_map[$dominant_mood] ?? 'Senang.png';

function truncateToAlphabets($text, $length = 10) {  
    // Hapus tag HTML  
    $text = strip_tags($text);  
    
    // Hapus karakter non-alfabet  
    $alphabetsOnly = preg_replace('/[^a-zA-Z]/', '', $text);  
    
    // Ambil 10 huruf pertama  
    $truncated = mb_substr($alphabetsOnly, 0, $length);  
    
    // Tambahkan ellipsis jika teks asli lebih panjang  
    $suffix = (mb_strlen($alphabetsOnly) > $length) ? '...' : '';  
    
    return $truncated . $suffix;  
}  


// Ambil journal pengguna yang login, diurutkan dari yang terbaru
$journal_query = "SELECT * FROM journal   
                  WHERE id_user = ?   
                  ORDER BY COALESCE(update_at, create_at) DESC   
                  LIMIT 5 OFFSET 0"; 
$journal_params = [$user['user_id']];
$journals = selectQuery($journal_query, $journal_params, 'Get User Journals');

// Ambil 2 artikel terbaru
$artikel_query = "SELECT * FROM artikel 
                  ORDER BY COALESCE(update_at, create_at) DESC 
                  LIMIT 2";
$artikels = selectQuery($artikel_query, [], 'Get Latest Articles');
?>
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>MOJU</title>  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;800&display=swap" rel="stylesheet">  
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">  
    <link rel="stylesheet" href="Assets/css/home.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
 .loading-overlay {  
    position: relative;  /* Ubah dari fixed menjadi relative */  
    width: 100%;  
    height: auto;  
    /* background: rgba(255, 255, 255, 0.8);   */
    display: flex;  
    justify-content: center;  
    align-items: center;  
    z-index: 10;  
    opacity: 0;  
    visibility: hidden;  
    transition: opacity 0.3s ease, visibility 0.3s ease;  
    margin-top: 20px; /* Tambahkan margin top */  
    margin-bottom: 40px; 
}  

.loading-spinner {  
    width: 50px;  
    height: 50px;  
    border: 5px solid #f3f3f3;  
    border-top: 5px solid #3498db;  
    border-radius: 50%;  
    animation: spin 1s linear infinite;  
    margin: 20px 0; /* Tambahkan margin vertikal */ 
   
} 

@keyframes spin {  
    0% { transform: rotate(0deg); }  
    100% { transform: rotate(360deg); }  
}  

.loading-overlay.show {  
    opacity: 1;  
    visibility: visible;  
}  
a{
    text-decoration: none;
color: white;
}
    </style>
</head>  
<body>  
    <!-- Tambahkan di bawah <body> -->  

    <div class="container">  
        <nav class="sidebar">
            <div class="logo">MOJU</div>
            <div class="nav-divider"></div>
            <div class="nav-item active"><i class="fas fa-home"></i></div>
            <a href="forumdiskusi.php" style="text-decoration: none;">
            <div class="nav-item"><i class="fa-solid fa-comment"></i></div>
            </a>
            <a href="settings.php" style="text-decoration: none;">
            <div class="nav-item "><i class="fas fa-cog"></i></div>
            <div class="nav-divider"></div>
            </a>
        </nav> 

        <div class="main-content">  
            <div class="logo-mobile">MOJU</div>  
            
            <header class="header">  
                <h1>Serba - Serbi</h1>  
            </header>  

            <div class="cards-container">  
                <!-- Mood Card -->
                <div class="mood-card">  
    <h2 class="card-title">Dominan Mood Kamu</h2>  
    <?php if(!empty($dominant_mood_result)): ?>  
    <div class="mood-emoji">  
        <img src="Assets/Emoji/<?= htmlspecialchars($emoji_file) ?>" alt="<?= htmlspecialchars($dominant_mood) ?>">  
    </div>  
    <p class="mood-label"><?= htmlspecialchars($dominant_mood) ?></p>  
    <?php else: ?>  
    <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding: 20px;">  
        <p style="color:brown; font-weight:800; position:relative; top:-50px;">Isi Note agar mood kamu dapat terlihat</p>  
    </div>  
    <?php endif; ?>  
</div>  

                <!-- Artikel Card -->
                <div class="article-card">  
                    <div style="display: flex; justify-content: space-between; align-items: center;">  
                        <div class="artikel-title">
                            <h2 class="card-title">Artikel & Edukasi</h2>  
                            <a href="artikel.php" class="view-all">Lihat Semua</a>  
                        </div>
                    </div>  
                    <div class="timeline">  
    <?php foreach($artikels as $artikel): ?>  
    <div class="timeline-item" data-artikel-id="<?= $artikel['id_artikel'] ?>">   
        <a href="detail_artikel.php?artikel=<?= $artikel['judul'] ?>">  
            <div class="timeline-date"><?= date('d F Y', strtotime($artikel['create_at'])) ?></div>  
            <div class="timeline-text">  
            <?= mb_substr(strip_tags($artikel['content']), 0, 100) ?>....  
            </div>  
        </a>  
    </div>  
    <?php endforeach; ?>  
</div>    
                </div>  
            </div>  

            <div class="notes-section">  
                <h2 class="card-title" style="margin: 10px 10px ;" >Daftar Catatan yang Pernah di Buat</h2>  
                <a href="menulis.php" style="text-decoration: none;">
                <div class="add-note-btn">  
                    <i class="fas fa-plus"></i>  
                    Catatan  
                </div>  
                </a>
            </div>  

            <!-- Journal Cards -->
            <?php if(empty($journals)): ?>  
               
<div class="note-card" style="display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding: 20px;">  
<img src="Assets/Emoji/Senang.png" alt="">
    <h3 style="font-size: 24px; margin-bottom: 10px; color: white;">Selamat Datang Di Aplikasi Moju</h3>  
    <p style="color: wheat;">Isi Note agar mood kamu dapat terlihat</p>  
</div>  
<?php else: ?>  
    <?php foreach($journals as $journal): ?>  
    <div class="note-card">  
        <i class="fas fa-trash delete-btn" data-journal-id="<?= $journal['id_journal'] ?>"></i>  
        <h3 style="font-size: 24px; margin-bottom: 10px;"><?= htmlspecialchars($journal['judul']) ?></h3>  
        <a href="menulis.php?id=<?= $journal['id_journal'] ?>">  
            <p style="opacity: 0.8; margin-bottom: 20px;">  
            <?=  $journal['truncated_content'] = truncateToAlphabets($journal['isi']);  
             htmlspecialchars($journal['truncated_content']) ?>   
            </p>  
            <div style="font-size: 14px; font-weight: 700;"><?= date('d M Y', strtotime($journal['create_at'])) ?></div>  
            <div class="hasil-note">  
                <img src="Assets/Emoji/<?= $mood_emoji_map[$journal['mood']] ?? 'Senang.png' ?>" alt="">  
            </div>  
        </a>  
    </div>   
    <?php endforeach; ?>  
<?php endif; ?>  

        </div>  
    </div>  
<div style="margin-bottom: 50px;"></div>
    <!-- Navigation Bar -->
    <div class="Navigation-moju">
        <ul>
            <li class="list-moju active">
                <a href="#">
                    <span class="icon-moju "><i class="fas fa-home"></i></span>
                    <span class="text">Home</span>
                </a>
            </li>
           <li class="list-moju">
                <a href="forumdiskusi.php">
                    <span class="icon-moju"><i class="fa-solid fa-comment"></i></span>
                    <span class="text">Diskusi</span>
                </a>
            </li>
           <li class="list-moju">
                <a href="settings.php">
                    <span class="icon-moju"><i class="fas fa-cog"></i></span>
                    <span class="text">Settings</span>
                </a>
            </li>

            <div class="indikator-moju"></div>
        </ul>
    </div>

    <script>  
        // Fungsi untuk delete journal
        document.addEventListener('DOMContentLoaded', function() {  
    // Delegasi event untuk delete button yang ada dan akan dibuat  
    document.querySelector('.main-content').addEventListener('click', function(e) {  
        // Cek apakah yang diklik adalah tombol delete  
        if (e.target.classList.contains('delete-btn')) {  
            const journalId = e.target.getAttribute('data-journal-id');  
            
            // Tampilkan SweetAlert konfirmasi  
            Swal.fire({  
                title: 'Hapus Catatan',  
                text: 'Apakah Anda yakin ingin menghapus catatan ini? Tindakan ini tidak dapat dibatalkan.',  
                icon: 'warning',  
                showCancelButton: true,  
                confirmButtonColor: '#3085d6',  
                cancelButtonColor: '#d33',  
                confirmButtonText: 'Ya, Hapus!',  
                cancelButtonText: 'Batal'  
            }).then((result) => {  
                if (result.isConfirmed) {  
                    // Tampilkan loading  
                    Swal.fire({  
                        title: 'Sedang Menghapus...',  
                        allowOutsideClick: false,  
                        didOpen: () => {  
                            Swal.showLoading();  
                        }  
                    });  

                    // Kirim request hapus  
                    fetch('delete_journal.php', {  
                        method: 'POST',  
                        headers: {  
                            'Content-Type': 'application/x-www-form-urlencoded',  
                        },  
                        body: 'id_journal=' + encodeURIComponent(journalId)  
                    })  
                    .then(response => response.json())  
                    .then(data => {  
                        if (data.success) {  
                            // Hapus card dari DOM  
                            e.target.closest('.note-card').remove();  
                            
                            // Tampilkan pesan sukses  
                            Swal.fire({  
                                title: 'Berhasil!',  
                                text: 'Catatan telah dihapus.',  
                                icon: 'success',  
                                timer: 2000,  
                                showConfirmButton: false  
                            });  
                        } else {  
                            // Tampilkan pesan error  
                            Swal.fire({  
                                title: 'Gagal!',  
                                text: data.message || 'Tidak dapat menghapus catatan',  
                                icon: 'error',  
                                confirmButtonText: 'Tutup'  
                            });  
                        }  
                    })  
                    .catch(error => {  
                        console.error('Error:', error);  
                        // Tampilkan pesan error jaringan  
                        Swal.fire({  
                            title: 'Kesalahan!',  
                            text: 'Terjadi masalah saat menghapus catatan',  
                            icon: 'error',  
                            confirmButtonText: 'Tutup'  
                        });  
                    });  
                }  
            });  
        }  
    });  
});

        // Navigation scripts (existing scripts)
        const list = document.querySelectorAll('.list-moju');  
        const indikator = document.querySelector('.indikator-moju');  
        
        function updateIndicatorPosition(activeItem) {  
            if (activeItem) {  
                const ul = activeItem.parentElement;  
                const ulLeftOffset = ul.offsetLeft;  
                const liWidth = activeItem.offsetWidth;  
                const liLeftOffset = activeItem.offsetLeft;  
                const correctOffset = liLeftOffset - ulLeftOffset;  
        
                indikator.style.width = `${liWidth}px`;  
                indikator.style.transform = `translateX(${correctOffset}px)`;  
            }  
        }  
        
        function activeLink() {  
            list.forEach((item) => {  
                item.classList.remove('active');  
            });  
            
            this.classList.add('active');  
            updateIndicatorPosition(this);  
        }  
        
        list.forEach((item) => {  
            item.addEventListener('click', activeLink);  
        });  
        
        document.addEventListener('DOMContentLoaded', () => {  
            const activeItem = document.querySelector('.list-moju.active');  
            if (activeItem) {  
                updateIndicatorPosition(activeItem);  
            }  
        });  
        
        window.addEventListener('resize', () => {  
            const activeItem = document.querySelector('.list-moju.active');  
            if (activeItem) {  
                updateIndicatorPosition(activeItem);  
            }  
        });  
    </script>
 <script>  
let page = 1;  
let isLoading = false;  
let hasMoreData = true;  
let loadedJournalIds = new Set();   

function loadMoreJournals() {  
    if (isLoading || !hasMoreData) return;  

    isLoading = true;  
    const mainContent = document.querySelector('.main-content');  
    
    const loadingOverlay = document.createElement('div');  
    loadingOverlay.classList.add('loading-overlay', 'show');  
    loadingOverlay.innerHTML = `  
        <div class="loading-spinner"></div>  
    `;  
    mainContent.appendChild(loadingOverlay);  

    fetch(`load_more_journals.php?page=${page}`, {  
        method: 'GET',  
        headers: {  
            'X-Requested-With': 'XMLHttpRequest'  
        }  
    })  
    .then(response => response.json())  
    .then(data => {  
        if (data.success && data.journals && data.journals.length > 0) {  
            const container = document.querySelector('.main-content');  
            
            data.journals.forEach(journal => {  
                if (!loadedJournalIds.has(parseInt(journal.id_journal))) {  
                    const journalCard = createJournalCard(journal);  
                    container.insertAdjacentHTML('beforeend', journalCard);  
                    loadedJournalIds.add(parseInt(journal.id_journal));  
                }  
            });  

            page++;  
            hasMoreData = data.hasMore;  
            attachDeleteListeners();  
        }   

        if (!hasMoreData) {  
            const noMoreDataMessage = document.createElement('div');  
            noMoreDataMessage.innerHTML = `  
                <div style="text-align: center; margin-top: 20px; margin-bottom: 50px; color: #888; font-size: 16px;">  
                    <img src="Assets/Emoji/Sedih.png" alt="Tidak ada data" style="width: 100px; margin-bottom: 10px;">  
                    <p>Tidak ada data selanjutnya</p>  
                    <p style="font-size: 14px; color: #666;">Kamu sudah melihat semua catatan</p>  
                </div>  
            `;  
            mainContent.appendChild(noMoreDataMessage);  
        }  
    })  
    .catch(error => {  
        console.error('Error:', error);  
        const errorMessage = document.createElement('div');  
        errorMessage.textContent = 'Gagal memuat catatan. Silakan coba lagi.';  
        errorMessage.style.color = 'red';  
        errorMessage.style.textAlign = 'center';  
        errorMessage.style.marginTop = '20px';  
        mainContent.appendChild(errorMessage);  
    })  
    .finally(() => {  
        isLoading = false;  
        const loadingOverlay = mainContent.querySelector('.loading-overlay');  
        if (loadingOverlay) {  
            loadingOverlay.remove();  
        }  
    });  
}  

function attachDeleteListeners() {  
    document.querySelectorAll('.delete-btn').forEach(deleteBtn => {  
        deleteBtn.removeEventListener('click', handleDeleteJournal);  
        deleteBtn.addEventListener('click', handleDeleteJournal);  
    });  
}  

function handleDeleteJournal(e) {  
    const journalId = e.target.getAttribute('data-journal-id');  
    
    Swal.fire({  
        title: 'Hapus Catatan',  
        text: 'Apakah Anda yakin ingin menghapus catatan ini? Tindakan ini tidak dapat dibatalkan.',  
        icon: 'warning',  
        showCancelButton: true,  
        confirmButtonColor: '#3085d6',  
        cancelButtonColor: '#d33',  
        confirmButtonText: 'Ya, Hapus!',  
        cancelButtonText: 'Batal'  
    }).then((result) => {  
        if (result.isConfirmed) {  
            Swal.fire({  
                title: 'Sedang Menghapus...',  
                allowOutsideClick: false,  
                didOpen: () => {  
                    Swal.showLoading();  
                }  
            });  

            fetch('delete_journal.php', {  
                method: 'POST',  
                headers: {  
                    'Content-Type': 'application/x-www-form-urlencoded',  
                },  
                body: 'id_journal=' + encodeURIComponent(journalId)  
            })  
            .then(response => response.json())  
            .then(data => {  
                if (data.success) {  
                    e.target.closest('.note-card').remove();  
                    loadedJournalIds.delete(parseInt(journalId));  
                    
                    Swal.fire({  
                        title: 'Berhasil!',  
                        text: 'Catatan telah dihapus.',  
                        icon: 'success',  
                        timer: 2000,  
                        showConfirmButton: false  
                    });  
                } else {  
                    Swal.fire({  
                        title: 'Gagal!',  
                        text: data.message || 'Tidak dapat menghapus catatan',  
                        icon: 'error',  
                        confirmButtonText: 'Tutup'  
                    });  
                }  
            })  
            .catch(error => {  
                console.error('Error:', error);  
                Swal.fire({  
                    title: 'Kesalahan!',  
                    text: 'Terjadi masalah saat menghapus catatan',  
                    icon: 'error',  
                    confirmButtonText: 'Tutup'  
                });  
            });  
        }  
    });  
}  

document.addEventListener('DOMContentLoaded', () => {  
    const initialJournals = document.querySelectorAll('.note-card');  
    
    // Hanya inisialisasi jika ada journal  
    if (initialJournals.length > 0) {  
        // Ambil ID journal yang sudah ada  
        initialJournals.forEach(journal => {  
            const journalId = journal.querySelector('.delete-btn').getAttribute('data-journal-id');  
            loadedJournalIds.add(parseInt(journalId));  
        });  

        // Tambahkan event listener untuk delete  
        attachDeleteListeners();  

        // Tambahkan event listener scroll  
        window.addEventListener('scroll', () => {  
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {  
                loadMoreJournals();  
            }  
        });  
    }  
});  

function createJournalCard(journal) {  
    return `  
    <div class="note-card">  
        <i class="fas fa-trash delete-btn" data-journal-id="${journal.id_journal}"></i>  
        <h3 style="font-size: 24px; margin-bottom: 10px;">${escapeHtml(journal.judul)}</h3>  
        <a href="menulis.php?id=${journal.id_journal}">  
            <p style="opacity: 0.8; margin-bottom: 20px;">${escapeHtml(journal.isi)}</p>  
            <div style="font-size: 14px; font-weight: 700;">${journal.create_at}</div>  
            <div class="hasil-note">  
                <img src="Assets/Emoji/${journal.mood_emoji}" alt="">  
            </div>  
        </a>  
    </div>`;  
}  

function escapeHtml(unsafe) {  
    return unsafe  
         .replace(/&/g, "&amp;")  
         .replace(/</g, "&lt;")  
         .replace(/>/g, "&gt;")  
         .replace(/"/g, "&quot;")  
         .replace(/'/g, "&#039;");  
}  

// Modifikasi artikel untuk dapat diklik  
document.querySelectorAll('.timeline-item').forEach(item => {  
    item.addEventListener('click', function() {  
        const artikelId = this.getAttribute('data-artikel-id');  
        window.location.href = `artikel.php?id=${artikelId}`;  
    });  
});  
</script>
</body>  

</html>