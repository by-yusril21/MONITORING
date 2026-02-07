<?php
session_start();
$delete = false;
$deleteTerminal = false;
$reset_id = false;


if (!isset($_SESSION['username'])) {
  echo "<script> location.href='login.php'; </script>";
  exit; // Tambahkan exit setelah redirect
}


include "config/database.php";
include "inc/header.php";
include "inc/navbar.php";
include "inc/sidebar.php";
include "inc/alerts.php";

if (isset($_GET['page'])) {
  $page = $_GET['page'];
  include "page/" . $page . ".php";
} else {
  include "page/dashboard.php";
}
include "inc/footer.php";
?>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
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
<!-- jQuery Knob -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>

<!-- FLOT CHARTS -->
<script src="plugins/flot/jquery.flot.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="plugins/flot/plugins/jquery.flot.resize.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="plugins/flot/plugins/jquery.flot.pie.js"></script>

<script src="plugins/toastr/toastr.min.js"></script>

<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<!-- MQTT client and dashboard logic (moved here so jQuery and plugins are available) -->
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script>
  (function () {
    const clientId = Math.random().toString(16).substr(2, 8);
    const host = 'wss://monitoring-panel-listrik-sound.cloud.shiftr.io:443';

    const options = {
      keepalive: 30,
      clientId: clientId,
      username: "monitoring-panel-listrik-sound",
      password: "QlpPWck275IIjTxu",
      protocolId: 'MQTT',
      protocolVersion: 4,
      clean: true,
      reconnectPeriod: 1000,
      connectTimeout: 30 * 1000,
    };

    console.log("Menghubungkan ke broker");
    const client = mqtt.connect(host, options);

    client.on("connect", () => {
      console.log("Terhubung");
      try {
        const statusEl = document.getElementById("status");
        if (statusEl) {
          statusEl.innerHTML = "Terhubung";
          statusEl.style.color = "blue";
        } else {
          console.warn('connect: element "status" not found in DOM');
        }
      } catch (e) {
        console.warn('connect: error updating status element', e);
      }

      client.subscribe("panelsound/#", { qos: 1 });
    });

    let suhuTimeout, kelembabanTimeout;
    let voltageTimeout, frequencyTimeout, currentTimeout, powerTimeout;
    const TIMEOUT_DURATION = 3000;

    client.on("message", function (topic, payload) {
      payload = payload.toString();

      try {
        if (topic === "panelsound/12345678/volt") {
          clearTimeout(voltageTimeout);
          var voltInput = document.getElementById("voltvalue");
          if (voltInput) {
            voltInput.value = payload;
            try { $(voltInput).trigger('change'); } catch (e) { /* jQuery should be present but guard anyway */ }
            if (typeof updatePlot === 'function') updatePlot(voltageChart, voltageData, payload, 280);
          }

          voltageTimeout = setTimeout(function () {
            if (voltInput) {
              voltInput.value = 0;
              try { $(voltInput).trigger('change'); } catch (e) { }
              for (let i = 0; i < 50; i++) {
                if (typeof updatePlot === 'function') updatePlot(voltageChart, voltageData, 0, 280);
              }
            }
          }, TIMEOUT_DURATION);

        } else if (topic === "panelsound/12345678/hz") {
          clearTimeout(frequencyTimeout);
          var hzInput = document.getElementById("hzvalue");
          if (hzInput) {
            hzInput.value = payload;
            try { $(hzInput).trigger('change'); } catch (e) { }
            if (typeof updatePlot === 'function') updatePlot(frequencyChart, frequencyData, payload, 100);
          }

          frequencyTimeout = setTimeout(function () {
            if (hzInput) {
              hzInput.value = 0;
              try { $(hzInput).trigger('change'); } catch (e) { }
              for (let i = 0; i < 50; i++) {
                if (typeof updatePlot === 'function') updatePlot(frequencyChart, frequencyData, 0, 100);
              }
            }
          }, TIMEOUT_DURATION);

        } else if (topic === "panelsound/12345678/arus") {
          var arusInput = document.getElementById("arusvalue");
          if (arusInput) {
            arusInput.value = payload;
            try { $(arusInput).trigger('change'); } catch (e) { }
            if (typeof updatePlot === 'function') updatePlot(amperChart, amperData, payload, 50);
          }

        } else if (topic === "panelsound/12345678/power") {
          var powerInput = document.getElementById("powervalue");
          if (powerInput) {
            powerInput.value = payload;
            try { $(powerInput).trigger('change'); } catch (e) { }
            if (typeof updatePlot === 'function') updatePlot(powerChart, powerData, payload, 3300);
          }

        } else if (topic === "panelsound/12345678/relayA") {
          updateIcon("iconA1", payload, "A-1", "A-0");
          updatechecked("ch-outA-1", "ch-outA-0", payload, "A-1", "A-0");
        } else if (topic === "panelsound/12345678/relayB") {
          updateIcon("iconB1", payload, "B-1", "B-0");
          updatechecked("ch-outB-1", "ch-outB-0", payload, "B-1", "B-0");
        } else if (topic === "panelsound/12345678/relayC") {
          updateIcon("iconC1", payload, "C-1", "C-0");
          updatechecked("ch-outC-1", "ch-outC-0", payload, "C-1", "C-0");
        } else if (topic === "panelsound/12345678/relayD") {
          updateIcon("iconD1", payload, "D-1", "D-0");
          updatechecked("ch-outD-1", "ch-outD-0", payload, "D-1", "D-0");
        }

        if (topic.includes("panelsound/status/")) {
          const statusTd = document.getElementById(topic);
          if (statusTd) {
            statusTd.innerHTML = payload;
            if (payload.toString() === "offline") {
              statusTd.style.color = "red";
              const suhuEl = document.getElementById("suhu");
              const kelembabanEl = document.getElementById("kelembaban");
              if (suhuEl) suhuEl.innerText = 0;
              if (kelembabanEl) kelembabanEl.innerText = 0;
            } else if (payload.toString() === "online") {
              statusTd.style.color = "blue";
            }
          } else {
            console.warn('message: no DOM element found for topic', topic);
          }
        }
      } catch (e) {
        console.warn('message handler error', e);
      }
    });

    // publish functions (unchanged) - these rely on DOM ids
    window.publishoutA = function () {
      let data;
      if (document.getElementById("outA-1") && document.getElementById("outA-1").checked) data = "A-1";
      if (document.getElementById("outA-0") && document.getElementById("outA-0").checked) data = "A-0";
      console.log(`Mempublikasikan: ${data}`);
      if (data) client.publish("panelsound/12345678/relayA", data, { qos: 1, retain: true });
    };

    window.publishoutB = function () {
      let data;
      if (document.getElementById("outB-1") && document.getElementById("outB-1").checked) data = "B-1";
      if (document.getElementById("outB-0") && document.getElementById("outB-0").checked) data = "B-0";
      console.log(`Mempublikasikan: ${data}`);
      if (data) client.publish("panelsound/12345678/relayB", data, { qos: 1, retain: true });
    };

    window.publishoutC = function () {
      let data;
      if (document.getElementById("outC-1") && document.getElementById("outC-1").checked) data = "C-1";
      if (document.getElementById("outC-0") && document.getElementById("outC-0").checked) data = "C-0";
      console.log(`Mempublikasikan: ${data}`);
      if (data) client.publish("panelsound/12345678/relayC", data, { qos: 1, retain: true });
    };

    window.publishoutD = function () {
      let data;
      if (document.getElementById("outD-1") && document.getElementById("outD-1").checked) data = "D-1";
      if (document.getElementById("outD-0") && document.getElementById("outD-0").checked) data = "D-0";
      console.log(`Mempublikasikan: ${data}`);
      if (data) client.publish("panelsound/12345678/relayD", data, { qos: 1, retain: true });
    };

    // helper to update the small power icon
    window.updateIcon = function (iconId, status, on, off) {
      const icon = document.getElementById(iconId);
      if (!icon) return;
      if (status === on) {
        icon.classList.add("icon-on");
        icon.classList.remove("icon-off");
      } else if (status === off) {
        icon.classList.add("icon-off");
        icon.classList.remove("icon-on");
      }
    };

    // update the active label and toggle the .output-on class on the card
    window.updatechecked = function (id1, id0, status, on, off) {
      const chicon1 = document.getElementById(id1);
      const chicon0 = document.getElementById(id0);
      if (chicon1 && chicon0) {
        if (status === on) {
          chicon1.classList.add("active");
          chicon0.classList.remove("active");
        } else {
          chicon1.classList.remove("active");
          chicon0.classList.add("active");
        }
      }
      try {
        const el = document.getElementById(id1) || document.getElementById(id0);
        if (el) {
          const card = el.closest('.output-card');
          if (card) {
            if (status === on) {
              card.classList.add('output-on');
            } else {
              card.classList.remove('output-on');
            }
          }
        }
      } catch (e) {
        console.warn('updatechecked: could not toggle output-on class', e);
      }
    };

    window.disableButtons = function (output) {
      const in1 = document.getElementById(`out${output}-1`);
      const in0 = document.getElementById(`out${output}-0`);
      if (in1) in1.disabled = true;
      if (in0) in0.disabled = true;
      const ch1 = document.getElementById(`ch-out${output}-1`);
      const ch0 = document.getElementById(`ch-out${output}-0`);
      if (ch1) ch1.classList.add('disabled');
      if (ch0) ch0.classList.add('disabled');
    };

    window.enableButtons = function (output) {
      const in1 = document.getElementById(`out${output}-1`);
      const in0 = document.getElementById(`out${output}-0`);
      if (in1) in1.disabled = false;
      if (in0) in0.disabled = false;
      const ch1 = document.getElementById(`ch-out${output}-1`);
      const ch0 = document.getElementById(`ch-out${output}-0`);
      if (ch1) ch1.classList.remove('disabled');
      if (ch0) ch0.classList.remove('disabled');
    };

    // keep other helper functions (updateIcon/updatechecked/disable/enable) global if already defined in page
    // Lock button handler: disables/enables all OUTPUT switches
    $(function () {
      // Restore lock state from localStorage
      var locked = localStorage.getItem('lockState') === 'on';
      var icon = $('#lockIcon');
      function setLockUI(state) {
        if (state) {
          icon.addClass('text-warning');
          window.disableButtons('A');
          window.disableButtons('B');
          window.disableButtons('C');
          window.disableButtons('D');
        } else {
          icon.removeClass('text-warning');
          window.enableButtons('A');
          window.enableButtons('B');
          window.enableButtons('C');
          window.enableButtons('D');
        }
      }
      setLockUI(locked);
      $('#lock').on('click', function () {
        locked = !locked;
        localStorage.setItem('lockState', locked ? 'on' : 'off');
        setLockUI(locked);
      });
    });
  })();
