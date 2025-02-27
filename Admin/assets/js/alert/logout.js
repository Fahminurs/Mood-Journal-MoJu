
// Add event listener to the logout button
document.getElementById('logout').addEventListener('click', function (event) {
    event.preventDefault(); // Prevent the default action

    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Anda akan keluar dari akun Anda.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Keluar!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to the logout URL if confirmed
            window.location.href = '../../logout.php';
        }
    });
});
