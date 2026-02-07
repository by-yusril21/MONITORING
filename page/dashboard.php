<style>
  /* HANYA 2 BARIS CSS TAMBAHAN */
  /* Memberi jarak kanan pada tombol Excel dan Show Entries */
  .dt-buttons, .dataTables_length { margin-right: 15px !important; }
  /* Menyembunyikan judul halaman bawaan */
  .content-header { display: none; }
</style>

<?php
// ARRAY JUDUL KOLOM (Agar tidak perlu menulis tag <th> 19 kali)
$columns = [
  "No", "TIMESTAMP", "EMAIL ADDRESS", "PILIH SALAH SATU", "SECTION NO", 
  "VIBRASI/GETARAN", "TEMP. BEARING DE", "TEMP. BEARING NDE", "SUHU RUANGAN", 
  "BEBAN GENERATOR", "OPENING DAMPER", "LOAD CURRENT", "BUNYI MOTOR", 
  "PANEL LOCAL", "KELENGKAPAN", "KEBERSIHAN", "GROUNDING", "REGREASING", "ACTIONS"
];
?>

<div class="content-wrapper" style="background-color: #f4f6f9;">
  
  <section class="content pt-2 px-2">
    <div class="container-fluid p-0">
      <div class="row m-0">
        <div class="col-12 p-0">
          
          <div class="card card-primary card-outline m-0 border shadow-none">
            
            <div class="card-body p-0 bg-white">
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
                      <td class="text-center">
                        <span class="badge badge-success">Tercatat</span>
                      </td>
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