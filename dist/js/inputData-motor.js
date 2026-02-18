/**
 * File: inputData-motor.js
 * Fitur:
 * - TRIGGER: Button Click (Bukan Form Submit) -> Anti Jebol
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

  // --- 3. PROSES KLIK TOMBOL (GANTI DARI SUBMIT KE CLICK) ---
  if (btnKirim) {
    // Kita matikan semua event submit bawaan form agar aman
    if (formInput) {
      formInput.onsubmit = function (e) {
        e.preventDefault();
        return false;
      };
    }

    btnKirim.onclick = function (e) {
      e.preventDefault(); // Mencegah reload halaman apapun yang terjadi

      // Ambil Data
      const elUnit = document.getElementById("pilihUnit");
      const elMotor = document.getElementById("pilihMotor");

      const valUnit = elUnit ? elUnit.value : "";
      const valMotor = elMotor ? elMotor.value : "";
      const tipeMain = pilihTipe.value;
      const isPreventive = tipeMain === "PREVENTIVE";

      writeLog(`--- Klik Terdeteksi (${tipeMain}) ---`, "SYSTEM");

      // --- TAHAP 1: VALIDASI ---
      let pesanError = "";

      // 1. Cek Unit/Motor
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

      // 2. Cek Action
      if (!pesanError) {
        const formData = new FormData(formInput);
        const actionVal = formData.get("action");
        if (!actionVal || actionVal.trim() === "") {
          pesanError = "Kolom ACTION (Keterangan) Wajib Diisi!";
        }
      }

      // 3. Cek Preventive
      if (!pesanError && isPreventive) {
        const formData = new FormData(formInput);

        // Cek Angka
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

        // Cek Dropdown
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
        // >>> STOP: JANGAN KIRIM <<<
        toastr.warning(pesanError);
        writeLog("GAGAL: " + pesanError, "WARNING");
        return false;
      } else {
        // >>> AMAN: KIRIM DATA <<<
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
          sectionNo: getGeneral("section_no"),
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
            const btnRefresh = document.getElementById("btnRefresh");
            if (btnRefresh) btnRefresh.click();
            else if (window.jQuery) $("#btnRefresh").trigger("click");
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
