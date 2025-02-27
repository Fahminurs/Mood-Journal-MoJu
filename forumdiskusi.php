
<?php
require_once 'koneksi.php';
require_once __DIR__ . '/Auth/auth.php'; 
session_start();
AuthHelper::requireRole(['user']);    
?>

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Forum Diskusi</title>  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;800&display=swap" rel="stylesheet">  
    <link href="https://fonts.googleapis.com/css2?family=Sankofa+Display&display=swap" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">  
    <link rel="stylesheet" href="Assets/css/forumDiskusi.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Ketika elemen diklik atau memiliki kelas aktif */
    .dilike {
    transition: transform 0.2s ease-in-out; /* Animasi halus */
}

.dilike.active {
    animation: dilikeBounce 0.5s ease-in-out;
    color: #ff00e9; /* Warna berubah saat aktif */
}
    .diunlike {
    transition: transform 0.2s ease-in-out; /* Animasi halus */
}

.diunlike.active {
    animation: dilikeBounce 0.5s ease-in-out;
    color: #ff00e9; /* Warna berubah saat aktif */
}
.forum-card {  

    position: relative; /* Tambahkan ini untuk mengatur konteks posisi */
}  
.delete-btn {
    position: absolute;
    top: 10px; /* Jarak dari atas */
    right: 10px; /* Jarak dari kanan */
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 16px; /* Sesuaikan ukuran font/icon */
    color: #ffffff;
    padding: 5px;
    z-index: 10;
    transition: all 0.2s ease-in-out;
}

/* Animasi membesar dan bergoyang */
@keyframes dilikeBounce {
    0%, 100% {
        transform: scale(1);
    }
    25% {
        transform: scale(1.2) rotate(-10deg);
    }
    50% {
        transform: scale(1.2) rotate(10deg);
    }
    75% {
        transform: scale(1.2) rotate(-5deg);
    }
}
.tanggal{
    margin-top: -10px;
    font-size: 12px;
    font-weight: 800;

}
@media (min-width: 768px) {  
    .Navigation-moju {  
        display: none !important;  
    }  
}  
</style>
</head>  
<body>  
<nav class="sidebar">
        <div class="logo">MOJU</div>
        <div class="nav-divider"></div>
        <a href="home.php" style="text-decoration: none;">
        <div class="nav-item"><i class="fas fa-home"></i></div>
        </a>
        <div class="nav-item active"><i class="fa-solid fa-comment"></i></div>
        <a href="settings.php" style="text-decoration: none;">
            <div class="nav-item "><i class="fas fa-cog"></i></div>
            <div class="nav-divider"></div>
        </a>
    </nav>

    <div class="main-content">  
        <nav class="header">  
            <h1>Forum Diskusi</h1>  
            <button class="post-button">  
                <i class="fas fa-plus"></i>  
                Postingan  
            </button>  
        </nav>  

           <nav class="sidebar-mobile">
        <h2 class="logo-mobile">MoJu</h2>
        <h2 class="title">Forum Diskusi</h2>
        <h2 class="action" style="color: white;"><i class="fas fa-plus"></i> </h2>
    </nav>

        <!-- Forum Card --> 
         <div class="jarak-mobile"></div>
     
         <div id="forum-posts" class="forum-posts"></div>
         </a>


        <!-- Modal Overlay -->  
<div class="modal-overlay" id="modalOverlay"></div>  

<!-- Modal Create Post -->  
<div class="modal" id="createPostModal">  
    <div class="modal-content">  
        <div class="modal-header">  
            <h2>Buat Postingan Baru</h2>  
            <button class="close-modal">  
                <i class="fas fa-times"></i>  
            </button>  
        </div>  
        <div class="modal-body">  
       
            
            <div class="input-group">  
                <label>Konten</label>  
                <textarea placeholder="Tulis isi postingan Anda di sini..." maxlength="1000"></textarea>  
                <div class="char-counter">0/1000</div>  
            </div>  

        </div>  
        <div class="modal-footer">  
            <button class="cancel-btn">Batal</button>  
            <button class="post-btn">Posting</button>  
        </div>  
    </div>  
