<?php
// Definisi kolom tabel
$columns = [
    "No", "TIMESTAMP", "EMAIL ADDRESS", "PILIH SALAH SATU", "SECTION NO", 
    "VIBRASI/GETARAN", "TEMP. BEARING DE", "TEMP. BEARING NDE", "SUHU RUANGAN", 
    "BEBAN GENERATOR", "OPENING DAMPER", "LOAD CURRENT", "BUNYI MOTOR", 
    "PANEL LOCAL", "KELENGKAPAN", "KEBERSIHAN", "GROUNDING", "REGREASING", "ACTIONS"
];
?>

<style>
    html {
        scroll-behavior: smooth;
    }

    .content-wrapper {
        background-color: #f4f6f9 !important;
        overflow-x: hidden;
    }

    /* Pengaturan Full Screen 100vh */
    .section-full {
        height: 100vh;
        display: flex;
        flex-direction: column;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    /* Jarak presisi 5px dari pinggir layar */
    .px-custom-5 {
        padding-left: 7px !important;
        padding-right: 7px !important;
    }

    /* Table Container Styling */
    .table-responsive-vh {
        flex: 1;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        background-color: white;
    }

    /* Card Styling */
    .card-custom {
        border-radius: 0;
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
        display: flex;
        flex-direction: column;
        height: 100%;
        margin-bottom: 0;
    }

    /* Form Label Styling */
    .form-label-custom {
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 3px;
        display: block;
    }

    /* Console Terminal Styling */
    #consoleStatus {
        font-family: 'Courier New', Courier, monospace;
        background-color: #1a1a1a;
        color: #28a745;
        border: 1px solid #333;
        padding: 10px;
        height: 120px;
        overflow-y: auto;
        font-size: 11px;
        width: 100%;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #b3b3b3;
    }

    html, body {
        -ms-overflow-style: none;  /* IE dan Edge */
        scrollbar-width: none;     /* Firefox */
    }

    body::-webkit-scrollbar {
        display: none;             /* Chrome, Safari, Opera */
    }
    #section-tabel, 
    #section-gauge, 
    #section-input {
        scroll-margin-top: 25px; 
    }
</style>

<div class="content-wrapper">

    <section id="section-tabel" class="section-full">
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
                <button type="button" id="btnRefresh" class="btn btn-info btn-sm">Update</button>
            </div>
        </div>

        <div class="container-fluid px-custom-5 h-100">
            <div class="card card-custom">
                <div class="card-header py-2 bg-white d-flex justify-content-between align-items-center">
                    <h3 id="label-title" class="m-0">
                        DATABASE MONITORING MOTOR
                    </h3>
                </div>
                <div class="card-body p-0 flex-fill d-flex flex-column">
                    <div class="table-responsive-vh">
                        <table id="example1" class="table table-bordered table-striped table-hover table-sm text-nowrap m-0">
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $col): ?>
                                        <th class="<?= ($col == 'ACTIONS') ? 'text-center' : ''; ?>" 
                                            <?= ($col == 'No') ? 'style="width: 50px;"' : ''; ?>>
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
    </section>

