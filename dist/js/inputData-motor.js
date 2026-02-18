/**
 * File: inputData-motor.js
 * Fitur:
 * - SYNC TABLE: Section No diambil otomatis dari Baris Terakhir Tabel (#example1)
 * - TRIGGER: Button Click (Anti Auto-Submit)
 * - LOGIC: Strict Validation
 * - UI: Toastr & Auto Refresh
 */

document.addEventListener("DOMContentLoaded", function () {
  const PASSWORD_RAHASIA = "SemenTonasa2026";

  const formInput = document.getElementById("formInputMotor");
  const logOutput = document.getElementById("log-output");
  const btnKirim = document.getElementById("btnKirim");
  const pilihTipe = document.getElementById("pilihTipe");
  const parameterSection = document.getElementById("parameterSection");
  const dividerBawah = document.getElementById("dividerBawah");
  const inputSectionNo = document.getElementById("inputSectionNo");

  // Element Filter
  const elUnit = document.getElementById("pilihUnit");
  const elMotor = document.getElementById("pilihMotor");

  // --- 1. Fungsi Log UI ---
  function writeLog(message, type = "INFO") {
    const now = new Date();
    const time = now.toLocaleTimeString("id-ID", { hour12: false });
    let color = "#cccccc";
    if (type === "ERROR") color = "#ff4d4d";
    if (type === "SUCCESS") color = "#00ff00";
    if (type === "WAIT") color = "#ffff00";
    if (type === "SYSTEM") color = "#00bfff";
    if (type === "WARNING") color = "#ffc107";

    const newEntry = document.createElement("div");
    newEntry.style.color = color;
    newEntry.style.marginBottom = "2px";
    newEntry.style.fontFamily = "'Courier New', monospace";
    newEntry.innerHTML = `> [${time}] [${type}] ${message}`;
    logOutput.prepend(newEntry);
  }

  // --- 2. Layout Handler ---
  function updateFormLayout() {
    if (!pilihTipe) return;
    if (pilihTipe.value === "PREVENTIVE") {
      parameterSection.style.display = "block";
      dividerBawah.style.display = "block";
    } else {
      parameterSection.style.display = "none";
      dividerBawah.style.display = "none";
    }
  }

  if (pilihTipe) {
    pilihTipe.addEventListener("change", updateFormLayout);
    updateFormLayout();
  }

  // ============================================================
  // FITUR BARU: AMBIL SECTION NO DARI TABEL (BUKAN DATABASE)
  // ============================================================

  // Fungsi ini akan dijalankan setiap kali Tabel selesai digambar (Draw Event)
  function syncSectionFromTable() {
    if (!inputSectionNo) return;

    // Cek apakah jQuery dan DataTable tersedia
    if (typeof $ !== "undefined" && $.fn.DataTable.isDataTable("#example1")) {
      const table = $("#example1").DataTable();

      // Cek apakah tabel ada isinya
      if (table.data().any()) {
        // Ambil semua data (tanpa filter/pagination) untuk mencari baris terakhir absolut
        const allData = table.rows().data();

        // Ambil baris terakhir (Data terbaru biasanya paling bawah)
        const lastRow = allData[allData.length - 1];

        // Di tabel-motor.js:
        // Index 0: No
        // Index 1: Timestamp
        // Index 2: Email
        // Index 3: Unit
        // Index 4: SECTION NO (Target Kita!)

        let lastSectionVal = lastRow[4]; // Ambil kolom ke-5 (Index 4)

        // Bersihkan data (kadang ada HTML atau strip)
        if (
          lastSectionVal === null ||
          lastSectionVal === undefined ||
          lastSectionVal === ""
        ) {
          lastSectionVal = "0";
        }

        // Masukkan ke Input
        inputSectionNo.value = lastSectionVal;

        // Log ke console kecil (opsional, agar tau sistem jalan)
        // writeLog(`Sync Tabel: Section No ditemukan [${lastSectionVal}]`, "SYSTEM");

        inputSectionNo.style.backgroundColor = "#e9ecef"; // Abu-abu
      } else {
        // Jika tabel kosong (Motor baru/belum ada data)
        inputSectionNo.value = "0";
        inputSectionNo.style.backgroundColor = "#fff3cd"; // Kuning warning
      }
    } else {
      inputSectionNo.placeholder = "Menunggu Tabel...";
    }
  }

  // PASANG "MATA-MATA" PADA TABEL
  // Setiap kali tabel-motor.js selesai me-load data ('draw.dt'), fungsi kita jalan.
  if (typeof $ !== "undefined") {
    $("#example1").on("draw.dt", function () {
      console.log("Tabel Updated -> Syncing Section No...");
      syncSectionFromTable();
    });
  }

  // ============================================================
  // 3. PROSES KLIK TOMBOL KIRIM
  // ============================================================
  if (btnKirim) {
    if (formInput) {
      formInput.onsubmit = function (e) {
        e.preventDefault();
        return false;
      };
    }

    btnKirim.onclick = function (e) {
      e.preventDefault();

      // Ambil value terbaru
      const elUnit = document.getElementById("pilihUnit");
      const elMotor = document.getElementById("pilihMotor");

      const valUnit = elUnit ? elUnit.value : "";
      const valMotor = elMotor ? elMotor.value : "";
      const tipeMain = pilihTipe.value;
      const isPreventive = tipeMain === "PREVENTIVE";

      writeLog(`--- Klik Terdeteksi (${tipeMain}) ---`, "SYSTEM");

      // --- TAHAP 1: VALIDASI ---
      let pesanError = "";

      if (!valUnit || valUnit === "") pesanError = "Unit belum dipilih!";
      else if (!valMotor || valMotor === "")
        pesanError = "Motor belum dipilih!";
      else {
        const targetURL =
          window.SCRIPT_URLS && valUnit
            ? window.SCRIPT_URLS[valUnit]
            : undefined;
        if (!targetURL) pesanError = "URL Script Unit ini tidak ditemukan!";
      }

      if (!pesanError) {
        const formData = new FormData(formInput);
        const actionVal = formData.get("action");
        if (!actionVal || actionVal.trim() === "") {
          pesanError = "Kolom ACTION (Keterangan) Wajib Diisi!";
        }
      }

      if (!pesanError && isPreventive) {
        const formData = new FormData(formInput);
        const numericFields = [
          "vibrasi",
          "temp_de",
          "temp_nde",
          "suhu_ruang",
          "beban_gen",
          "damper",
          "load_current",
        ];
        for (let name of numericFields) {
          let val = formData.get(name);
          if (val === null || val.trim() === "") {
            pesanError = `Data Teknis (Angka) belum lengkap!`;
            break;
          }
        }

        if (!pesanError) {
          const dropdownFields = [
            "bunyi",
            "panel",
            "lengkap",
            "bersih",
            "ground",
            "regrease",
          ];
          for (let name of dropdownFields) {
            let val = formData.get(name);
            if (val === null || val === "") {
              pesanError = `Pilihan Dropdown belum dipilih semua!`;
              break;
            }
          }
        }
      }

      // --- TAHAP 2: EKSEKUSI ---
      if (pesanError !== "") {
        toastr.warning(pesanError);
        writeLog("GAGAL: " + pesanError, "WARNING");
        return false;
      } else {
        const formData = new FormData(formInput);
        const targetURL = window.SCRIPT_URLS[valUnit];

        const getGeneral = (name) => {
          let val = formData.get(name);
          return val && val.trim() !== "" ? val : "-";
        };

        const getTeknis = (name) => {
          if (isPreventive) {
            let val = formData.get(name);
            return val && val.trim() !== "" ? val : "-";
          } else {
            return "--";
          }
        };

        const payload = {
          token: PASSWORD_RAHASIA,
          targetSheet: valMotor,
          maintenanceType: tipeMain,

          // AMBIL VALUE LANGSUNG DARI INPUT (HASIL SYNC DARI TABEL)
          sectionNo: inputSectionNo ? inputSectionNo.value || "-" : "-",

          actions: getGeneral("action"),
          vibrasi: getTeknis("vibrasi"),
          tempDE: getTeknis("temp_de"),
          tempNDE: getTeknis("temp_nde"),
          suhuRuang: getTeknis("suhu_ruang"),
          beban: getTeknis("beban_gen"),
          damper: getTeknis("damper"),
          amper: getTeknis("load_current"),
          bunyi: getTeknis("bunyi"),
          panel: getTeknis("panel"),
          kelengkapan: getTeknis("lengkap"),
          kebersihan: getTeknis("bersih"),
          grounding: getTeknis("ground"),
          regreasing: getTeknis("regrease"),
        };

        writeLog("Data Valid. Mengirim ke server...", "WAIT");
        btnKirim.disabled = true;
        btnKirim.innerHTML =
          '<i class="fas fa-spinner fa-spin"></i> MENGIRIM...';

        fetch(targetURL, {
          method: "POST",
          mode: "no-cors",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        })
          .then(() => {
            writeLog("SUKSES: Tersimpan di Database.", "SUCCESS");
            toastr.success("Data monitoring berhasil disimpan.");

            formInput.reset();
            updateFormLayout();

            // Auto Refresh Tabel
            // (Setelah tabel refresh, fungsi 'syncSectionFromTable' otomatis jalan lagi karena event 'draw.dt')
            const btnRefresh = document.getElementById("btnRefresh");
            if (btnRefresh) btnRefresh.click();
            else if (window.jQuery) $("#btnRefresh").trigger("click");

            // Reset input sementara sambil nunggu tabel reload
            if (inputSectionNo) {
              inputSectionNo.value = "Updating...";
              inputSectionNo.style.backgroundColor = "#fff3cd";
            }
          })
          .catch((err) => {
            writeLog("ERROR FETCH: " + err.message, "ERROR");
            toastr.error("Gagal koneksi: " + err.message);
          })
          .finally(() => {
            btnKirim.disabled = false;
            btnKirim.innerHTML =
              '<i class="fas fa-paper-plane mr-1"></i> KIRIM DATA MONITORING';
          });
      }
    };
  }
});
