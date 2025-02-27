$(document).ready(function () {
    // Inisialisasi Summernote untuk Edukasi
    $("#kontenEdukasi").summernote({
      placeholder: "Tulis konten edukasi di sini...",
      tabsize: 2,
      height: 400,
      toolbar: [
        ["style", ["style"]],
        ["font", ["bold", "italic", "underline", "clear"]],
        ["color", ["color"]],
        ["para", ["ul", "ol", "paragraph"]],
        ["table", ["table"]],
        ["insert", ["link", "picture", "video"]],
        ["view", ["fullscreen", "codeview"]],
      ],
    });

    // Tombol Simpan Edukasi
    $("#simpanEdukasi").on("click", function () {
      // Ambil value dari form
      let author = $("#authorEdukasi").val().trim();
      let judul = $("#judulEdukasi").val().trim();
      let konten = $("#kontenEdukasi").summernote("code");

      // Validasi
      if (
        author === "" ||
        judul === "" ||
        konten.replace(/<\/?[^>]+(>|$)/g, "") === ""
      ) {
        Swal.fire({
          icon: "warning",
          title: "Validasi Gagal",
          text: "Harap isi semua field yang wajib",
        });
        return;
      }

      // Konfirmasi Simpan
      Swal.fire({
        title: "Simpan Edukasi?",
        text: "Pastikan semua informasi sudah benar",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Simpan!",
      }).then((result) => {
        if (result.isConfirmed) {
          // Kirim data via AJAX
          $.ajax({
            url: "proses_edukasi.php",
            method: "POST",
            data: {
              author: author,
              judul: judul,
              konten: konten,
              
            },
            dataType: "json",
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  icon: "success",
                  title: "Berhasil!",
                  text: "Edukasi telah disimpan",
                }).then(() => {
                  // Reset form
                  $("#formTambahEdukasi")[0].reset();
                  $("#kontenEdukasi").summernote("code", "");
                  $("#tambahEdukasiModal").modal("hide");
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Gagal",
                  text: response.message || "Terjadi kesalahan",
                });
              }
            },
            error: function (xhr, status, error) {
              Swal.fire({
                icon: "error",
                title: "Kesalahan",
                text: "Terjadi masalah saat menyimpan edukasi: " + error,
              });
            },
          });
        }
      });
    });
  });

