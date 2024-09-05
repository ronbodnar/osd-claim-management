Chart.register(ChartDataLabels)

function updateChartColors() {
  var charts = [totalClaimsChart]

  charts.forEach((chart) => {
    if (chart == null) return

    chart.options.color =
      localStorage.getItem("theme") === "light" ? "#7F7F7F" : "#CCCCCC"
    chart.options.borderColor =
      localStorage.getItem("theme") === "light" ? "#eee" : "#252526"

    if (chart.options.scales.x) {
      chart.options.scales.x.title.color =
        localStorage.getItem("theme") === "light" ? "#7F7F7F" : "#CCCCCC"
      chart.options.scales.x.grid.color =
        localStorage.getItem("theme") === "light"
          ? "rgba(138, 136, 136, 0.2)"
          : "rgba(255, 255, 255, 0.2)"
      chart.options.scales.x.ticks.color =
        localStorage.getItem("theme") === "light" ? "#7F7F7F" : "#CCCCCC"
      chart.options.scales.x.border.color =
        localStorage.getItem("theme") === "light"
          ? "rgba(138, 136, 136, 0.1)"
          : "rgba(255, 255, 255, 0.2)"
    }

    if (chart.options.scales.x2) {
      chart.options.scales.x2.title.color =
        localStorage.getItem("theme") === "light" ? "#7F7F7F" : "#CCCCCC"
      chart.options.scales.x2.grid.color =
        localStorage.getItem("theme") === "light"
          ? "rgba(138, 136, 136, 0.2)"
          : "rgba(255, 255, 255, 0.2)"
      chart.options.scales.x2.ticks.color =
        localStorage.getItem("theme") === "light" ? "#7F7F7F" : "#CCCCCC"
      chart.options.scales.x2.border.color =
        localStorage.getItem("theme") === "light"
          ? "rgba(138, 136, 136, 0.1)"
          : "rgba(255, 255, 255, 0.2)"
    }

    if (chart.options.scales.y) {
      chart.options.scales.y.grid.color =
        localStorage.getItem("theme") === "light"
          ? "rgba(138, 136, 136, 0.2)"
          : "rgba(255, 255, 255, 0.2)"
      chart.options.scales.y.ticks.color =
        localStorage.getItem("theme") === "light" ? "#7F7F7F" : "#CCCCCC"
      chart.options.scales.y.border.color =
        localStorage.getItem("theme") === "light"
          ? "rgba(138, 136, 136, 0.1)"
          : "rgba(255, 255, 255, 0.2)"
    }

    if (chart.data.datasets[0].datalabels) {
      var datasets = chart.data.datasets
      datasets.forEach((data) => {
        data.datalabels.color =
          localStorage.getItem("theme") === "light" ? "#7F7F7F" : "#CCCCCC"
      })
    }
    chart.update()
  })
}

function updateTotalClaims() {
  $.ajax({
    dataType: "json",
    data: "action=count-type",
    url: "src/api.php",
  }).done(function (data) {
    $("#totalClaims").html(data["total"])

    var chart = Chart.getChart("totalClaimsChart")
    chart.data.datasets[0].data = [
      data["overage"],
      data["shortage"],
      data["damage"],
    ]
    chart.update()
  })
}

var totalClaimsChart = null
if (document.getElementById("totalClaimsChart")) {
  const ctx = document.getElementById("totalClaimsChart").getContext("2d")
  totalClaimsChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Overage", "Shortage", "Damage"],
      datasets: [
        {
          label: "Number of claims",
          backgroundColor: ["#3D68BB", "#E69322", "#218B94"],
          borderRadius: 3,
          data: [30, 180, 35],
          datalabels: {
            align: "end",
            anchor: "end",
          },
        },
      ],
    },
    options: {
      //barThickness: 8,
      barPercentage: 0.5,
      categoryPercentage: 1.0,
      indexAxis: "y",
      responsive: true,
      maintainAspectRatio: false,
      layout: {
        padding: {
          left: 0,
          right: 26,
          top: 0,
          bottom: 0,
        },
      },
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          mode: "nearest",
          callbacks: {
            label: function (context) {
              let label = context.dataset.label || ""

              if (label) {
                label += ": "
              }
              if (context.parsed.x !== null) {
                label += context.parsed.x
              }
              return label
            },
          },
        },
      },
      scales: {
        x: {
          grid: {
            display: false,
          },
          border: {
            display: false,
          },
          title: {
            display: false,
          },
          ticks: {
            display: false,
          },
        },
        y: {
          grace: 5,
          grid: {
            display: false,
          },
          border: {
            display: false,
          },
          title: {
            display: false,
          },
          ticks: {},
        },
      },
    },
  })

  // Update the total claim chart
  updateTotalClaims()
}
