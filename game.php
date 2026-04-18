<?php
// Simple Bubble Shooter - PHP wrapper
session_start();
if (!isset($_SESSION['highscore'])) $_SESSION['highscore'] = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Bubble Shooter</title>
    <style>
        body { margin:0; background:#111; font-family:Arial; text-align:center; color:#fff; }
        canvas { border:2px solid #333; background:#000; margin-top:20px; }
        h1 { margin:20px; }
        .score { font-size:22px; margin:10px; }
    </style>
</head>
<body>
    <h1>🎯 Bubble Shooter</h1>
    <div class="score">
        Score: <span id="score">0</span> &nbsp;&nbsp;&nbsp; 
        High Score: <span id="highscore"><?php echo $_SESSION['highscore']; ?></span>
    </div>
    <canvas id="game" width="400" height="500"></canvas>
    <p>Move mouse to aim • Click to shoot</p>

<script>
// ============== Simple Bubble Shooter ==============
const canvas = document.getElementById('game');
const ctx = canvas.getContext('2d');
let score = 0;
let bubbles = [];
let shooter = { x: 200, y: 460, angle: 0 };
let currentBubble = null;
let nextColor = getRandomColor();
let gameOver = false;

const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff'];

function getRandomColor() {
    return colors[Math.floor(Math.random() * colors.length)];
}

// Create initial bubbles at top
function initBubbles() {
    bubbles = [];
    for (let row = 0; row < 5; row++) {
        for (let col = 0; col < 8; col++) {
            bubbles.push({
                x: 50 + col * 45,
                y: 40 + row * 45,
                radius: 18,
                color: getRandomColor()
            });
        }
    }
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw ceiling line
    ctx.strokeStyle = '#555';
    ctx.lineWidth = 4;
    ctx.beginPath();
    ctx.moveTo(0, 30);
    ctx.lineTo(canvas.width, 30);
    ctx.stroke();

    // Draw stationary bubbles
    for (let b of bubbles) {
        ctx.fillStyle = b.color;
        ctx.beginPath();
        ctx.arc(b.x, b.y, b.radius, 0, Math.PI * 2);
        ctx.fill();
        ctx.strokeStyle = '#fff';
        ctx.lineWidth = 2;
        ctx.stroke();
    }

    // Draw shooter cannon
    ctx.strokeStyle = '#ccc';
    ctx.lineWidth = 12;
    ctx.beginPath();
    ctx.moveTo(200, 460);
    ctx.lineTo(200 + Math.cos(shooter.angle) * 40, 460 + Math.sin(shooter.angle) * 40);
    ctx.stroke();

    // Draw current bubble in cannon
    if (currentBubble) {
        ctx.fillStyle = currentBubble.color;
        ctx.beginPath();
        ctx.arc(currentBubble.x, currentBubble.y, currentBubble.radius, 0, Math.PI * 2);
        ctx.fill();
    }

    // Draw flying bubble if any
    if (currentBubble && currentBubble.speed) {
        ctx.fillStyle = currentBubble.color;
        ctx.beginPath();
        ctx.arc(currentBubble.x, currentBubble.y, currentBubble.radius, 0, Math.PI * 2);
        ctx.fill();
    }
}

function update() {
    if (!currentBubble || !currentBubble.speed) return;

    // Move flying bubble
    currentBubble.x += Math.cos(currentBubble.angle) * currentBubble.speed;
    currentBubble.y += Math.sin(currentBubble.angle) * currentBubble.speed;

    // Wall bounce
    if (currentBubble.x - currentBubble.radius < 0 || currentBubble.x + currentBubble.radius > canvas.width) {
        currentBubble.angle = Math.PI - currentBubble.angle;
    }

    // Hit top or stationary bubble
    if (currentBubble.y - currentBubble.radius < 40) {
        attachBubble();
        return;
    }

    // Check collision with other bubbles
    for (let i = 0; i < bubbles.length; i++) {
        let b = bubbles[i];
        let dx = currentBubble.x - b.x;
        let dy = currentBubble.y - b.y;
        let dist = Math.sqrt(dx*dx + dy*dy);
        if (dist < currentBubble.radius + b.radius + 2) {
            attachBubble();
            return;
        }
    }
}

function attachBubble() {
    if (!currentBubble) return;
    currentBubble.speed = 0;
    bubbles.push(currentBubble);

    // Very simple pop (remove if same color nearby - basic version)
    checkAndPop(currentBubble);

    // Load next bubble
    currentBubble = {
        x: 200,
        y: 460,
        radius: 18,
        color: nextColor,
        angle: 0,
        speed: 0
    };
    nextColor = getRandomColor();

    // Check game over (if bubbles reach bottom)
    for (let b of bubbles) {
        if (b.y > 420) {
            gameOver = true;
        }
    }
}

function checkAndPop(newBubble) {
    // Simple pop: remove the new bubble + any same color touching it (very basic match)
    let toRemove = [];
    for (let i = 0; i < bubbles.length; i++) {
        let b = bubbles[i];
        if (b.color === newBubble.color) {
            let dx = newBubble.x - b.x;
            let dy = newBubble.y - b.y;
            if (Math.sqrt(dx*dx + dy*dy) < 50) {
                toRemove.push(i);
            }
        }
    }
    if (toRemove.length >= 2) {  // at least 3 including the new one
        toRemove.sort((a,b)=>b-a);
        for (let i of toRemove) {
            bubbles.splice(i, 1);
        }
        score += (toRemove.length + 1) * 10;
        document.getElementById('score').textContent = score;
    }
}

function shoot() {
    if (!currentBubble || currentBubble.speed) return;
    currentBubble.angle = shooter.angle;
    currentBubble.speed = 9;
    currentBubble.x = 200;
    currentBubble.y = 440;
}

// Mouse controls
canvas.addEventListener('mousemove', (e) => {
    if (gameOver) return;
    const rect = canvas.getBoundingClientRect();
    const mx = e.clientX - rect.left;
    const my = e.clientY - rect.top;
    shooter.angle = Math.atan2(my - 460, mx - 200);
    shooter.angle = Math.max(Math.min(shooter.angle, Math.PI/2), -Math.PI/2); // limit angle
});

canvas.addEventListener('click', () => {
    if (!gameOver) shoot();
});

// Game loop
function loop() {
    if (!gameOver) {
        update();
        draw();
    } else {
        ctx.fillStyle = 'rgba(0,0,0,0.7)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#fff';
        ctx.font = '30px Arial';
        ctx.fillText('GAME OVER', 80, 250);
        ctx.font = '20px Arial';
        ctx.fillText('Score: ' + score, 140, 300);
        
        if (score > <?php echo $_SESSION['highscore']; ?>) {
            <?php $_SESSION['highscore'] = "' + score + '"; ?> // Note: this is just for demo, real update needs AJAX or form
        }
    }
    requestAnimationFrame(loop);
}

// Start game
initBubbles();
currentBubble = {
    x: 200,
    y: 460,
    radius: 18,
    color: nextColor,
    angle: 0,
    speed: 0
};
nextColor = getRandomColor();
loop();
</script>
</body>
</html>
