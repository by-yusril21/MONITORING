<style>
  /* --- SETUP GLOBAL --- */
  .content-wrapper { background-color: #f4f6f9 !important; }
  .content-header { display: none; }
  .main-header { border-bottom: none !important; box-shadow: none !important; }

  /* --- LAYOUT TOOLBAR --- */
  .dataTables_wrapper .row:first-child {
      padding: 10px;
      background-color: white;
      border-bottom: none !important;
      display: flex;
      align-items: center;
      flex-wrap: wrap; 
  }

  /* --- ELEMEN TOOLBAR --- */
  .dataTables_length, .dataTables_filter, .dt-buttons {
      display: inline-block !important;
      margin-bottom: 0 !important;
      vertical-align: middle;
  }
  .dataTables_filter { margin-right: 10px !important; }
  .dataTables_length { margin-right: 10px !important; }

  .form-control-sm, .btn-sm, .custom-select-sm, 
  .dataTables_filter input, .dataTables_length select {
      height: 32px !important; 
      line-height: 1.5; 
      font-size: 14px !important; 
  }

  /* DROPDOWN TETAP BESAR (15px Bold) */
  #pilihUnit, #pilihMotor {
      font-weight: bold !important;
      color: #000 !important;
      font-size: 15px !important; 
      height: 34px !important;   
  }

  /* --- RESET CSS DATATABLES --- */
  table.dataTable { margin-top: 0 !important; margin-bottom: 0 !important; border-collapse: collapse !important; }
  .dataTables_scrollHeadInner { padding-left: 0 !important; padding-right: 0 !important; }

  /* --- HEADER ABU-ABU & GARIS TEGAS --- */
  .table-bordered { border: 1px solid #888 !important; }
  .table-bordered th, .table-bordered td { border: 1px solid #888 !important; }

  .table thead th {
      vertical-align: middle !important;
      background-color: #b2b2b2 !important; 
      color: #000 !important;               
      font-weight: bold !important;
      text-align: center;
      padding: 0px 15px !important; 
      font-size: 14px !important;
      white-space: nowrap !important;
  }

  /* --- ISI TABEL & EFEK HOVER --- */
  .table tbody td {
      vertical-align: middle !important;
      padding: 4px 10px !important; 
      color: #333;
      font-size: 14px !important;
  }

  /* [KUNCI PERBAIKAN] KOLOM ACTIONS TIDAK MELEBAR */
  /* Target kolom terakhir agar bisa pindah baris (wrapping) */
  #example1 td:last-child, 
  #example1 th:last-child {
      white-space: normal !important; /* Batalkan text-nowrap */
      min-width: 400px !important;    /* Lebar minimal kolom */
      max-width: 550px !important;    /* Batasan lebar maksimal sebelum pindah baris */
      word-break: break-word;         /* Potong kata jika terlalu panjang */
      line-height: 1.4 !important;    /* Beri jarak antar baris teks */
  }

  /* HOVER ABU KEBIRUAN */
  .table-hover tbody tr:hover {
      background-color: #d1dbe5 !important;
      transition: background-color 0.1s ease-in-out;
  }

  /* Kolom No Rata Kiri */
  table.dataTable tbody td:first-child { text-align: left !important; padding-left: 20px !important; }

  /* Label Entries & Search */
  .dataTables_length label, .dataTables_filter label {
    font-weight: normal !important;
    margin-bottom: 0 !important;
    font-size: 14px !important;
  }

  /* Pastikan area filter tidak tertutup elemen lain */
  #my-filter-placeholder {
      position: relative;
      z-index: 10;
  }

  #btnRefresh {
      position: relative;
      z-index: 11;
      cursor: pointer;
      white-space: nowrap;
  }

  /* Memastikan toolbar DataTables tidak saling tumpang tindih */
  .dataTables_wrapper .row {
      width: 100%;
      margin: 0;
  }
</style>

<?php
$columns = [
  "No", "TIMESTAMP", "EMAIL ADDRESS", "PILIH SALAH SATU", "SECTION NO", 
  "VIBRASI/GETARAN", "TEMP. BEARING DE", "TEMP. BEARING NDE", "SUHU RUANGAN", 
  "BEBAN GENERATOR", "OPENING DAMPER", "LOAD CURRENT", "BUNYI MOTOR", 
  "PANEL LOCAL", "KELENGKAPAN", "KEBERSIHAN", "GROUNDING", "REGREASING", "ACTIONS"
];
?>

<div class="content-wrapper">

  <div id="my-filter-source" class="d-none">
      <div class="d-inline-flex align-items-center">
          <select id="pilihUnit" class="form-control form-control-sm mr-2" style="width: 160px;">
              <option value="">-- Pilih Unit --</option>
              <option value="C6KV">PLTU UNIT C 6KV</option>
              <option value="C380">PLTU UNIT C 380</option>
              <option value="D6KV">PLTU UNIT D 6KV</option>
              <option value="D380">PLTU UNIT D 380</option>
              <option value="UTILITY">PLTU UNIT UTILITY</option>
          </select>

          <select id="pilihMotor" class="form-control form-control-sm mr-2" style="width: 230px;" disabled>
              <option value="">-- Pilih Motor --</option>
          </select>

          <button type="button" id="btnRefresh" class="btn btn-info btn-sm" title="Refresh Data">
           Update
          </button>
      </div>
  </div>

  <section class="content pt-2 px-2">
    <div class="container-fluid p-0">
      <div class="row m-0">
        <div class="col-12 p-0">
          
          <div class="card card-primary card-outline m-0 border shadow-none">
            <div class="card-body bg-white p-0">
              <div class="table-responsive">
                
                <table id="example1" class="table table-bordered table-striped table-hover table-sm text-nowrap m-0">
                  <thead>
                    <tr>
                      <?php foreach ($columns as $col): ?>
                        <th class="<?php echo ($col == 'ACTIONS') ? 'text-center' : ''; ?>" 
                            <?php echo ($col == 'No') ? 'style="width: 50px;"' : ''; ?>>
                            <?= $col ?>
                        </th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>

              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>