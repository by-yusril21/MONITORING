<?php
session_start();
// Inisialisasi variabel notifikasi
$delete = false;
$deleteTerminal = false;
$reset_id = false;

// 1. Cek Login (Wajib ada di paling atas)
if (!isset($_SESSION['username'])) {
  echo "<script> location.href='login.php'; </script>";
  exit;
}

// 2. Include Konfigurasi Database & Bagian Layout
include "config/database.php";
include "inc/header.php";   // Pastikan file ini memuat CSS AdminLTE & DataTables
include "inc/navbar.php";
include "inc/sidebar.php";
include "inc/alerts.php";

// 3. Logika Halaman Dinamis (Routing)
if (isset($_GET['page'])) {
  $page = $_GET['page'];
  // Cek apakah file page benar-benar ada
  if (file_exists("page/" . $page . ".php")) {
    include "page/" . $page . ".php";
  } else {
    include "page/dashboard.php"; // Halaman default jika tidak ketemu
  }
} else {
  include "page/dashboard.php"; // Halaman default awal
}
?>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script src="plugins/toastr/toastr.min.js"></script>

<script src="dist/js/adminlte.min.js"></script>

<?php
if ($delete == true) {
  echo "<script>toastr.success('Data berhasil dihapus.');</script>";
} else if ($reset_id == true) {
  echo "<script>toastr.success('Data di-reset.');</script>";
}
?>

<script>
  $(function () {
    $("#example1").DataTable({
      // KONFIGURASI TAMPILAN (DOM) DENGAN CLASS BOOTSTRAP:
      // - row m-0: Baris tanpa margin
      // - bg-white: Latar putih
      // - p-2: Padding (jarak dalam) sekitar 8px
      // - border-bottom: Garis pemisah di bawah tombol
      // - d-flex align-items-center: Membuat tombol Excel, Show entries, Search sejajar satu baris

      "dom": "<'row m-0 bg-white border-bottom p-2'<'col-12 d-flex align-items-center' B l f>>" +
        "<'row m-0'<'col-12 p-0'tr>>" +
        "<'row m-0 p-2 bg-white'<'col-md-5'i><'col-md-7'p>>",

      "responsive": false,
      "scrollX": true,
      "lengthChange": true,
      "autoWidth": false,
      "searching": true,
      "paging": true,
      "info": true,

      // Konfigurasi Tombol Excel
      "buttons": [
        {
          extend: 'excel',
          text: '<i class="fas fa-file-excel"></i> Excel',
          className: 'btn btn-success btn-sm'
        }
      ],

      // Bahasa Indonesia
      "language": {
        "search": "", // Hapus label "Search" biar hemat tempat
        "searchPlaceholder": "Cari data...",
        "lengthMenu": "_MENU_", // Hapus label "Show entries"
        "info": "Menampilkan _START_ sd _END_ dari _TOTAL_ data",
        "paginate": {
          "previous": "Sebelumnya",
          "next": "Selanjutnya"
        }
      }
    });
  });
</script>

</body>

</html>