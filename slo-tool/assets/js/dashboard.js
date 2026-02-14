(function () {
  function markActiveSidebarLink() {
    var links = document.querySelectorAll(".sidebar a");
    var current = window.location.pathname.toLowerCase();
    links.forEach(function (link) {
      var href = (link.getAttribute("href") || "").toLowerCase();
      if (!href || href.indexOf("logout") !== -1) return;
      if (current.endsWith(href.replace("./", "")) || current.indexOf(href) !== -1) {
        link.classList.add("is-active");
      }
    });
  }

  function addTopbar() {
    if (!document.querySelector(".wrapper .content") || document.querySelector(".topbar")) return;
    var content = document.querySelector(".content");
    var pageHeading = (content.querySelector("h1") || {}).textContent || "Dashboard";
    var roleHeading = (document.querySelector(".sidebar h2") || {}).textContent || "Academic User";

    var topbar = document.createElement("div");
    topbar.className = "topbar";
    topbar.innerHTML =
      '<div class="topbar-left">' +
      "<h3>" + pageHeading + "</h3>" +
      "<p>Student Learning Outcome Mapping Tool</p>" +
      "</div>" +
      '<div class="profile-area">' +
      '<button type="button" class="profile-btn">User: ' + roleHeading + " v</button>" +
      '<div class="profile-menu">' +
      '<a href="#" data-tooltip="Profile options are frontend preview only">Profile</a>' +
      '<a href="#" data-tooltip="Settings panel can be connected later">Settings</a>' +
      '<a href="/slo-tool/auth/logout.php">Logout</a>' +
      "</div>" +
      "</div>";
    content.insertBefore(topbar, content.firstChild);

    var profileArea = topbar.querySelector(".profile-area");
    var profileBtn = topbar.querySelector(".profile-btn");
    profileBtn.addEventListener("click", function () {
      profileArea.classList.toggle("open");
    });
    document.addEventListener("click", function (event) {
      if (!profileArea.contains(event.target)) profileArea.classList.remove("open");
    });
  }

  function animateProgressBars() {
    var bars = document.querySelectorAll(".progress-fill[data-value]");
    bars.forEach(function (bar) {
      var value = Number(bar.getAttribute("data-value") || 0);
      var safeValue = Math.max(0, Math.min(100, value));
      if (safeValue < 50) bar.classList.add("red");
      else if (safeValue < 70) bar.classList.add("yellow");
      setTimeout(function () {
        bar.style.width = safeValue + "%";
      }, 120);
    });
  }

  function drawBarChart(canvasId, labels, values, color) {
    var canvas = document.getElementById(canvasId);
    if (!canvas || !canvas.getContext) return;
    var ctx = canvas.getContext("2d");
    var width = canvas.width;
    var height = canvas.height;
    var leftPad = 42;
    var rightPad = 16;
    var bottomPad = 34;
    var topPad = 16;
    var chartHeight = height - topPad - bottomPad;
    var chartWidth = width - leftPad - rightPad;
    var maxValue = Math.max.apply(null, values.concat([1]));
    var barGap = 18;
    var barWidth = Math.max(14, (chartWidth - barGap * (values.length - 1)) / Math.max(values.length, 1));

    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = "#5f6386";
    ctx.font = "12px Inter, sans-serif";

    for (var g = 0; g <= 4; g++) {
      var yLine = topPad + (chartHeight / 4) * g;
      ctx.strokeStyle = "#e8eafe";
      ctx.beginPath();
      ctx.moveTo(leftPad, yLine);
      ctx.lineTo(width - rightPad, yLine);
      ctx.stroke();
    }

    values.forEach(function (value, index) {
      var x = leftPad + index * (barWidth + barGap);
      var barHeight = (value / maxValue) * (chartHeight - 8);
      var y = topPad + (chartHeight - barHeight);

      ctx.fillStyle = color || "#5e3df2";
      ctx.fillRect(x, y, barWidth, barHeight);

      ctx.fillStyle = "#3d416e";
      ctx.fillText(labels[index] || "", x, height - 12);
      ctx.fillText(Number(value).toFixed(1), x, y - 6);
    });
  }

  window.drawBarChart = drawBarChart;

  document.addEventListener("DOMContentLoaded", function () {
    markActiveSidebarLink();
    addTopbar();
    animateProgressBars();
  });
})();