</script>

<?php
// Letakkan ini di bagian atas halaman, tepat setelah pustaka toastr diimpor
if ($delete == true) {
  echo "<script>toastr.success('Data sensor berhasil dihapus.');</script>";
} else if ($deleteTerminal == true) {
  echo "<script>toastr.success('Data terminal berhasil dihapus.');</script>";
} else if ($reset_id == true) {
  echo "<script>toastr.success('Semua Data Di Hapus Dan Id Di Reset Ke 0.');</script>";
}
?>

<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>

<style>
  .knob-input {
    font-size: 30px !important;
    /* Ubah ukuran font sesuai kebutuhan */
  }

  .center-text {
    text-align: center;
  }

  .icon-on {
    color: red;
    font-size: 1.2rem;
  }

  .icon-off {
    color: black;
    font-size: 1.1rem;
  }

  /* Default color for the icon */
  #lockIcon {
    color: black;
  }

  /* Color when disabled */
  #lockIcon.disabled-icon {
    color: red;
  }

  @keyframes pulse {
    0% {
      transform: scale(1);
      opacity: 1;
    }

    50% {
      transform: scale(1.1);
      opacity: 0.9;
    }

    100% {
      transform: scale(1);
      opacity: 1;
    }
  }

  #emergency-alert {
    position: fixed;
    top: 1%;
    z-index: 9999;
    /* Memastikan elemen berada di atas elemen lain */
    animation: pulse 2s infinite;

    font-size: 11px;
  }
