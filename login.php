<?php
session_start();
require_once "config/database.php";

/* Redirect if already logged in */
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

$error = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare(
        "SELECT * FROM users 
         WHERE email = ? OR phone = ? LIMIT 1"
    );
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])){
        /* SAVE SESSION DATA */
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['vip_level'] = $user['vip_level'];
        $_SESSION['balance']   = $user['balance'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Login</title>
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

        /* Full-screen stretched layout */
        .box {
            min-height: 100vh;
            background: #14161c;
            padding: 60px 24px 40px;
            position: relative;
            overflow: visible;
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

        .signin {
            background: #f0b24b;
            color: #000;
        }

        .signup {
            background: #2b2b2b;
            color: #fff;
        }

        .error {
            color: #ff6b6b;
            text-align: center;
            margin-top: 16px;
            font-size: 15px;
        }

        /* Desktop – limit content width */
        @media (min-width: 768px) {
            .box {
                padding: 80px 60px;
                max-width: 580px;
                margin: 0 auto;
                min-height: auto;
                border-radius: 24px;
            }
        }

        /* Very small screens */
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

    <div class="title">BINANCE DIGITAL</div>

    <div class="tabs">
        <div class="active" onclick="switchTab(event, 'email')">Email Login</div>
        <div onclick="switchTab(event, 'phone')">Phone Login</div>
    </div>

    <div class="form-container">

        <!-- EMAIL LOGIN -->
        <form method="POST" id="email" class="form active">
            <div class="input">
                <i class="fa fa-envelope"></i>
                <input type="email" name="login" placeholder="Email" required autocomplete="email">
            </div>
            <div class="input">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn signin">Sign In</button>
            <button type="button" class="btn signup" onclick="location.href='register.php'">Don't have an account? Sign Up</button>
        </form>

        <!-- PHONE LOGIN -->
        <form method="POST" id="phone" class="form">
            <div class="input">
                <i class="fa fa-phone"></i>
                <input type="tel" name="login" placeholder="Phone Number" required autocomplete="tel">
            </div>
            <div class="input">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn signin">Sign In</button>
            <button type="button" class="btn signup" onclick="location.href='register.php'">Don't have an account? Sign Up</button>
        </form>

        <?php if($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
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
