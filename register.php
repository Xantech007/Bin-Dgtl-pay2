<?php
session_start();
require_once "config/database.php";

/* Prevent logged users from opening register */
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

$msg = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $type = $_POST['type'];
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);
    $invite = trim($_POST['invite']);

    if($password != $confirm){
        $msg = "Passwords do not match";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        if($type == "email"){
            $email = trim($_POST['email']);
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if($check->rowCount() > 0){
                $msg = "Email already registered";
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO users(email, password, invite_code, vip_level, balance)
                     VALUES(?, ?, ?, ?, ?)"
                );
                $stmt->execute([$email, $hash, $invite, 0, 0]);
                header("Location: login.php");
                exit;
            }
        } else {
            $phone = trim($_POST['phone']);
            $check = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
            $check->execute([$phone]);
            if($check->rowCount() > 0){
                $msg = "Phone number already registered";
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO users(phone, password, invite_code, vip_level, balance)
                     VALUES(?, ?, ?, ?, ?)"
                );
                $stmt->execute([$phone, $hash, $invite, 0, 0]);
                header("Location: login.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Register</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            background: #0f1115;
            font-family: Arial, sans-serif;
        }

        /* Full-screen layout – no big wrapper anymore */
        .box {
            min-height: 100vh;
            background: #14161c;
            padding: 60px 24px 40px;           /* generous top, smaller sides/bottom on mobile */
            position: relative;
            overflow: visible;
            box-shadow: 0 0 40px rgba(0,0,0,.6);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Floating background - non-interactive */
        .bg {
            position: absolute;
            right: -50px;
            bottom: -30px;
            width: 360px;
            opacity: .25;
            animation: float 4s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-20px); }
        }

        .logo {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            display: block;
            margin: 0 auto 20px;
            object-fit: cover;
        }

        .title {
            text-align: center;
            color: #f0b24b;
            font-size: 32px;
            margin: 0 0 40px;
        }

        .tabs {
            display: flex;
            margin-bottom: 30px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .tabs div {
            flex: 1;
            text-align: center;
            padding: 14px;
            color: #aaa;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-size: 16px;
        }

        .tabs .active {
            color: #fff;
            border-color: #f0b24b;
        }

        .form-container {
            max-width: 420px;
            margin: 0 auto;
            width: 100%;
        }

        .input {
            display: flex;
            align-items: center;
            background: rgba(240,178,75,.25);
            padding: 16px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            backdrop-filter: blur(6px);
        }

        .input i {
            color: white;
            margin-right: 14px;
            font-size: 20px;
        }

        .input input {
            border: none;
            background: transparent;
            outline: none;
            color: white;
            flex: 1;
            font-size: 17px;
        }

        .btn {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 30px;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 12px;
            position: relative;
            z-index: 2;
            touch-action: manipulation;
        }

        .signup {
            background: #f0b24b;
            color: #000;
        }

        .signin {
            background: #2b2b2b;
            color: #fff;
        }

        .form {
            display: none;
        }

        .form.active {
            display: block;
        }

        .msg {
            color: #ff6b6b;
            text-align: center;
            margin-top: 16px;
            font-size: 15px;
        }

        /* Desktop – limit max width of content */
        @media (min-width: 768px) {
            .box {
                padding: 80px 60px;
                max-width: 580px;
                margin: 0 auto;
                min-height: auto;
                border-radius: 24px;
            }
        }

        /* Very small phones – tighter padding */
        @media (max-width: 360px) {
            .box {
                padding: 50px 18px 30px;
            }
            .title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

<div class="box">
    <img src="assets/images/wallet.png" class="bg" alt="background decoration">

    <img src="assets/images/logo.webp" class="logo" alt="Logo">

    <div class="title">Create Account</div>

    <div class="tabs">
        <div class="active" onclick="switchTab(event, 'emailForm')">Email Sign Up</div>
        <div onclick="switchTab(event, 'phoneForm')">Phone Sign Up</div>
    </div>

    <div class="form-container">

        <!-- EMAIL REGISTER -->
        <form method="POST" id="emailForm" class="form active">
            <input type="hidden" name="type" value="email">
            <div class="input">
                <i class="fa fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required autocomplete="email">
            </div>
            <div class="input">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
            </div>
            <div class="input">
                <i class="fa fa-lock"></i>
                <input type="password" name="confirm" placeholder="Re-enter Password" required autocomplete="new-password">
            </div>
            <div class="input">
                <i class="fa fa-thumbs-up"></i>
                <input type="text" name="invite" placeholder="Invitation Code" autocomplete="off">
            </div>
            <button type="submit" class="btn signup">Sign Up</button>
            <button type="button" class="btn signin" onclick="location.href='login.php'">Already have an account? Sign In</button>
        </form>

        <!-- PHONE REGISTER -->
        <form method="POST" id="phoneForm" class="form">
            <input type="hidden" name="type" value="phone">
            <div class="input">
                <i class="fa fa-phone"></i>
                <input type="tel" name="phone" placeholder="Phone Number" required autocomplete="tel">
            </div>
            <div class="input">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
            </div>
            <div class="input">
                <i class="fa fa-lock"></i>
                <input type="password" name="confirm" placeholder="Re-enter Password" required autocomplete="new-password">
            </div>
            <div class="input">
                <i class="fa fa-thumbs-up"></i>
                <input type="text" name="invite" placeholder="Invitation Code" autocomplete="off">
            </div>
            <button type="submit" class="btn signup">Sign Up</button>
            <button type="button" class="btn signin" onclick="location.href='login.php'">Already have an account? Sign In</button>
        </form>

        <?php if($msg): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

    </div>
</div>

<script>
function switchTab(event, id) {
    document.querySelectorAll('.form').forEach(f => f.classList.remove('active'));
    document.getElementById(id).classList.add('active');

    document.querySelectorAll('.tabs div').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');
}
</script>

</body>
</html>
