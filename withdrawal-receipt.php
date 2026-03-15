<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit;
}

$user_id=$_SESSION['user_id'];

if(!isset($_GET['id'])){
echo "Invalid receipt.";
exit;
}

$withdraw_id=intval($_GET['id']);

$stmt=$pdo->prepare("
SELECT * FROM withdrawals
WHERE id=? AND user_id=?
LIMIT 1
");

$stmt->execute([$withdraw_id,$user_id]);
$withdraw=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$withdraw){
echo "Receipt not found or unauthorized.";
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
<span>Amount</span>
<strong><?php echo number_format($withdraw['amount'],2)." ".$withdraw['currency']; ?></strong>
</div>

<div class="receipt-row">
<span>Fee</span>
<strong><?php echo number_format($withdraw['fee'],2)." ".$withdraw['currency']; ?></strong>
</div>

<div class="receipt-row">
<span>Received</span>
<strong><?php echo number_format($withdraw['received'],2)." ".$withdraw['currency']; ?></strong>
</div>

<?php if(!empty($withdraw['address'])): ?>

<div class="receipt-row">
<span>Wallet Address</span>
<strong><?php echo htmlspecialchars($withdraw['address']); ?></strong>
</div>

<?php endif; ?>

<?php if(!empty($withdraw['account_name'])): ?>

<div class="receipt-row">
<span>Account Name</span>
<strong><?php echo htmlspecialchars($withdraw['account_name']); ?></strong>
</div>

<?php endif; ?>

<?php if(!empty($withdraw['account_number'])): ?>

<div class="receipt-row">
<span>Account Number</span>
<strong><?php echo htmlspecialchars($withdraw['account_number']); ?></strong>
</div>

<?php endif; ?>

<?php if(!empty($withdraw['network_bank'])): ?>

<div class="receipt-row">
<span>Network / Bank</span>
<strong><?php echo htmlspecialchars($withdraw['network_bank']); ?></strong>
</div>

<?php endif; ?>

<div class="receipt-row">
<span>Status</span>
<strong class="receipt-status">

<?php
if($withdraw['status']==0) echo "Pending";
elseif($withdraw['status']==1) echo "Completed";
elseif($withdraw['status']==2) echo "Rejected";
?>

</strong>
</div>

<div class="receipt-row">
<span>Date</span>
<strong><?php echo $withdraw['created_at']; ?></strong>
</div>

</div>

<a href="index.php" class="receipt-btn">
Back to Dashboard
</a>

</div>

<?php include "inc/footer.php"; ?>
