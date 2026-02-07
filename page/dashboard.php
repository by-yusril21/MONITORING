<style>
  /* --- SETUP GLOBAL --- */
  .content-wrapper { background-color: #f4f6f9 !important; }
  .content-header { display: none; }

  /* --- KUNCI AGAR SEJAJAR RAPI (FORCED HEIGHT) --- */
  /* Memaksa semua elemen input dan tombol memiliki tinggi 32px */
  .dataTables_filter input, 
  .dataTables_length select, 
  .dt-buttons .btn,
  #pilihUnit, 
  #pilihMotor, 
  #btnRefresh {
      height: 32px !important;
      line-height: 1.5 !important;
      padding-top: 3px !important;
      padding-bottom: 3px !important;
      font-size: 14px !important;
      vertical-align: middle !important;
      border-radius: 4px !important;
  }

  /* --- JARAK ANTAR ELEMEN --- */
  .dataTables_wrapper .row:first-child {
      padding: 8px 10px;
      background-color: white;
      border-bottom: 1px solid #dee2e6;
  }

  /* Margin Kanan (Area Search & Excel) */
  .dataTables_filter input { margin-left: 10px !important; width: 150px !important; display: inline-block !important; }
  .dataTables_length select { margin: 0 5px !important; display: inline-block !important; }
  .dt-buttons { margin-left: 10px !important; }

  /* Margin Kiri (Area Filter) */
  .custom-toolbar-item { margin-right: 5px; }

  /* Reset Label */
  .dataTables_filter label, .dataTables_length label {
      margin-bottom: 0 !important;
      font-weight: normal !important;
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

<div class="content-wrapper" style="background-color: #f4f6f9;">

  <div id="my-filter-source" class="d-none">
      <div class="d-flex align-items-center">
          
          <select id="pilihUnit" class="form-control custom-select-sm custom-toolbar-item" style="width: 150px;">
              <option value="">-- Pilih Unit --</option>
              <option value="C6KV">PLTU UNIT C 6KV</option>
              <option value="C380">PLTU UNIT C 380</option>
              <option value="D6KV">PLTU UNIT D 6KV</option>
              <option value="D380">PLTU UNIT D 380</option>
              <option value="UTILITY">PLTU UNIT UTILITY</option>
          </select>

          <select id="pilihMotor" class="form-control custom-select-sm custom-toolbar-item" style="width: 220px;" disabled>
              <option value="">-- Pilih Motor --</option>
          </select>

          <button type="button" id="btnRefresh" class="btn btn-info btn-sm custom-toolbar-item" title="Refresh Data">
              <i class="fas fa-sync-alt"></i>
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
                <table id="example1" class="table table-bordered table-striped table-hover text-nowrap m-0">
                  <thead class="bg-primary text-white">
                    <tr>
                      <?php foreach ($columns as $col): ?>
                        <th class="<?php echo ($col == 'No' || $col == 'ACTIONS') ? 'text-center' : ''; ?>" 
                            <?php echo ($col == 'No') ? 'style="width: 50px;"' : ''; ?>>
                            <?= $col ?>
                        </th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center">1</td>
                      <td>2023-10-27 08:00:00</td>
                      <td>teknisi1@tonasa.co.id</td>
                      <td>Unit A</td>
                      <td>Sect-01</td>
                      <td>Normal</td>
                      <td>65°C</td>
                      <td>62°C</td>
                      <td>30°C</td>
                      <td>800 kW</td>
                      <td>50%</td>
                      <td>120 A</td>
                      <td>Halus</td>
                      <td>Start</td>
                      <td>Lengkap</td>
                      <td>Bersih</td>
                      <td>OK</td>
                      <td>Done</td>
                      <td class="text-center"><span class="badge badge-success">Tercatat</span></td>
                    </tr>
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