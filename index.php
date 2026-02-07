<?php
session_start();
// Inisialisasi variabel notifikasi
$delete = false;
$deleteTerminal = false;
$reset_id = false;

// 1. Cek Login
if (!isset($_SESSION['username'])) {
  echo "<script> location.href='login.php'; </script>";
  exit;
}

// 2. Include Konfigurasi & Layout
include "config/database.php";
include "inc/header.php";
include "inc/navbar.php";
include "inc/sidebar.php";
include "inc/alerts.php";

// 3. Logika Routing Halaman
if (isset($_GET['page'])) {
  $page = $_GET['page'];
  if (file_exists("page/" . $page . ".php")) {
    include "page/" . $page . ".php";
  } else {
    include "page/dashboard.php";
  }
} else {
  include "page/dashboard.php";
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
  echo "<script>toastr.success('Data sensor berhasil dihapus.');</script>";
} else if ($deleteTerminal == true) {
  echo "<script>toastr.success('Data terminal berhasil dihapus.');</script>";
} else if ($reset_id == true) {
  echo "<script>toastr.success('Data di-reset.');</script>";
}
?>

<script>
  // 1. DATA MOTOR
  const dataMotor = {
    "6KV": [
      "BOILER FEED WATER PUMP A", "BOILER FEED WATER PUMP B", "COAL MILL C",
      "FORCED DRAFT FAN C", "PULVERIZED FAN C", "INDUCED DRAFT FAN C",
      "VENT GAS FAN C", "SEA WATER INTAKE PUMP A", "SEA WATER INTAKE PUMP C"
    ],
    "380": [
      "EJECTOR PUMP A", "EJECTOR PUMP B", "PULVERIZED COAL FAN C",
      "MILL SEAL AIR FAN C", "CONDENSATE PUMP A", "CONDENSATE PUMP B",
      "IGNITER AIR FAN C", "BLOWER PFISTER C", "GAS AIR HEATER C"
    ]
  };

  $(document).ready(function () {

    // --- KONFIGURASI DATATABLES ---
    var table = $("#example1").DataTable({
      // DOM CONFIG: Satu baris rapi
      // align-items-center: Wajib agar vertikal rata tengah
      "dom": "<'row m-0 bg-white border-bottom p-2 align-items-center'" +
        "<'col-md-7 d-flex align-items-center' <'#my-filter-placeholder'>>" +
        "<'col-md-5 d-flex align-items-center justify-content-end' f l B>>" +
        "<'row m-0'<'col-12 p-0'tr>>" +
        "<'row m-0 p-2 bg-white'<'col-md-5'i><'col-md-7'p>>",

      "responsive": false,
      "scrollX": true,
      "lengthChange": true,
      "autoWidth": false,
      "searching": true,
      "paging": true,
      "info": true,
      "buttons": [
        {
          extend: 'excel',
          text: 'Download Excel', // Teks Saja
          titleAttr: 'Download Excel',
          className: 'btn btn-success btn-sm' // btn-sm + CSS height:32px = SEJAJAR
        }
      ],
      "language": {
        "search": "",
        "searchPlaceholder": "Cari...",
        "lengthMenu": "_MENU_",
        "info": "Show _START_-_END_ of _TOTAL_",
        "paginate": { "previous": "<", "next": ">" }
      },

      // Saat tabel siap, pindahkan filter ke Header
      "initComplete": function () {
        var filterContent = $("#my-filter-source").html();
        $("#my-filter-placeholder").html(filterContent);
        $("#my-filter-source").remove();

        // Jalankan logika dropdown setelah elemen dipindah
        bindFilterEvents();
      }
    });

    // --- FUNGSI LOGIKA FILTER DROPDOWN ---
    function bindFilterEvents() {
      const unitSelect = $('#pilihUnit');
      const motorSelect = $('#pilihMotor');

      function populateMotor(unit, selectedMotor = null) {
        motorSelect.empty();
        if (unit && dataMotor[unit]) {
          motorSelect.prop('disabled', false);
          motorSelect.append('<option value="">-- Pilih Motor --</option>');
          dataMotor[unit].forEach(function (motorName) {
            const isSelected = (selectedMotor === motorName) ? 'selected' : '';
            motorSelect.append(`<option value="${motorName}" ${isSelected}>${motorName}</option>`);
          });
        } else {
          motorSelect.prop('disabled', true);
          motorSelect.append('<option value="">-- Pilih Motor --</option>');
        }
      }

      unitSelect.change(function () {
        const val = $(this).val();
        populateMotor(val);
        localStorage.setItem('mon_selectedUnit', val);
        localStorage.removeItem('mon_selectedMotor');
      });

      motorSelect.change(function () {
        localStorage.setItem('mon_selectedMotor', $(this).val());
      });

      $('#btnRefresh').click(function () {
        window.location.reload();
      });

      // LOAD SAVED DATA (Agar tidak hilang saat refresh)
      const savedUnit = localStorage.getItem('mon_selectedUnit');
      const savedMotor = localStorage.getItem('mon_selectedMotor');

      if (savedUnit) {
        unitSelect.val(savedUnit);
        populateMotor(savedUnit, savedMotor);
      }
    }

  });
</script>

</body>

</html>