</style>

<script>
  $(document).ready(function () {
    $(".knob-max-380").knob({
      min: 0,
      max: 280, // Nilai maksimal 380
      width: 120,
      height: 120,
      fgColor: "#007bff",
      bgColor: "#CCCCCC",
      thickness: 0.26,
      displayInput: true,
      displayPrevious: true,
      angleArc: 250,
      angleOffset: -125,
      draw: function () {
        // Apply the custom class to the input field
        $(this.i).addClass('knob-input');
      },
      release: function (value) {
        console.log("Released at (max 380): " + value);
      }
    });

    // Sync knob labels color to match their gauge fgColor (TEGANGAN, FREKUENSI, AMPER, DAYA)
    function syncKnobLabels() {
      try {
        $('#voltvalue').parent().next('.knob-label').css('color', '#007bff'); // Voltage - blue
        $('#hzvalue').parent().next('.knob-label').css('color', '#66cc66'); // Frequency - green
        $('#arusvalue').parent().next('.knob-label').css('color', '#dc3545'); // Current - red
        $('#powervalue').parent().next('.knob-label').css('color', '#ffc107'); // Power - yellow
      } catch (e) {
        console.warn('syncKnobLabels error', e);
      }
    }
    // Run once after init and also after short delay in case knob plugin manipulates DOM later
    syncKnobLabels();
    setTimeout(syncKnobLabels, 200);

    $(".knob-max-100").knob({
      min: 0,
      max: 100, // Nilai maksimal 100
      step: 0.1,
      width: 120,
      height: 120,
      fgColor: "#66CC66",
      bgColor: "#CCCCCC",
      thickness: 0.26,
      displayInput: true,
      displayPrevious: true,
      angleArc: 250,
      angleOffset: -125,
      draw: function () {
        // Apply the custom class to the input field
        $(this.i).addClass('knob-input');
      },
      release: function (value) {
        console.log("Released at (max 100): " + value);
      }
    });

    $(".knob-max-50").knob({
      min: 0,
      max: 50, // Nilai maksimal 100
      step: 0.1, // Mengatur langkah perpindahan menjadi 0.5
      width: 120,
      height: 120,
      fgColor: "#dc3545",
      bgColor: "#CCCCCC",
      thickness: 0.26,
      displayInput: true,
      displayPrevious: true,
      angleArc: 250,
      angleOffset: -125,
      draw: function () {
        // Apply the custom class to the input field
        $(this.i).addClass('knob-input');
      },
      release: function (value) {
        console.log("Released at (max 100): " + value);
      }
    });


    $(".knob-max-2000").knob({
      min: 0,
      max: 3300, // Nilai maksimal 2000
      width: 120,
      height: 120,
      fgColor: "#ffc107",
      bgColor: "#CCCCCC",
      thickness: 0.26,
      displayInput: true,
      displayPrevious: true,
      angleArc: 250,
      angleOffset: -125,
      draw: function () {
        // Apply the custom class to the input field
        $(this.i).addClass('knob-input');
      },
      release: function (value) {
        console.log("Released at (max 2000): " + value);
      }
    });
  });


  // Flot Interactive Chart
  let voltageData = [];
  let frequencyData = [];
  let amperData = [];
  let powerData = [];
  const totalPoints = 50;

  function getMinMax(arr) {
    if (!arr.length) return { min: 0, max: 1 };
    let min = Math.min(...arr);
    let max = Math.max(...arr);
    if (min === max) max = min + 1;
    return { min, max };
  }

  function updateChartData(dataArray, payload) {
    if (dataArray.length > 0) {
      dataArray.shift(); // Remove the first element
    }
    const y = parseFloat(payload);
    dataArray.push(y);
    while (dataArray.length < totalPoints) {
      dataArray.push(y);
    }
    const res = [];
    for (let i = 0; i < dataArray.length; ++i) {
      res.push([i, dataArray[i]]);
    }
    return res;
  }

  // (removed updateChartDataN — reverting to original behavior)

  function getYAxisOptions(dataArray, defaultMax, tickSizeY) {
    const { min, max } = getMinMax(dataArray);
    return {
      min: min,
      max: Math.max(max, defaultMax),
      show: true,
      tickColor: '#ccc',
      font: { color: '#333' },
      tickSize: tickSizeY,
    };
  }

  function getXAxisOptions(dataArray, tickSizeX) {
    return {
      min: 0,
      max: dataArray.length ? dataArray.length - 1 : totalPoints,
      show: true,
      tickColor: '#ccc',
      font: { color: '#333' },
      tickSize: tickSizeX,
    };
  }

  function initChart(chartId, color, defaultMaxY, tickSizeY, tickSizeX, dataArray) {
    const yaxis = getYAxisOptions(dataArray, defaultMaxY, tickSizeY);
    const xaxis = getXAxisOptions(dataArray, tickSizeX);
    // Font axis warna putih soft
    yaxis.font = { color: '#f0f0f0', size: 13, weight: 'bold' };
    xaxis.font = { color: '#f0f0f0', size: 13, weight: 'bold' };
    return $.plot(chartId, [
      { data: updateChartData([], 0) }
    ], {
      grid: {
        borderColor: '#3a465a',
        borderWidth: 3,
        tickColor: '#3a465a',
        color: '#3a465a',
        backgroundColor: { colors: ["#232a36", "#252d3a"] }
      },
      series: {
        color: color,
        lines: {
          lineWidth: 1,
          show: true,
          fill: true,
          fillColor: { colors: [{ opacity: 0.1 }, { opacity: 0.50 }] }
        },
        shadowSize: 0
      },
      yaxis: yaxis,
      xaxis: xaxis,
      tooltip: true,
      tooltipOpts: {
        content: "Value: %y",
        defaultTheme: false,
        shifts: { x: 10, y: 20 }
      }
    });
  }


  // Inisialisasi chart dengan data awal (otomatis ambil skala asli)
  // Inisialisasi chart dengan data awal (otomatis ambil skala asli)
  const voltageChart = initChart('#chart-voltage', '#007bff', 250, 10, 1, voltageData);
  voltageChart.setupGrid(); voltageChart.draw();
  const frequencyChart = initChart('#chart-frequency', '#28a745', 80, 5, 1, frequencyData);
  frequencyChart.setupGrid(); frequencyChart.draw();
  const amperChart = initChart('#chart-amper', '#dc3545', 50, 5, 1, amperData);
  amperChart.setupGrid(); amperChart.draw();
  const powerChart = initChart('#chart-power', '#ffc107', 2000, 100, 1, powerData);
  powerChart.setupGrid(); powerChart.draw();

  function updatePlot(chart, dataArray, payload, defaultMaxY) {
    const updatedData = updateChartData(dataArray, payload);
    const yaxis = getYAxisOptions(dataArray, defaultMaxY, chart.getOptions().yaxes[0].tickSize);
    const xaxis = getXAxisOptions(dataArray, chart.getOptions().xaxes[0].tickSize);
    chart.setData([updatedData]);
    chart.getOptions().yaxes[0].min = yaxis.min;
    chart.getOptions().yaxes[0].max = yaxis.max;
    chart.getOptions().xaxes[0].min = xaxis.min;
    chart.getOptions().xaxes[0].max = xaxis.max;
    chart.setupGrid();
    chart.draw();
  }
</script>



</body>

</html>