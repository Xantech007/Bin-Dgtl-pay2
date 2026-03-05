<?php
session_start();
require_once "config/database.php";

if(isset($_SESSION['user_id'])){
header("Location: index.php");
exit;
}

$error="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

$login=trim($_POST['login']);
$password=$_POST['password'];

$stmt=$pdo->prepare(
"SELECT * FROM users WHERE email=? OR phone=? LIMIT 1"
);

$stmt->execute([$login,$login]);
$user=$stmt->fetch(PDO::FETCH_ASSOC);

if($user && password_verify($password,$user['password'])){

$_SESSION['user_id']=$user['id'];
$_SESSION['email']=$user['email'];
$_SESSION['vip']=$user['vip_level'];
$_SESSION['balance']=$user['balance'];

header("Location: index.php");
exit;

}else{
$error="Invalid login credentials";
}
}
?>
<!DOCTYPE html>
<html>
<head>

<meta name="viewport" content="width=device-width,initial-scale=1">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<title>Login</title>

<style>

body{
margin:0;
background:#0f1115;
font-family:Arial;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
}

.wrapper{
width:100%;
max-width:700px;
padding:30px;
}

.box{
background:#14161c;
border-radius:20px;
padding:50px 40px;
box-shadow:0 0 40px rgba(0,0,0,.6);
}

.logo{
width:110px;
height:110px;
border-radius:50%;
display:block;
margin:auto;
object-fit:cover;
}

.title{
text-align:center;
color:#f0b24b;
font-size:28px;
margin:15px 0 25px;
}

.tabs{
display:flex;
margin-bottom:20px;
}

.tabs div{
flex:1;
text-align:center;
padding:12px;
color:#aaa;
cursor:pointer;
border-bottom:2px solid transparent;
}

.tabs .active{
color:#fff;
border-color:#fff;
}

.input{
display:flex;
align-items:center;
background:rgba(240,178,75,.25);
padding:15px;
border-radius:10px;
margin-bottom:15px;
}

.input i{
color:white;
margin-right:10px;
}

.input input{
border:none;
background:transparent;
outline:none;
color:white;
flex:1;
font-size:16px;
}

.btn{
width:100%;
padding:16px;
border:none;
border-radius:30px;
font-size:17px;
cursor:pointer;
margin-top:10px;
}

.signin{
background:#f0b24b;
color:white;
}

.signup{
background:#2b2b2b;
color:white;
}

.form{
display:none;
}

.form.active{
display:block;
}

.error{
color:red;
text-align:center;
margin-top:10px;
}

</style>
</head>

<body>

<div class="wrapper">
<div class="box">

<img src="assets/images/logo.webp" class="logo">

<div class="title">BINANCE DIGITAL</div>

<div class="tabs">
<div class="active" onclick="switchTab(event,'email')">Email Login</div>
<div onclick="switchTab(event,'phone')">Phone Login</div>
</div>

<!-- EMAIL LOGIN -->

<form method="POST" id="email" class="form active">

<div class="input">
<i class="fa fa-envelope"></i>
<input type="email" name="login" placeholder="E-mail" required>
</div>

<div class="input">
<i class="fa fa-lock"></i>
<input type="password" name="password" placeholder="Password" required>
</div>

<button type="submit" class="btn signin">Sign In</button>

<button type="button" class="btn signup"
onclick="location.href='register.php'">
Create Account
</button>

</form>

<!-- PHONE LOGIN -->

<form method="POST" id="phone" class="form">

<div class="input">
<i class="fa fa-phone"></i>
<input type="text" name="login" placeholder="Phone Number" required>
</div>

<div class="input">
<i class="fa fa-lock"></i>
<input type="password" name="password" placeholder="Password" required>
</div>

<button type="submit" class="btn signin">Sign In</button>

<button type="button" class="btn signup"
onclick="location.href='register.php'">
Create Account
</button>

</form>

<?php if($error): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

</div>
</div>

<script>

function switchTab(event,id){

document.querySelectorAll('.form')
.forEach(f=>f.classList.remove('active'));

document.getElementById(id)
.classList.add('active');

document.querySelectorAll('.tabs div')
.forEach(t=>t.classList.remove('active'));

event.target.classList.add('active');

}

</script>

</body>
</html>