$(document).ready(function () {  
const table = $("#userTable").DataTable({  
    processing: true,  
    serverSide: true,  
    ajax: {  
        url: './Backend/artikel/get_artikel.php', // Sesuaikan path  
        type: 'GET',  
        error: function (xhr, error, thrown) {  
            console.error('DataTables error:', error);  
            Swal.fire({  
                icon: 'error',  
                title: 'Gagal Memuat Data',  
                text: 'Terjadi kesalahan saat mengambil data artikel'  
            });  
        }  
    },  
    columns: [  
        { data: 0 },           // No  
        { data: 1 },            // Penulis  
        { data: 2 },            // Judul  
        { data: 3 },            // Konten Preview  
        { data: 4 },            // Dibuat  
        { data: 5 },            // Diperbaharui  
        {   
            data: 6,             // Aksi  
            orderable: false,    // Kolom aksi tidak bisa diurutkan  
            searchable: false    // Kolom aksi tidak bisa dicari  
        }  
    ],  
    language: {  
        processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Memuat...</span></div>',  
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",  
        infoEmpty: "Tidak ada data yang ditampilkan",  
        infoFiltered: "(disaring dari _MAX_ total entri)",  
        lengthMenu: "_MENU_",  
        search: "",  
        searchPlaceholder: "Cari Data Artikel",  
        zeroRecords: "Tidak ada data artikel yang cocok ditemukan",  
        paginate: {  
            first: "<i class='fas fa-angle-double-left'></i>",  
            last: "<i class='fas fa-angle-double-right'></i>",  
            next: "<i class='fas fa-angle-right'></i>",  
            previous: "<i class='fas fa-angle-left'></i>",  
        },  
    },  
    lengthMenu: [  
        [5, 10, 25, 50, 100, -1],  
        [  
            "5 Data",  
            "10 Data",  
            "25 Data",   
            "50 Data",   
            "100 Data",   
            "Semua Data"  
        ],  
    ],  
    pagingType: "simple_numbers",  
    responsive: true,  
    
    // Event handler untuk tombol aksi  
    drawCallback: function() {  
      
        // Edit Artikel  
        $('#tambahArtikelBtn').on('click', function() {  
    // Reset form  
    $('#formTambahArtikel')[0].reset();  
    $('#konten').summernote('code', '');  
    
    // Ubah judul modal  
    $('#tambahArtikelModalLabel').text('Tambah Artikel Baru');  
    $('#simpanArtikel').data('mode', 'tambah');  
});  

// Handler untuk tombol Edit di tabel  
$('.edit-btn-artikel').on('click', function() {  
    const artikelId = $(this).data('id');  
    
    // Ambil detail artikel  
    Swal.fire({  
title: "Perbarui Artikel?",  
text: "Pastikan perubahan sudah benar",  
icon: "question",  
showCancelButton: true,  
confirmButtonColor: "#3085d6",  
cancelButtonColor: "#d33",  
confirmButtonText: "Ya, Perbarui!",  
}).then((result) => {  
if (result.isConfirmed) {  
  $.ajax({  
        url: './Backend/artikel/get_artikel_detail.php',  
        method: 'POST',  
        contentType: 'application/json',  
        data: JSON.stringify({ id_artikel: artikelId }),  
        dataType: 'json',  
        success: function(response) {  
            if (response.status === 'success') {  
                const artikel = response.data;  
                
                // Isi form  
                $('#author').val(artikel.author);  
                $('#judul').val(artikel.judul);  
                $('#konten').summernote('code', artikel.content);  
                
                // Ubah judul modal  
                $('#tambahArtikelModalLabel').text('Edit Artikel');  
                
                // Set mode edit dan simpan ID artikel  
                $('#simpanArtikel').data('mode', 'edit');  
                $('#simpanArtikel').data('id', artikelId);  
                
                // Tampilkan modal  
                $('#tambahArtikelModal').modal('show');  
            }  
        },  
        error: function(xhr) {  
            const errorMsg = xhr.responseJSON   
                ? xhr.responseJSON.message   
                : 'Gagal mengambil detail artikel';  
            
            Swal.fire({  
                icon: 'error',  
                title: 'Kesalahan',  
                text: errorMsg  
            });  
        }  
    });   
}  
});
   
});  

// Handler untuk tombol Simpan Artikel  
$('#simpanArtikel').on('click', function() {  
// Validasi form  
if (!$('#formTambahArtikel')[0].checkValidity()) {  
    $('#formTambahArtikel')[0].reportValidity();  
    return;  
}  

const mode = $(this).data('mode');  
const data = {  
    author: $('#author').val(),  
    judul: $('#judul').val(),  
    konten: $('#konten').summernote('code')  
};  

// Tambahkan ID artikel jika mode edit  
if (mode === 'edit') {  
    data.id_artikel = $(this).data('id');  
}  

// Tentukan URL berdasarkan mode  
const url = mode === 'edit'   
    ? './Backend/artikel/update_artikel.php'   
    : './Backend/artikel/proses_artikel.php';  

// Konfirmasi Simpan  
Swal.fire({  
    title: "Simpan Artikel?",  
    text: "Pastikan semua informasi sudah benar",  
    icon: "question",  
    showCancelButton: true,  
    confirmButtonColor: "#3085d6",  
    cancelButtonColor: "#d33",  
    confirmButtonText: "Ya, Simpan!",  
}).then((result) => {  
    if (result.isConfirmed) {  
        // Kirim request  
        $.ajax({  
            url: url,  
            method: 'POST',  
            contentType: 'application/json',  
            data: JSON.stringify(data),  
            dataType: 'json',  
            success: function(response) {  
                if (response.status === 'success') {  
                    Swal.fire({  
                        icon: 'success',  
                        title: 'Berhasil!',  
                        text: response.message,  
                        timer: 2000,  
                        showConfirmButton: false  
                    });  
                    
                    // Tutup modal  
                    $('#tambahArtikelModal').modal('hide'); // Menutup modal

                    window.location.reload();
                    
                    // Reload tabel  
                    $('#userTable').DataTable().ajax.reload(null, false);  
                }  
            },  
            error: function(xhr) {  
                const errorMsg = xhr.responseJSON   
                    ? xhr.responseJSON.message   
                    : 'Gagal menyimpan artikel';  
                
                Swal.fire({  
                    icon: 'error',  
                    title: 'Kesalahan',  
                    text: errorMsg  
                });  
            }  
        });  
    }  
});  
});

        // Hapus Artikel  
$('.delete-btn-artikel').on('click', function() {  
const artikelId = $(this).data('id');  

// Konfirmasi penghapusan  
Swal.fire({  
    title: 'Hapus Artikel?',  
    text: 'Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan.',  
    icon: 'warning',  
    showCancelButton: true,  
    confirmButtonColor: '#d33',  
    cancelButtonColor: '#3085d6',  
    confirmButtonText: 'Ya, Hapus!',  
    cancelButtonText: 'Batal'  
}).then((result) => {  
    if (result.isConfirmed) {  
        // Kirim request AJAX untuk menghapus artikel  
        $.ajax({  
            url: './Backend/artikel/hapus_artikel.php',  
            method: 'POST', // Atau bisa diganti dengan DELETE  
            contentType: 'application/json',  
            data: JSON.stringify({  
                id_artikel: artikelId  
            }),  
            dataType: 'json',  
            success: function(response) {  
                // Tampilkan pesan sukses  
                Swal.fire({  
                    icon: 'success',  
                    title: 'Berhasil!',  
                    text: response.message || 'Artikel berhasil dihapus'  
                });  

                // Reload DataTable  
                $('#userTable').DataTable().ajax.reload(null, false);  
            },  
            error: function(xhr, status, error) {  
                // Parse error response  
                let errorMessage = 'Gagal menghapus artikel';  
                try {  
                    const response = JSON.parse(xhr.responseText);  
                    errorMessage = response.message || errorMessage;  
                } catch(e) {  
                    errorMessage = xhr.responseText || errorMessage;  
                }  

                // Tampilkan pesan error  
                Swal.fire({  
                    icon: 'error',  
                    title: 'Kesalahan',  
                    text: errorMessage  
                });  

                // Log error ke console  
                console.error('Hapus Artikel Error:', error, xhr.responseText);  
            }  
        });  
    }  
});  
}); 

        // Lihat Detail Artikel  
        $('.view-btn-artikel').on('click', function() {  
            const judulartikel = $(this).data('judul');  
            // Tambahkan logika tampilkan detail artikel  
            window.location.href = `./Backend/artikel/detail_artikel.php?artikel=${judulartikel}`;  
        });  
    }  
});  
});