/**
 * File: inputData-motor.js
 * Perbaikan: Menggunakan pengambilan elemen dinamis dan pengecekan Global Config.
 */

document.addEventListener("DOMContentLoaded", function () {
  // 1. Inisialisasi Password/Token
  const PASSWORD_RAHASIA = "SemenTonasa2026";

  // 2. Definisi Elemen Form & UI
  const formInput = document.getElementById("formInputMotor");
  const logOutput = document.getElementById("log-output");
  const btnKirim = document.getElementById("btnKirim");
  const pilihTipe = document.getElementById("pilihTipe");
  const parameterSection = document.getElementById("parameterSection");
  const dividerBawah = document.getElementById("dividerBawah");

  // ==========================================
  // 3. FUNGSI LOG terminal (UI)
  // ==========================================
  function writeLog(message, type = "INFO") {
    const now = new Date();
    const time =
      now.getHours().toString().padStart(2, "0") +
      ":" +
      now.getMinutes().toString().padStart(2, "0") +
      ":" +
      now.getSeconds().toString().padStart(2, "0");

    let color = "#cccccc";
    if (type === "ERROR") color = "#ff4d4d";
    if (type === "SUCCESS") color = "#00ff00";
    if (type === "WAIT") color = "#ffff00";
    if (type === "SYSTEM") color = "#00bfff";

    const newEntry = document.createElement("div");
    newEntry.style.color = color;
    newEntry.style.marginBottom = "2px";
    newEntry.innerHTML = `> [${time}] [${type}] ${message}`;
    logOutput.prepend(newEntry);
  }

  // ==========================================
  // 4. LOGIKA TAMPILAN (HIDE/SHOW PARAMETER)
  // ==========================================
  function updateFormLayout() {
    if (!pilihTipe) return;
    const tipe = pilihTipe.value;
    if (tipe === "PREVENTIVE") {
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

  // ==========================================
  // 5. LOGIKA PENGIRIMAN DATA (SUBMIT)
  // ==========================================
  if (formInput) {
    formInput.addEventListener("submit", function (e) {
      e.preventDefault();

      // A. AMBIL ELEMEN DROPDOWN SECARA REAL-TIME (Mencari ulang di DOM)
      // Ini untuk memastikan data terambil meski dropdown ada di section lain
      const elUnit = document.getElementById("pilihUnit");
      const elMotor = document.getElementById("pilihMotor");

      const valUnit = elUnit ? elUnit.value : "";
      const valMotor = elMotor ? elMotor.value : "";

      // B. AMBIL URL DARI GLOBAL CONFIG (tabel-motor.js)
      // Pastikan di tabel-motor.js menggunakan: window.SCRIPT_URLS = { ... }
      const targetURL =
        window.SCRIPT_URLS && valUnit ? window.SCRIPT_URLS[valUnit] : undefined;

      // --- DEBUGGING LOG KE CONSOLE BROWSER ---
      console.log("=== PROSES PENGIRIMAN DATA ===");
      console.log("1. Elemen Unit Terdeteksi:", elUnit ? "YA" : "TIDAK");
      console.log("2. Elemen Motor Terdeteksi:", elMotor ? "YA" : "TIDAK");
      console.log("3. Nilai Unit:", valUnit);
      console.log("4. Nilai Motor:", valMotor);
      console.log("5. URL Target:", targetURL);

      // C. VALIDASI SEBELUM KIRIM
      if (!valUnit || valUnit === "") {
        writeLog("GAGAL: Unit belum dipilih pada filter atas!", "ERROR");
        alert("Silakan pilih UNIT pada dropdown filter terlebih dahulu.");
        return;
      }

      if (!valMotor || valMotor === "") {
        writeLog("GAGAL: Motor belum dipilih pada filter atas!", "ERROR");
        alert("Silakan pilih MOTOR pada dropdown filter terlebih dahulu.");
        return;
      }

      if (!targetURL) {
        writeLog(
          `ERROR: URL Apps Script untuk unit [${valUnit}] tidak ditemukan!`,
          "ERROR",
        );
        console.error("Konfigurasi URL tidak ditemukan untuk unit:", valUnit);
        return;
      }

      // D. PERSIAPAN PAYLOAD (DATA FORM)
      const formData = new FormData(formInput);
      const payload = {
        token: PASSWORD_RAHASIA,
        targetSheet: valMotor,
        maintenanceType: formData.get("pilih_salah_satu"),
        sectionNo: formData.get("section_no"),
        actions: formData.get("action"),

        // Ambil data teknis, jika kosong/null beri tanda "-"
        vibrasi: formData.get("vibrasi") || "-",
        tempDE: formData.get("temp_de") || "-",
        tempNDE: formData.get("temp_nde") || "-",
        suhuRuang: formData.get("suhu_ruang") || "-",
        beban: formData.get("beban_gen") || "-",
        damper: formData.get("damper") || "-",
        amper: formData.get("load_current") || "-",
        bunyi: formData.get("bunyi") || "-",
        panel: formData.get("panel") || "-",
        kelengkapan: formData.get("lengkap") || "-",
        kebersihan: formData.get("bersih") || "-",
        grounding: formData.get("ground") || "-",
        regreasing: formData.get("regrease") || "-",
      };

      // E. PROSES EKSEKUSI KIRIM (FETCH)
      writeLog(`Menghubungkan ke server ${valUnit}...`, "WAIT");
      btnKirim.disabled = true;
      btnKirim.innerHTML = '<i class="fas fa-spinner fa-spin"></i> MENGIRIM...';

      fetch(targetURL, {
        method: "POST",
        mode: "no-cors", // Penting untuk bypass CORS Google Apps Script
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      })
        .then(() => {
          // Sukses (Mode no-cors selalu masuk ke .then selama request terkirim)
          writeLog(`SUKSES: Data tersimpan di Sheet [${valMotor}]`, "SUCCESS");
          formInput.reset();
          updateFormLayout();
        })
        .catch((error) => {
          writeLog("Koneksi gagal: " + error.message, "ERROR");
          console.error("Fetch Error:", error);
        })
        .finally(() => {
          btnKirim.disabled = false;
          btnKirim.innerHTML =
            '<i class="fas fa-paper-plane mr-1"></i> KIRIM DATA MONITORING';
        });
    });
  } else {
    console.error(
      "CRITICAL: Form ID 'formInputMotor' tidak ditemukan di halaman ini!",
    );
  }
});
