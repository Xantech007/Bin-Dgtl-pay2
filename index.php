<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT email,vip_level,balance FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$user_email = $user['email'];
$user_vip = "VIP".$user['vip_level'];
$user_balance = $user['balance'];

/* NEWS */
$query = $pdo->query("SELECT title FROM news ORDER BY id DESC");

/* VIP LIST */
$vipQuery = $pdo->query("SELECT * FROM vip WHERE status=1 ORDER BY id ASC");

?>

<?php include "inc/header.php"; ?>

<!-- NEWS -->
<div class="news-wrapper">
<div class="news-marquee">
<div class="news-content">

<?php
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
echo "<span class='news-item'>".htmlspecialchars($row['title'])."</span>";
}
?>

</div>
</div>
</div>

<!-- DASHBOARD -->

<div class="dashboard-container">

<div class="dashboard-top">

<div class="user-info">
<span class="user-email"><?php echo $user_email; ?></span>
<span class="vip-badge"><?php echo $user_vip; ?></span>
</div>

<a href="wallet.php" class="wallet-btn">
<i class="fa-solid fa-wallet"></i>
</a>

</div>

<div class="balance-box">
<span>Balance</span>
<strong>$<?php echo number_format($user_balance,2); ?></strong>
</div>

<div class="dashboard-actions">

<a href="recharge.php" class="action-item">
<div class="icon-circle"><i class="fa-solid fa-money-bill-wave"></i></div>
<span>Recharge</span>
</a>

<a href="withdraw.php" class="action-item">
<div class="icon-circle"><i class="fa-solid fa-arrow-up-from-bracket"></i></div>
<span>Withdraw</span>
</a>

<a href="app.php" class="action-item">
<div class="icon-circle"><i class="fa-solid fa-mobile-screen"></i></div>
<span>App</span>
</a>

<a href="company.php" class="action-item">
<div class="icon-circle"><i class="fa-solid fa-building"></i></div>
<span>Company Profile</span>
</a>

</div>

</div>

<!-- BANNER -->

<div class="banner-slider">
<div class="banner-track">
<img src="assets/images/banner1.jpeg">
<img src="assets/images/banner2.jpeg">
</div>
</div>

<!-- TASK HALL -->

<div class="task-section">
<h2 class="task-title">Task Hall</h2>

<?php while($vip = $vipQuery->fetch(PDO::FETCH_ASSOC)): ?>

<div class="task-card">

<img src="assets/images/vip.jpg" class="task-left">

<div class="task-content">

<h3><?php echo htmlspecialchars($vip['name']); ?></h3>

<p>
Mine rate - $<?php echo $vip['mine_rate']; ?> per minute  
Earn $<?php echo $vip['daily_income']; ?> daily
</p>

</div>

<div class="task-right">

<?php if($user['vip_level'] < $vip['id']): ?>

<i class="fa-solid fa-lock"></i>

<a href="activate_vip.php?id=<?php echo $vip['id']; ?>" class="vip-btn">
Activation fee: $<?php echo $vip['activation_fee']; ?>
</a>

<?php else: ?>

<a href="mine.php?vip=<?php echo $vip['id']; ?>" class="vip-active">
Start Mining
</a>

<?php endif; ?>

</div>

</div>

<?php endwhile; ?>

</div>

<?php include "inc/footer.php"; ?>