</div>
<!-- End Navigation -->
     <!-- Navigation Bar -->
     <div class="Navigation-moju">
        <ul>
            <li class="list-moju ">
                <a href="home.php">
                    <span class="icon-moju "><i class="fas fa-home"></i></span>
                    <span class="text">Home</span>
                </a>
            </li>
           <li class="list-moju active">
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
 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
document.addEventListener('DOMContentLoaded', function() {  
    const postButton = document.querySelector('.post-button');  
    let lastScroll = 0;  
    
    window.addEventListener('scroll', function() {  
        const currentScroll = window.pageYOffset;  
        const isTop = currentScroll < 100; // Deteksi area paling atas  
        
        if (isTop) {  
            // Jika di area atas, tampilkan button normal dan sembunyikan floating  
            postButton.classList.remove('floating');  
            postButton.style.transform = 'none';  
            postButton.style.opacity = '1';  
            postButton.style.visibility = 'visible';  
        } else {  
            // Jika di-scroll ke bawah, tampilkan button floating  
            postButton.classList.add('floating');  
            postButton.style.transform = 'scale(1)';  
            postButton.style.opacity = '1';  
            postButton.style.visibility = 'visible';  
        }  
        
        lastScroll = currentScroll;  
    }, { passive: true });  
}); 
        </script>
    <script>  
        // Fungsi untuk toggle active class pada nav items  
        document.querySelectorAll('.nav-item').forEach(item => {  
            item.addEventListener('click', function() {  
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));  
                this.classList.add('active');  
            });  
        });  

        // Fungsi untuk delete note  
        document.querySelector('.delete-btn').addEventListener('click', function() {  
            const noteCard = this.closest('.note-card');  
            noteCard.style.display = 'none';  
        });  
    </script>  
    <!-- Navigation -->
      <script>  
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
            // Modal functionality  
