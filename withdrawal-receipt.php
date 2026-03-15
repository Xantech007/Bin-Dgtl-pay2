<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit;
}

$user_id = $_SESSION['user_id'];

if(!isset($_GET['id'])){
header("Location: index.php");
exit;
}

$withdraw_id = intval($_GET['id']);

/* fetch withdrawal */

$stmt=$pdo->prepare("
SELECT * FROM withdrawals
WHERE id=? AND user_id=?
");

$stmt->execute([$withdraw_id,$user_id]);
$withdraw=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$withdraw){
header("Location: index.php");
exit;
}

?>

<?php include "inc/header.php"; ?>

<div class="receipt-container">

<h2>Withdrawal Receipt</h2>

<div class="receipt-box">

<div class="receipt-row">
<span>Transaction ID</span>
<strong>#<?php echo $withdraw['id']; ?></strong>
</div>

<div class="receipt-row">
<span>Method</span>
<strong><?php echo htmlspecialchars($withdraw['method']); ?></strong>
</div>

<div class="receipt-row">
<span>Currency</span>
<strong><?php echo $withdraw['currency']; ?></strong>
</div>

<div class="receipt-row">
<span>Amount (USD)</span>
<strong><?php echo number_format($withdraw['amount'],2); ?> USD</strong>
</div>

<div class="receipt-row">
<span>Fee</span>
<strong><?php echo $withdraw['fee']; ?> <?php echo $withdraw['currency']; ?></strong>
</div>

<div class="receipt-row">
<span>Received</span>
<strong><?php echo number_format($withdraw['received'],2); ?> <?php echo $withdraw['currency']; ?></strong>
</div>

<?php if(!empty($withdraw['address'])): ?>

<div class="receipt-row">
<span>Wallet Address</span>
<strong><?php echo htmlspecialchars($withdraw['address']); ?></strong>
</div>

<?php endif; ?>

<?php if(!empty($withdraw['account_number'])): ?>

<div class="receipt-row">
<span>Account Number</span>
<strong><?php echo htmlspecialchars($withdraw['account_number']); ?></strong>
</div>

<?php endif; ?>

<?php if(!empty($withdraw['account_name'])): ?>

<div class="receipt-row">
<span>Account Name</span>
<strong><?php echo htmlspecialchars($withdraw['account_name']); ?></strong>
</div>

<?php endif; ?>

<div class="receipt-row">
<span>Status</span>
<strong class="receipt-status">Pending</strong>
</div>

</div>

<a href="index.php" class="receipt-btn">
Back to Dashboard
</a>

</div>

<?php include "inc/footer.php"; ?>
