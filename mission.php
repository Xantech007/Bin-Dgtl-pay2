<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit;
}

$user_id = $_SESSION['user_id'];

/* FETCH RESET TIME */
$stmt = $pdo->query("SELECT reset_time FROM task_reset LIMIT 1");
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

$reset_time = strtotime($reset['reset_time']);

/* TASK COUNTS (example) */
$today_tasks = 0;
$remaining_tasks = 0;
?>

<?php include "inc/header.php"; ?>

<div class="mission-header">
<a href="index.php"><i class="fa fa-arrow-left"></i></a>
<span>BINANCE DIGITAL</span>
</div>


<!-- RESET TIMER -->

<div class="task-reset-box">

<i class="fa-solid fa-clock"></i>

<span>Task reset</span>

<strong id="countdown"></strong>

</div>


<!-- TASK STATS -->

<div class="task-stats">

<div>
<p>All tasks for today</p>
<strong><?php echo $today_tasks; ?></strong>
</div>

<div>
<p>Today's remaining tasks</p>
<strong><?php echo $remaining_tasks; ?></strong>
</div>

</div>


<!-- TASK PANEL -->

<div class="task-panel">

<div class="task-tabs">

<span class="tab active" onclick="switchTab('progress')">
In progress
</span>

<span class="tab" onclick="switchTab('completed')">
Completed
</span>

</div>


<!-- IN PROGRESS -->

<div id="progress" class="task-content active">

<div class="no-data">
<i class="fa-solid fa-cloud"></i>
<p>No data</p>
</div>

</div>


<!-- COMPLETED -->

<div id="completed" class="task-content">

<div class="no-data">
<i class="fa-solid fa-building"></i>
<p>No data</p>
</div>

</div>

</div>


<?php include "inc/footer.php"; ?>

<script>

/* RESET COUNTDOWN */

var resetTime = <?php echo $reset_time * 1000; ?>;

function updateCountdown(){

var now = new Date().getTime();

var distance = resetTime - now;

if(distance < 0){
location.reload();
return;
}

var hours = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));
var minutes = Math.floor((distance % (1000*60*60)) / (1000*60));
var seconds = Math.floor((distance % (1000*60)) / 1000);

document.getElementById("countdown").innerHTML =
hours.toString().padStart(2,'0') + ":" +
minutes.toString().padStart(2,'0') + ":" +
seconds.toString().padStart(2,'0');

}

setInterval(updateCountdown,1000);


/* TAB SWITCH */

function switchTab(tab){

document.querySelectorAll(".task-content")
.forEach(el=>el.classList.remove("active"));

document.getElementById(tab).classList.add("active");

document.querySelectorAll(".tab")
.forEach(el=>el.classList.remove("active"));

event.target.classList.add("active");

}

</script>
