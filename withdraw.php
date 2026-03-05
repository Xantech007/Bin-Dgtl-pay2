<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* FETCH USER DATA */
$stmt = $pdo->prepare("SELECT id,email,balance,password FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$balance = $user['balance'];

/* FETCH PAYMENT METHODS */
$methods = $pdo->query("SELECT * FROM payment_methods WHERE status=1")->fetchAll(PDO::FETCH_ASSOC);

$msg="";

if($_SERVER['REQUEST_METHOD']=="POST"){

$method = $_POST['method'];
$amount = floatval($_POST['amount']);
$address = trim($_POST['address']);
$password = $_POST['password'];

/* PASSWORD CHECK */
if(!password_verify($password,$user['password'])){

$msg="Incorrect password";

}elseif($amount > $balance){

$msg="Insufficient balance";

}elseif($amount <= 0){

$msg="Invalid withdrawal amount";

}else{

$fee = $amount * 0.05;
$received = $amount - $fee;

/* SAVE WITHDRAWAL */
$stmt=$pdo->prepare("
INSERT INTO withdrawals
(user_id,method,amount,address,fee,received)
VALUES(?,?,?,?,?,?)
");

$stmt->execute([
$user_id,
$method,
$amount,
$address,
$fee,
$received
]);

/* DEDUCT USER BALANCE */
$pdo->prepare("UPDATE users SET balance=balance-? WHERE id=?")
->execute([$amount,$user_id]);

$_SESSION['withdraw_msg']="Withdrawal request submitted successfully";

header("Location: index.php");
exit;

}
}
?>

<?php include "inc/header.php"; ?>

<div class="withdraw-header">
<a href="index.php"><i class="fa fa-arrow-left"></i></a>
<span>Withdraw</span>
</div>

<div class="withdraw-container">

<h3>Withdrawal account</h3>
<p class="withdraw-note">24 hours withdrawal</p>

<div class="withdraw-balance">
Total balance
<strong><?php echo number_format($balance,2); ?> USDT</strong>
</div>

<form method="POST">

<label>Withdrawal method:</label>

<div class="withdraw-methods">

<?php foreach($methods as $method): ?>

<label class="method">

<input type="radio"
name="method"
value="<?php echo htmlspecialchars($method['name']); ?>"
required>

<img src="<?php echo htmlspecialchars($method['image']); ?>" class="method-icon">

<?php echo htmlspecialchars($method['name']); ?>

</label>

<?php endforeach; ?>

</div>

<input
type="number"
step="0.01"
name="amount"
placeholder="Enter withdrawal amount"
required
class="withdraw-input">

<input
type="text"
name="address"
placeholder="Withdrawal Address"
required
class="withdraw-input">

<input
type="password"
name="password"
placeholder="Enter your password"
required
class="withdraw-input">

<div class="withdraw-summary">

<div>
Fees
<span id="fee">0 USDT</span>
</div>

<div>
Actually received
<span id="received">0 USDT</span>
</div>

</div>

<button class="withdraw-btn">
Confirm
</button>

</form>

<?php if(!empty($msg)): ?>

<div class="withdraw-error">
<?php echo htmlspecialchars($msg); ?>
</div>

<?php endif; ?>

<div class="withdraw-info">

TRC20 minimum withdrawal: $10  
BEP20, POLYGON minimum withdrawal is $1

</div>

</div>

<?php include "inc/footer.php"; ?>

<script>

/* AUTO CALCULATE FEES */

const amountInput=document.querySelector("input[name='amount']");

amountInput.addEventListener("input",function(){

let amount=parseFloat(this.value)||0;

let fee=amount*0.05;
let received=amount-fee;

document.getElementById("fee").innerText=fee.toFixed(2)+" USDT";
document.getElementById("received").innerText=received.toFixed(2)+" USDT";

});

</script>
