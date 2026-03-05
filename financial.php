<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit;
}

$user_id = $_SESSION['user_id'];

/* GET DEPOSITS */

$stmt=$pdo->prepare("SELECT amount,created_at FROM deposits WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$user_id]);
$deposits=$stmt->fetchAll(PDO::FETCH_ASSOC);

/* GET WITHDRAWALS */

$stmt=$pdo->prepare("SELECT amount,created_at FROM withdrawals WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$user_id]);
$withdrawals=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "inc/header.php"; ?>


<div class="financial-header">

<a onclick="goBack()">
<i class="fa fa-arrow-left"></i>
</a>

<span>Financial records</span>

</div>


<div class="financial-container">

<div class="financial-tabs">

<div class="tab active" onclick="switchTab('basic')">
Basic account
</div>

<div class="tab" onclick="switchTab('withdraw')">
Withdrawal account
</div>

</div>


<div class="financial-filter">
<i class="fa-solid fa-filter"></i>
</div>



<!-- BASIC ACCOUNT -->

<div class="financial-content active" id="basic">

<?php if(count($deposits) == 0): ?>

<div class="financial-empty">

<img src="assets/images/nodata.png">

<p>No data</p>

</div>

<?php else: ?>

<?php foreach($deposits as $row): ?>

<div class="financial-item">

<div>
Deposit
</div>

<div class="amount">
+<?php echo number_format($row['amount'],2); ?> USDT
</div>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>



<!-- WITHDRAW ACCOUNT -->

<div class="financial-content" id="withdraw">

<?php if(count($withdrawals) == 0): ?>

<div class="financial-empty">

<img src="assets/images/nodata.png">

<p>No data</p>

</div>

<?php else: ?>

<?php foreach($withdrawals as $row): ?>

<div class="financial-item">

<div>
Withdraw
</div>

<div class="amount minus">
-<?php echo number_format($row['amount'],2); ?> USDT
</div>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

</div>


<?php include "inc/footer.php"; ?>


<script>

function goBack(){

if(document.referrer){
window.history.back();
}else{
window.location.href="index.php";
}

}


/* TAB SWITCH */

function switchTab(tab){

document.querySelectorAll(".tab").forEach(t=>t.classList.remove("active"));
event.target.classList.add("active");

document.querySelectorAll(".financial-content").forEach(c=>c.classList.remove("active"));

document.getElementById(tab).classList.add("active");

}

</script>
