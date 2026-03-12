<?php
// admin/dashboard.php
require_once __DIR__ . '/inc/header.php';

// ────────────────────────────────────────────────
//   Fetch statistics (adjust table/column names to match your real schema)
// ────────────────────────────────────────────────
try {
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $stmt->fetchColumn();

    // Total deposits (sum of confirmed deposits)
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM deposits WHERE status = 'approved'");
    $total_deposits = number_format($stmt->fetchColumn(), 2);

    // Total withdrawals (sum of approved withdrawals)
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM withdrawals WHERE status = 'approved'");
    $total_withdrawals = number_format($stmt->fetchColumn(), 2);

    // Number of VIP users (adjust condition to your logic)
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_vip = 1 OR plan = 'vip' OR level >= 3"); // ← customize
    $total_vips = $stmt->fetchColumn();

} catch (PDOException $e) {
    // In production: log error
    $total_users = $total_deposits = $total_withdrawals = $total_vips = "Error";
}
?>

<main>
  <div class="stats-grid">
    <div class="card">
      <div class="card-icon" style="color:#58a6ff;">
        <i class="fas fa-users"></i>
      </div>
      <div class="card-value"><?= htmlspecialchars($total_users) ?></div>
      <div class="card-label">Total Users</div>
    </div>

    <div class="card">
      <div class="card-icon" style="color:#238636;">
        <i class="fas fa-arrow-down"></i>
      </div>
      <div class="card-value">$<?= htmlspecialchars($total_deposits) ?></div>
      <div class="card-label">Total Deposits</div>
    </div>

    <div class="card">
      <div class="card-icon" style="color:#f85149;">
        <i class="fas fa-arrow-up"></i>
      </div>
      <div class="card-value">$<?= htmlspecialchars($total_withdrawals) ?></div>
      <div class="card-label">Total Withdrawals</div>
    </div>

    <div class="card">
      <div class="card-icon" style="color:#d29922;">
        <i class="fas fa-crown"></i>
      </div>
      <div class="card-value"><?= htmlspecialchars($total_vips) ?></div>
      <div class="card-label">VIP Members</div>
    </div>
  </div>

  <h2 style="text-align:center; margin:2.5rem 0 1.8rem; font-size:1.6rem;">Management Sections</h2>

  <div class="actions-grid">
    <a href="manage-users.php"       class="btn"><i class="fas fa-user-friends"></i> Manage Users</a>
    <a href="manage-deposits.php"    class="btn green"><i class="fas fa-wallet"></i> Manage Deposits</a>
    <a href="manage-withdrawals.php" class="btn red"><i class="fas fa-money-bill-wave"></i> Manage Withdrawals</a>
    <a href="region-settings.php"    class="btn"><i class="fas fa-globe"></i> Region Settings</a>
    <a href="manage-vip.php"         class="btn"><i class="fas fa-crown"></i> Manage VIP</a>
    <a href="manage-news.php"        class="btn"><i class="fas fa-newspaper"></i> Manage News</a>
  </div>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
