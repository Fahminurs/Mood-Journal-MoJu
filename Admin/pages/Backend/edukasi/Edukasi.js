$(document).ready(function () {  
    const table = $("#userTable-edukasi").DataTable({  
        processing: true,  
        serverSide: true,  
        ajax: {  
            url: './Backend/edukasi/get_edukasi.php',   
            type: 'GET',  
            error: function (xhr, error, thrown) {  
                console.error('DataTables error:', error);  
                Swal.fire({  
                    icon: 'error',  
                    title: 'Gagal Memuat Data',  
                    text: 'Terjadi kesalahan saat mengambil data edukasi'  
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
            searchPlaceholder: "Cari Data Edukasi",  
            zeroRecords: "Tidak ada data edukasi yang cocok ditemukan",  
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
          
            // Tambah Edukasi  
            $('#tambahEdukasiBtn').on('click', function() {  
                // Reset form  
                $('#formTambahEdukasi')[0].reset();  
                $('#kontenEdukasi').summernote('code', '');  
                
                // Ubah judul modal  
                $('#tambahEdukasiModalLabel').text('Tambah Edukasi Baru');  
                $('#simpanEdukasi').data('mode', 'tambah');  
            });  

            // Handler untuk tombol Edit di tabel  
            $('.edit-btn-edukasi').on('click', function() {  
                const edukasiId = $(this).data('id');  
                
                // Ambil detail edukasi  
                Swal.fire({  
                    title: "Perbarui Edukasi?",  
                    text: "Pastikan perubahan sudah benar",  
                    icon: "question",  
                    showCancelButton: true,  
                    confirmButtonColor: "#3085d6",  
                    cancelButtonColor: "#d33",  
                    confirmButtonText: "Ya, Perbarui!",  
                }).then((result) => {  
                    if (result.isConfirmed) {  
                        $.ajax({  
                            url: './Backend/edukasi/get_edukasi_detail.php',  
                            method: 'POST',  
                            contentType: 'application/json',  
                            data: JSON.stringify({ id_edukasi: edukasiId }),  
                            dataType: 'json',  
                            success: function(response) {  
                                if (response.status === 'success') {  
                                    const edukasi = response.data;  
                                    
                                    // Isi form  
                                    $('#authorEdukasi').val(edukasi.author);  
                                    $('#judulEdukasi').val(edukasi.judul);  
                                    $('#kontenEdukasi').summernote('code', edukasi.content);  
                                    
                                    // Ubah judul modal  
                                    $('#tambahEdukasiModalLabel').text('Edit Edukasi');  
                                    
                                    // Set mode edit dan simpan ID edukasi  
                                    $('#simpanEdukasi').data('mode', 'edit');  
                                    $('#simpanEdukasi').data('id', edukasiId);  
                                    
                                    // Tampilkan modal  
                                    $('#tambahEdukasiModal').modal('show');  
                                }  
                            },  
                            error: function(xhr) {  
                                const errorMsg = xhr.responseJSON   
                                    ? xhr.responseJSON.message   
                                    : 'Gagal mengambil detail edukasi';  
                                
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

            // Handler untuk tombol Simpan Edukasi  
            $('#simpanEdukasi').on('click', function() {  
                // Validasi form  
                if (!$('#formTambahEdukasi')[0].checkValidity()) {  
                    $('#formTambahEdukasi')[0].reportValidity();  
                    return;  
                }  

                const mode = $(this).data('mode');  
                const data = {  
                    author: $('#authorEdukasi').val(),  
                    judul: $('#judulEdukasi').val(),  
                    konten: $('#kontenEdukasi').summernote('code')  
                };  

                // Tambahkan ID edukasi jika mode edit  
                if (mode === 'edit') {  
                    data.id_edukasi = $(this).data('id');  
                }  

                // Tentukan URL berdasarkan mode  
                const url = mode === 'edit'   
                    ? './Backend/edukasi/update_edukasi.php'   
                    : './Backend/edukasi/proses_edukasi.php';  

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
                                    $('#tambahEdukasiModal').modal('hide');  

                                    // Hapus overlay modal-backdrop secara eksplisit
                                    window.location.reload();
                                
                                    // Reset scroll body jika diperlukan
                                    // Reload tabel  
                                    $('#userTable-edukasi').DataTable().ajax.reload(null, false);  
                                }  
                            },  
                            error: function(xhr) {  
                                const errorMsg = xhr.responseJSON   
                                    ? xhr.responseJSON.message   
                                    : 'Gagal menyimpan edukasi';  
                                
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

            // Hapus Edukasi  
            $('.delete-btn-edukasi').on('click', function() {  
                const edukasiId = $(this).data('id');  
                
                // Konfirmasi penghapusan  
                Swal.fire({  
                    title: 'Hapus Edukasi?',  
                    text: 'Anda yakin ingin menghapus edukasi ini? Tindakan ini tidak dapat dibatalkan.',  
                    icon: 'warning',  
                    showCancelButton: true,  
                    confirmButtonColor: '#d33',  
                    cancelButtonColor: '#3085d6',  
                    confirmButtonText: 'Ya, Hapus!',  
                    cancelButtonText: 'Batal'  
                }).then((result) => {  
                    if (result.isConfirmed) {  
                        // Kirim request AJAX untuk menghapus edukasi  
                        $.ajax({  
                            url: './Backend/edukasi/hapus_edukasi.php',  
                            method: 'POST',   
                            contentType: 'application/json',  
                            data: JSON.stringify({  
                                id_edukasi: edukasiId  
                            }),  
                            dataType: 'json',  
                            success: function(response) {  
                                // Tampilkan pesan sukses  
                                Swal.fire({  
                                    icon: 'success',  
                                    title: 'Berhasil!',  
                                    text: response.message || 'Edukasi berhasil dihapus'  
                                });  

                                // Reload DataTable  
                                $('#userTable-edukasi').DataTable().ajax.reload(null, false);  
                            },  
                            error: function(xhr, status, error) {  
                                // Parse error response  
                                let errorMessage = 'Gagal menghapus edukasi';  
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
                                console.error('Hapus Edukasi Error:', error, xhr.responseText);  
                            }  
                        });  
                    }  
                });  
            });  

            // Lihat Detail Edukasi  
            $('.view-btn-edukasi').on('click', function() {  
                const judulEdukasi = $(this).data('judul');  
                // Tambahkan logika tampilkan detail edukasi  
                window.location.href = `./Backend/edukasi/detail_edukasi.php?edukasi=${judulEdukasi}`;  
            });  
        }  
    });  
}); 