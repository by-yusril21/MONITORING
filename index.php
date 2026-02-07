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

<!-- https://script.google.com/macros/s/AKfycbxEad_-Wnmvrc5is1POvpdkr7OVEegX7KtNZlql1vhRukATPGgu2LLHza_dJXG2Qw/exec -->
<script>
  // --- KONFIGURASI GOOGLE SHEET ---
  const GOOGLE_SCRIPT_URL = "https://script.google.com/macros/s/AKfycbxEad_-Wnmvrc5is1POvpdkr7OVEegX7KtNZlql1vhRukATPGgu2LLHza_dJXG2Qw/exec";
  const API_TOKEN = "SemenTonasa2026";

  // 1. DATA MOTOR
  const dataMotor = {
    "6KV": [
      "BOILER FEED WATER PUMP A",
      "BOILER FEED WATER PUMP B",
      "COAL MILL C",
      "FORCED DRAFT FAN C",
      "PULVERIZED FAN C",
      "INDUCED DRAFT FAN C",
      "VENT GAS FAN C",
      "SEA WATER INTAKE PUMP A",
      "SEA WATER INTAKE PUMP C"
    ],
    "380": [
      "EJECTOR PUMP A",
      "EJECTOR PUMP B",
      "PULVERIZED COAL FAN C",
      "MILL SEAL AIR FAN C",
      "CONDENSATE PUMP A",
      "CONDENSATE PUMP B",
      "IGNITER AIR FAN C",
      "BLOWER PFISTER C",
      "GAS AIR HEATER C"
    ]
  };

  $(document).ready(function () {

    // --- INISIALISASI DATATABLES ---
    $("#example1").DataTable({
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
      "processing": true,
      "ordering": true,
      "order": [[0, "asc"]], // Data lama di atas (No 1), baru di bawah

      // [TAMBAHAN BARU] MENU PILIHAN JUMLAH DATA (Ada angka 5)
      "lengthMenu": [
        [5, 10, 25, 50, -1],
        [5, 10, 25, 50, "Semua"]
      ],

      "buttons": [
        {
          extend: 'excel',
          text: 'Download Excel',
          className: 'btn btn-success btn-sm'
        }
      ],
      "language": {
        "search": "",
        "searchPlaceholder": "Cari data...",
        "lengthMenu": "_MENU_", // Label dropdown hanya angka
        "processing": "<i class='fas fa-spinner fa-spin'></i> Mengambil Data...",
        "emptyTable": "Silakan Pilih Unit dan Motor terlebih dahulu"
      },
      "initComplete": function () {
        var filterContent = $("#my-filter-source").html();
        if (filterContent) {
          $("#my-filter-placeholder").html(filterContent);
          $("#my-filter-source").remove();
          bindFilterEvents();
        }
      }
    });

    // Event Listener: Saat user mengganti jumlah "Show Entries"
    $('#example1').on('length.dt', function (e, settings, len) {
      const currentMotor = localStorage.getItem('mon_selectedMotor');
      if (currentMotor) {
        loadDataFromSheet(currentMotor);
      }
    });

    // --- FUNGSI LOAD DATA ---
    function loadDataFromSheet(sheetName) {
      if (!sheetName) return;

      var dt = $("#example1").DataTable();

      // Ambil Limit Data sesuai pilihan "Show Entries"
      var limitData = dt.page.len();

      dt.clear().draw();

      const url = `${GOOGLE_SCRIPT_URL}?token=${API_TOKEN}&sheet=${encodeURIComponent(sheetName)}`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (!Array.isArray(data) || data.length === 0) {
            toastr.warning("Sheet kosong atau nama sheet tidak sesuai.");
            return;
          }

          const headers = data[0];
          var rows = data.slice(1);

          if (rows.length === 0) {
            toastr.info("Data belum tersedia.");
            return;
          }

          // --- LOGIKA PENGAMBILAN DATA (DATA TERBARU DI BAWAH) ---
          // Jika user pilih "Semua" (-1), limitData biasanya -1

          if (limitData > 0 && rows.length > limitData) {
            // Ambil data TERAKHIR (Slice dari ujung) agar yang tampil data terbaru
            rows = rows.slice(rows.length - limitData);
            toastr.info(`Menampilkan ${limitData} data terakhir.`);
          } else {
            toastr.success(`Memuat seluruh ${rows.length} data.`);
          }

          // MAPPING KOLOM
          const idxTime = getColIndex(headers, "Timestamp");
          const idxEmail = getColIndex(headers, "Email");
          const idxUnit = getColIndex(headers, "PILIH SALAH SATU");
          const idxSection1 = getColIndex(headers, "SECTION NO");
          const idxSection2 = getColIndex(headers, "SECTION NO 2");

          const idxVibrasi = getColIndex(headers, "VIBRASI");
          const idxTempDE = getColIndex(headers, ["TEMPERATURE BEARING DE", "TEMP. BEARING DE"]);
          const idxTempNDE = getColIndex(headers, ["TEMPERATURE BEARING NDE", "TEMP. BEARING NDE"]);
          const idxSuhu = getColIndex(headers, ["SUHU RUANGAN", "VENTILASI"]);
          const idxBeban = getColIndex(headers, "BEBAN GENERATOR");
          const idxDamper = getColIndex(headers, "OPENING DAMPER");
          const idxCurrent = getColIndex(headers, "LOAD CURRENT");
          const idxBunyi = getColIndex(headers, "BUNYI MOTOR");
          const idxPanel = getColIndex(headers, "PANEL LOCAL");
          const idxKelengkapan = getColIndex(headers, "KELENGKAPAN");
          const idxKebersihan = getColIndex(headers, "KEBERSIHAN");
          const idxGrounding = getColIndex(headers, ["GROUNDING", "PENTANAHAN"]);
          const idxRegreasing = getColIndex(headers, "REGREASING");
          const idxAction = getColIndex(headers, "ACTIONS");

          let formattedData = [];

          rows.forEach((row, index) => {
            let valSection = safeGet(row, idxSection1);
            if (!valSection || valSection === "-" || valSection === "") {
              valSection = safeGet(row, idxSection2);
            }

            formattedData.push([
              index + 1, // No Urut
              safeGet(row, idxTime),
              safeGet(row, idxEmail),
              safeGet(row, idxUnit),
              valSection,
              safeGet(row, idxVibrasi),
              safeGet(row, idxTempDE),
              safeGet(row, idxTempNDE),
              safeGet(row, idxSuhu),
              safeGet(row, idxBeban),
              safeGet(row, idxDamper),
              safeGet(row, idxCurrent),
              safeGet(row, idxBunyi),
              safeGet(row, idxPanel),
              safeGet(row, idxKelengkapan),
              safeGet(row, idxKebersihan),
              safeGet(row, idxGrounding),
              safeGet(row, idxRegreasing),
              safeGet(row, idxAction) ? safeGet(row, idxAction) : '<span class="badge badge-success">Tercatat</span>'
            ]);
          });

          dt.rows.add(formattedData).draw();
        })
        .catch(error => {
          console.error('Error Fetching:', error);
          toastr.error("Gagal koneksi ke Google Sheet.");
        });
    }

    // --- HELPER FUNCTIONS ---
    function getColIndex(headers, keywords) {
      if (!Array.isArray(keywords)) keywords = [keywords];
      for (let i = 0; i < headers.length; i++) {
        const h = String(headers[i]).toUpperCase();
        for (let k of keywords) {
          if (h.includes(k.toUpperCase())) return i;
        }
      }
      return -1;
    }

    function safeGet(row, index) {
      if (index < 0 || !row[index]) return "-";
      return row[index];
    }

    // --- LOGIKA DROPDOWN ---
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
        $("#example1").DataTable().clear().draw();
      });

      motorSelect.change(function () {
        const motorName = $(this).val();
        localStorage.setItem('mon_selectedMotor', motorName);
        if (motorName) loadDataFromSheet(motorName);
      });

      $('#btnRefresh').click(function () {
        const currentMotor = motorSelect.val();
        if (currentMotor) {
          loadDataFromSheet(currentMotor);
        } else {
          window.location.reload();
        }
      });

      const savedUnit = localStorage.getItem('mon_selectedUnit');
      const savedMotor = localStorage.getItem('mon_selectedMotor');

      if (savedUnit) {
        unitSelect.val(savedUnit);
        populateMotor(savedUnit, savedMotor);
        if (savedMotor) loadDataFromSheet(savedMotor);
      }
    }
  });
</script>

</body>

</html>