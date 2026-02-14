<?php
require_once __DIR__ . '/config/auth.php';
if (is_logged_in()) {
    header('Location: ' . dashboard_by_role($_SESSION['user']['role']));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SLO Mapping Tool - Login</title>
  <link rel="stylesheet" href="/slo-tool/assets/css/ui.css">
</head>
<body class="login-body">
  <section class="login-panel">
    <div class="login-card">
      <span class="brand-kicker">Academic SaaS</span>
      <h1>Student Learning Outcome Mapping Tool</h1>
      <p>Sign in to continue to your institutional dashboard.</p>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert danger">Invalid credentials.</div>
      <?php endif; ?>
      <form action="/slo-tool/auth/login.php" method="post">
        <div class="form-row">
          <div class="form-label">Email Address</div>
          <div class="input-wrap">
            <span class="field-icon">@</span>
            <input type="email" name="email" placeholder="name@college.edu" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-label">Password</div>
          <div class="input-wrap">
            <span class="field-icon">*</span>
            <input type="password" name="password" placeholder="Enter your password" required>
          </div>
        </div>
        <div class="login-meta">
          <small>Secure login enabled</small>
          <a href="#" class="forgot-link" data-tooltip="Connect this to reset workflow">Forgot Password?</a>
        </div>
        <button type="submit">Login to Dashboard</button>
      </form>
      <small>Demo users are available in <code>database/schema.sql</code></small>
    </div>
  </section>
  <section class="login-side">
    <div class="side-content">
      <h2>Track attainment. Improve outcomes. Engage stakeholders.</h2>
      <p>Monitor student performance, map CO-PO outcomes, and deliver timely interventions through one modern academic platform.</p>
      <div class="hero-art">
        <div class="hero-grid">
          <div class="hero-chip">CO Mapping</div>
          <div class="hero-chip">PO Analytics</div>
          <div class="hero-chip">Mentor Actions</div>
          <div class="hero-chip">Parent Alerts</div>
          <div class="hero-chip">Risk Tracking</div>
          <div class="hero-chip">Progress Trends</div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
