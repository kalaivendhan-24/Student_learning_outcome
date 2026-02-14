// Backward-compatible bridge for pages still including app.js
if (typeof window.drawBarChart !== "function") {
  window.drawBarChart = function () {};
}