<section id="section-input" class="section-full" style="min-height: 100vh;">
    <div class="container-fluid px-custom-5 h-100 py-3">
        <div class="card card-custom bg-white shadow-sm">
            <div class="card-header py-2 bg-light">
                <h3 class="card-title text-sm font-weight-bold">
                    <i class="fas fa-plus-circle mr-1 text-primary"></i> INPUT DATA MONITORING
                </h3>
            </div>

            <form id="formInputMotor" class="p-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="form-label-custom font-weight-bold">PILIH SALAH SATU</label>
                            <select name="pilih_salah_satu" id="pilihTipe" class="form-control form-control-sm border-secondary font-weight-bold">
                                <option value="PREVENTIVE">PREVENTIVE</option>
                                <option value="PREDICTIVE">PREDICTIVE</option>
                                <option value="CORRECTIVE">CORRECTIVE</option>
                                <option value="LAINNYA">LAINNYA</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="form-label-custom font-weight-bold">SECTION NO (AUTO)</label>
                            <input type="text" name="section_no" id="inputSectionNo" class="form-control form-control-sm border-secondary font-weight-bold" style="background-color: #e9ecef;" readonly placeholder="Menunggu Data...">
                        </div>
                    </div> 
                </div>

                <div id="parameterSection">
                    <hr class="my-2">
                   <div class="row">
                    <?php 
                    // Format Array: [name_attribute, Label_Tampilan, tipe_input, opsi_dropdown]
                    $allInputs = [
                        ["vibrasi", "Vibrasi/Getaran", "number"], 
                        ["temp_de", "Temp. Bearing DE", "number"],
                        ["temp_nde", "Temp. Bearing NDE", "number"], 
                        ["suhu_ruang", "Suhu Ruangan", "number"],
                        ["beban_gen", "Beban Generator", "number"], 
                        ["damper", "Opening Damper", "number"],
                        ["load_current", "Load Current", "number"], 
                        // Dropdown
                        ["bunyi", "Bunyi Motor", "select", ["GOOD", "FAIR", "POOR"]],
                        ["panel", "Panel Local", "select", ["GOOD", "FAIR", "POOR"]], 
                        ["lengkap", "Kelengkapan", "select", ["GOOD", "FAIR", "POOR"]],
                        ["bersih", "Kebersihan", "select", ["GOOD", "FAIR", "POOR"]], 
                        ["ground", "Grounding", "select", ["GOOD", "FAIR", "POOR"]],
                        ["regrease", "Regreasing", "select", ["BELUM", "SELESAI"]]
                    ];

                    foreach($allInputs as $item): ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                        <div class="form-group mb-2">
                            <label class="form-label-custom"><?= $item[1] ?></label>
                            
                            <?php if($item[2] == "number"): ?>
                                <input type="number" step="0.01" name="<?= $item[0] ?>" 
                                    class="form-control form-control-sm border-secondary" 
                                    placeholder="-">
                            
                            <?php else: ?>
                                <select name="<?= $item[0] ?>" class="form-control form-control-sm border-secondary">
                                    <option value="" selected disabled>- Pilih -</option>
                                    <?php foreach($item[3] as $opt): ?>
                                        <option value="<?= $opt ?>"><?= $opt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>

                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                </div>

                <hr id="dividerBawah" class="my-2">

                <div class="row align-items-stretch">
                    
                    <div class="col-md-6 d-flex flex-column">
                        <div class="form-group mb-2 d-flex flex-column flex-grow-1">
                            <label class="form-label-custom font-weight-bold">ACTION (KETERANGAN)</label>
                            <textarea name="action" class="form-control form-control-sm border-secondary flex-grow-1" 
                                      style="min-height: 120px;" placeholder="Isi keterangan action di sini..."></textarea>
                        </div>
                        <button type="button" id="btnKirim" class="btn btn-primary btn-sm btn-block shadow-sm font-weight-bold">
                            <i class="fas fa-paper-plane mr-1"></i> KIRIM DATA MONITORING
                        </button>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-0 d-flex flex-column h-100">
                            <label class="form-label-custom text-muted font-weight-bold">
                                <i class="fas fa-terminal mr-1"></i> CONSOLE LOG STATUS
                            </label>
                            <div id="consoleStatus" class="border rounded p-2 bg-dark flex-grow-1" 
                                 style="min-height: 160px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 11px; color: #00ff00;">
                                <div id="log-output">
                                    <div>> [SYSTEM] Dashboard Ready...</div>
                                    <div>> [STATUS] Connection Stable</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pilihTipe = document.getElementById('pilihTipe');
    const parameterSection = document.getElementById('parameterSection');
    const dividerBawah = document.getElementById('dividerBawah');
    const logOutput = document.getElementById('log-output');

    // Fungsi untuk menulis log ke console virtual
    function writeLog(message, type = "INFO") {
        const now = new Date();
        const time = now.getHours().toString().padStart(2, '0') + ":" + 
                     now.getMinutes().toString().padStart(2, '0') + ":" + 
                     now.getSeconds().toString().padStart(2, '0');
        
        const newEntry = document.createElement('div');
        newEntry.innerHTML = `> [${time}] [${type}] ${message}`;
        logOutput.prepend(newEntry);
    }

    // Fungsi utama pengatur tampilan
    function updateFormLayout() {
        const tipe = pilihTipe.value;

        if (tipe === "PREVENTIVE") {
            parameterSection.style.display = "block";
            dividerBawah.style.display = "block";
            writeLog("Mode PREVENTIVE", "SYSTEM");
        } else {
            parameterSection.style.display = "none";
            dividerBawah.style.display = "none";
            writeLog(`Mode ${tipe}`, "SYSTEM");
        }
    }

    // Listener saat select berubah
    pilihTipe.addEventListener('change', updateFormLayout);

    // Jalankan saat pertama kali load
    updateFormLayout();

    // Contoh listener saat tombol kirim diklik
    document.getElementById('formInputMotor').addEventListener('submit', function(e) {
        e.preventDefault();
        writeLog("Mengirim data ke server...", "WAIT");
        
        // Simulasi pengiriman sukses
        setTimeout(() => {
            writeLog("Data berhasil tersimpan di Database.", "SUCCESS");
            this.reset();
            updateFormLayout();
        }, 1000);
    });
});
</script>