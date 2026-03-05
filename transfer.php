<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit;
}

$user_id = $_SESSION['user_id'];

/* FETCH USER */

$stmt=$pdo->prepare("SELECT balance,withdrawal_balance,password FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user=$stmt->fetch(PDO::FETCH_ASSOC);

$basic=$user['balance'];
$withdraw=$user['withdrawal_balance'];

$msg="";

if($_SERVER['REQUEST_METHOD']=="POST"){

$amount=floatval($_POST['amount']);
$password=$_POST['password'];
$from=$_POST['from_account'];
$to=$_POST['to_account'];

if(!password_verify($password,$user['password'])){
$msg="Incorrect password";
}

elseif($amount<=0){
$msg="Invalid amount";
}

else{

/* CHECK SOURCE BALANCE */

$source_balance = ($from=="basic") ? $basic : $withdraw;

if($amount>$source_balance){
$msg="Insufficient balance";
}else{

if($from=="basic"){

$pdo->prepare("
UPDATE users
SET balance=balance-?,
withdrawal_balance=withdrawal_balance+?
WHERE id=?
")->execute([$amount,$amount,$user_id]);

}

else{

$pdo->prepare("
UPDATE users
SET withdrawal_balance=withdrawal_balance-?,
balance=balance+?
WHERE id=?
")->execute([$amount,$amount,$user_id]);

}

$_SESSION['transfer_msg']="Transfer completed successfully";
header("Location: transfer.php");
exit;

}

}

}
?>

<?php include "inc/header.php"; ?>


<div class="transfer-header">

<a onclick="goBack()">
<i class="fa fa-arrow-left"></i>
</a>

<span>Transfer</span>

</div>


<?php if(isset($_SESSION['transfer_msg'])): ?>

<div class="transfer-success">
<?php
echo $_SESSION['transfer_msg'];
unset($_SESSION['transfer_msg']);
?>
</div>

<?php endif; ?>


<div class="transfer-wrapper">


<!-- BALANCE PANEL -->

<div class="transfer-balance">

<div class="transfer-box" id="leftBox">

<p id="leftLabel">Withdrawal account</p>
<h3 id="leftBalance"><?php echo number_format($withdraw,2); ?></h3>

</div>


<div class="transfer-icon" id="swapBtn">
<i class="fa-solid fa-right-left"></i>
</div>


<div class="transfer-box" id="rightBox">

<p id="rightLabel">Basic account</p>
<h3 id="rightBalance"><?php echo number_format($basic,2); ?></h3>

</div>

</div>



<!-- TRANSFER FORM -->

<div class="transfer-container">

<form method="POST">

<input type="hidden" name="from_account" id="from_account" value="withdraw">
<input type="hidden" name="to_account" id="to_account" value="basic">

<input
type="number"
step="0.01"
name="amount"
placeholder="Conversion quantity"
required
class="transfer-input">


<div class="password-box">

<input
type="password"
name="password"
placeholder="Password"
required
class="transfer-input">

<i class="fa fa-eye toggle-pass"></i>

</div>


<button class="transfer-btn">
Confirm
</button>

</form>


<?php if($msg): ?>

<div class="transfer-error">
<?php echo htmlspecialchars($msg); ?>
</div>

<?php endif; ?>

</div>

</div>


<?php include "inc/footer.php"; ?>


<script>

/* BACK BUTTON */

function goBack(){
if(document.referrer){
window.history.back();
}else{
window.location.href="index.php";
}
}


/* PASSWORD TOGGLE */

document.querySelector(".toggle-pass").onclick=function(){
let input=document.querySelector("input[name='password']");
input.type=input.type==="password"?"text":"password";
}


/* SWAP LOGIC */

let left="withdraw";
let right="basic";

document.getElementById("swapBtn").onclick=function(){

const leftLabel=document.getElementById("leftLabel");
const rightLabel=document.getElementById("rightLabel");

const leftBalance=document.getElementById("leftBalance");
const rightBalance=document.getElementById("rightBalance");

/* animation */

leftBalance.classList.add("swap-anim");
rightBalance.classList.add("swap-anim");

setTimeout(()=>{

let tempLabel=leftLabel.innerText;
leftLabel.innerText=rightLabel.innerText;
rightLabel.innerText=tempLabel;

let tempBalance=leftBalance.innerText;
leftBalance.innerText=rightBalance.innerText;
rightBalance.innerText=tempBalance;

/* swap variables */

let temp=left;
left=right;
right=temp;

/* update form */

document.getElementById("from_account").value=left;
document.getElementById("to_account").value=right;

leftBalance.classList.remove("swap-anim");
rightBalance.classList.remove("swap-anim");

},250);

}

</script>
