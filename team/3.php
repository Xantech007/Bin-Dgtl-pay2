<?php
session_start();

if(!isset($_SESSION['user_id'])){
header("Location: ../login.php");
exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT referral_code FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$my_code = $user['referral_code'];

/* level 1 */

$stmt = $pdo->prepare("SELECT referral_code FROM users WHERE referred_by=?");
$stmt->execute([$my_code]);
$l1 = $stmt->fetchAll(PDO::FETCH_COLUMN);

$l2=[];

if($l1){

$p=implode(',',array_fill(0,count($l1),'?'));

$stmt=$pdo->prepare("SELECT referral_code FROM users WHERE referred_by IN ($p)");
$stmt->execute($l1);

$l2=$stmt->fetchAll(PDO::FETCH_COLUMN);

}

$members=[];

if($l2){

$p=implode(',',array_fill(0,count($l2),'?'));

$stmt=$pdo->prepare("SELECT email,phone,vip_level,balance,created_at FROM users WHERE referred_by IN ($p)");
$stmt->execute($l2);

$members=$stmt->fetchAll(PDO::FETCH_ASSOC);

}

include "../inc/header.php";
?>

<div class="team-detail">

<div class="team-header">
<a href="../team.php"><i class="fa fa-arrow-left"></i></a>
<span>Level 3 Team</span>
</div>

<?php if(!$members): ?>

<div class="team-empty">
No level 3 referrals
</div>

<?php endif; ?>


<?php foreach($members as $m): ?>

<div class="team-member">

<div>
<strong>
<?php echo $m['email'] ?: $m['phone']; ?>
</strong>
</div>

<div class="member-info">
VIP<?php echo $m['vip_level']; ?> |
$<?php echo number_format($m['balance'],2); ?>
</div>

<div class="member-date">
<?php echo date("d M Y",strtotime($m['created_at'])); ?>
</div>

</div>

<?php endforeach; ?>

</div>

<?php include "../inc/footer.php"; ?>