document.addEventListener('DOMContentLoaded', function() {  
    const postButton = document.querySelector('.post-button');  
    const actionButton = document.querySelector('.action');  
    const modalOverlay = document.getElementById('modalOverlay');  
    const modal = document.getElementById('createPostModal');  
    const closeModal = document.querySelector('.close-modal');  
    const cancelBtn = document.querySelector('.cancel-btn');  
    
    // Function to open modal  
    function openModal() {  
        modalOverlay.classList.add('active');  
        modal.classList.add('active');  
        document.body.style.overflow = 'hidden';  
    }  
    
    // Function to close modal  
    function closeModalFunc() {  
        modalOverlay.classList.remove('active');  
        modal.classList.remove('active');  
        document.body.style.overflow = '';  
    }  
    
    // Event listeners  
    postButton?.addEventListener('click', openModal);  
    actionButton?.addEventListener('click', openModal);  
    closeModal.addEventListener('click', closeModalFunc);  
    cancelBtn.addEventListener('click', closeModalFunc);  
    modalOverlay.addEventListener('click', closeModalFunc);  
    
    // Character counter  
    const inputs = document.querySelectorAll('input, textarea');  
    inputs.forEach(input => {  
        const counter = input.parentElement.querySelector('.char-counter');  
        input.addEventListener('input', () => {  
            const remaining = input.value.length;  
            const max = input.getAttribute('maxlength');  
            counter.textContent = `${remaining}/${max}`;  
        });  
    });  
    
    // Prevent modal close when clicking inside modal  
    modal.addEventListener('click', function(e) {  
        e.stopPropagation();  
    });  
});
        </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const postButton = document.querySelector('.post-btn');
        const modalTextarea = document.querySelector('.modal textarea');
        const forumPosts = document.getElementById('forum-posts');

        // Fungsi load posts
        function loadPosts() {
            fetch('get_posts.php')
                .then(response => response.json())
                .then(data => {
                    forumPosts.innerHTML = ''; // Clear existing posts
                    data.forEach(post => {
                        const postCard = createPostCard(post);
                        forumPosts.appendChild(postCard);
                    });
                });
        }

        // Fungsi membuat post card
        function createPostCard(post) {
    const card = document.createElement('div');
    card.className = 'forum-card';
    card.innerHTML = `
        ${post.is_owner ? `
            <button class="delete-btn" data-diskusi-id="${post.id_diskusi}">
                <i class="fas fa-trash"></i>
            </button>
        ` : ''}
        <div class="user-info">
            <img src="${post.foto_profile || 'Assets/img/foto-profile/example.png'}" alt="User">
            <span>${post.username}</span>
        </div>
        <div class="post-content"><div class="tanggal">Dipublish : ${post.tanggal}</div> <div>${post.content}</div></div>
        <div class="post-actions">
            <div class="action-btn like-btn" data-diskusi-id="${post.id_diskusi}">
                <i class="fa-solid fa-thumbs-up dilike ${post.dilike === 1 ? 'active' : ''}"></i>
                <span>${post.total_likes}</span>
            </div>
            <div class="action-btn dislike-btn" data-diskusi-id="${post.id_diskusi}">
                <i class="fa-solid fa-thumbs-down diunlike ${post.diunlike === 1 ? 'active' : ''}"></i>
            </div>
        </div>
    `;

    // Tambahkan event listener untuk like/dislike
    const likeBtn = card.querySelector('.like-btn');
    const dislikeBtn = card.querySelector('.dislike-btn');

    likeBtn.addEventListener('click', () => toggleLike(post.id_diskusi, 'like'));
    dislikeBtn.addEventListener('click', () => toggleLike(post.id_diskusi, 'unlike'));
    const deleteBtn = card.querySelector('.delete-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', () => deletePost(post.id_diskusi));
            }
    return card;
    // Fungsi untuk menghapus postingan

        }
        function deletePost(diskusiId) {
    Swal.fire({
        title: 'Yakin ingin menghapus postingan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete_post.php', {
                method: 'POST',
                body: new URLSearchParams({ id_diskusi: diskusiId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Postingan berhasil dihapus',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    loadPosts(); // Refresh postingan
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: data.message
                    });
                }
            });
        }
    });

   
}


        // Fungsi toggle like
        function toggleLike(diskusiId, action) {
    const formData = new FormData();
    formData.append('action', 'toggle_like');
    formData.append('diskusi_id', diskusiId);
    formData.append('like_action', action);

    fetch('forum_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const likeButton = document.querySelector(`.like-btn[data-diskusi-id="${diskusiId}"] i`);
            const dislikeButton = document.querySelector(`.dislike-btn[data-diskusi-id="${diskusiId}"] i`);
            const likeCount = likeButton.nextElementSibling;

            // Toggle kelas aktif
            if (action === 'like') {
                likeButton.classList.toggle('active');
                dislikeButton.classList.remove('active');
            } else if (action === 'unlike') {
                dislikeButton.classList.toggle('active');
                likeButton.classList.remove('active');
            }

            // Perbarui jumlah like/dislike
            if (likeCount) {
    const count = parseInt(likeCount.textContent, 10) || 0;
    if (likeButton.classList.contains('active')) {
        likeCount.textContent = count + 1; // Tambahkan 1 jika diaktifkan
    } else {
        likeCount.textContent = Math.max(count - 1, 0); // Pastikan tidak negatif
    }
}
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal memperbarui status like/unlike'
            });
        }
    });
}

        // Submit postingan
        postButton.addEventListener('click', function() {
            const content = modalTextarea.value.trim();
            
            if (content === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Konten postingan tidak boleh kosong'
                });
                return;
            }

            const formData = new FormData();
            formData.append('action', 'create_post');
            formData.append('content', content);

            fetch('forum_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Postingan berhasil dibuat',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    modalTextarea.value = ''; // Clear textarea
                    loadPosts(); // Reload posts
                    // Close modal
                    document.querySelector('.close-modal').click();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: data.message
                    });
                }
            });
        });

        // Load posts saat halaman dimuat
        loadPosts();
    });
    </script>

 
        
    </body>
    </html>