<?php
require_once __DIR__ . '/inc/header.php';

$message = '';
$error   = '';

/* HANDLE STATUS CHANGE */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $deposit_id = (int)($_POST['deposit_id'] ?? 0);
    $action     = $_POST['action'] ?? '';

    if ($deposit_id <= 0 || !in_array($action,['approve','reject'])) {

        $error="Invalid request.";

    } else {

        try {

            $pdo->beginTransaction();

            $stmt=$pdo->prepare("
                SELECT user_id, amount, status
                FROM deposits
                WHERE id=?
            ");
            $stmt->execute([$deposit_id]);
            $deposit=$stmt->fetch(PDO::FETCH_ASSOC);

            if(!$deposit){
                throw new Exception("Deposit not found.");
            }

            /* APPROVE */

            if($action==="approve"){

                if($deposit['status']!=1){

                    $stmt=$pdo->prepare("
                        UPDATE users
                        SET balance=balance+?
                        WHERE id=?
                    ");
                    $stmt->execute([$deposit['amount'],$deposit['user_id']]);

                }

                $stmt=$pdo->prepare("
                    UPDATE deposits
                    SET status=1, updated_at=NOW()
                    WHERE id=?
                ");
                $stmt->execute([$deposit_id]);

                $message="Deposit #{$deposit_id} approved.";

            }

            /* REJECT */

            if($action==="reject"){

                $stmt=$pdo->prepare("
                    UPDATE deposits
                    SET status=2, updated_at=NOW()
                    WHERE id=?
                ");
                $stmt->execute([$deposit_id]);

                $message="Deposit #{$deposit_id} rejected.";

            }

            $pdo->commit();

        } catch(Exception $e){

            $pdo->rollBack();
            $error="Operation failed: ".$e->getMessage();

        }

    }

}


/* LOAD ALL DEPOSITS */

try{

$stmt=$pdo->query("
SELECT
d.id,
d.amount,
d.paid_amount,
d.paid_currency,
d.proof,
d.status,
d.created_at,

u.email,
u.phone,

COALESCE(pm.name,'Unknown') AS method_name

FROM deposits d

LEFT JOIN users u ON d.user_id=u.id
LEFT JOIN payment_methods pm ON d.method_id=pm.id

ORDER BY d.created_at DESC
");

$deposits=$stmt->fetchAll(PDO::FETCH_ASSOC);

}catch(PDOException $e){

$error="Failed to load deposits: ".$e->getMessage();
$deposits=[];

}

?>

<main>

<h1 style="text-align:center;margin:2.5rem 0 2rem;">
Manage Deposits
</h1>


<?php if ($message): ?>
<div style="background:#238636;color:white;padding:1.2rem;border-radius:8px;margin-bottom:2rem;text-align:center;max-width:900px;margin-left:auto;margin-right:auto;">
<?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div style="background:#f85149;color:white;padding:1.2rem;border-radius:8px;margin-bottom:2rem;text-align:center;max-width:900px;margin-left:auto;margin-right:auto;">
<?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>


<?php if(empty($deposits)): ?>

<p style="text-align:center;color:var(--text-muted);font-size:1.1rem;padding:3rem 1rem;">
No deposits found.
</p>

<?php else: ?>

<div style="overflow-x:auto;margin:0 auto;max-width:100%;">

<table style="
width:100%;
max-width:1200px;
margin:0 auto 3rem;
border-collapse:separate;
border-spacing:0 12px;
">

<thead>

<tr style="background:#1f2937;color:#e6edf3;">

<th style="padding:1.2rem 1rem;">ID</th>
<th style="padding:1.2rem 1rem;">User</th>
<th style="padding:1.2rem 1rem;">Amount</th>
<th style="padding:1.2rem 1rem;">Paid</th>
<th style="padding:1.2rem 1rem;">Method</th>
<th style="padding:1.2rem 1rem;">Proof</th>
<th style="padding:1.2rem 1rem;">Status</th>
<th style="padding:1.2rem 1rem;">Date</th>
<th style="padding:1.2rem 1rem;">Actions</th>

</tr>

</thead>

<tbody>

<?php foreach($deposits as $dep): ?>

<tr style="background:var(--card);box-shadow:0 2px 8px rgba(0,0,0,0.3);">

<td style="padding:1.3rem 1rem;text-align:center;">
<?= $dep['id'] ?>
</td>

<td style="padding:1.3rem 1rem;">
<?= htmlspecialchars($dep['email'] ?? '—') ?><br>
<small style="color:var(--text-muted);">
<?= htmlspecialchars($dep['phone'] ?? '—') ?>
</small>
</td>

<td style="padding:1.3rem 1rem;text-align:right;font-weight:600;">
$<?= number_format($dep['amount'],2) ?> USD
</td>

<td style="padding:1.3rem 1rem;text-align:right;">
<?php if($dep['paid_amount']): ?>
<?= number_format($dep['paid_amount'],2)." ".htmlspecialchars($dep['paid_currency']); ?>
<?php else: ?>
—
<?php endif; ?>
</td>

<td style="padding:1.3rem 1rem;text-align:center;">
<?= htmlspecialchars($dep['method_name']) ?>
</td>

<td style="padding:1.3rem 1rem;text-align:center;">

<?php if(!empty($dep['proof'])): ?>

<?php $proof='../'.$dep['proof']; ?>

<div style="cursor:pointer" onclick="openPreview('<?= $proof ?>')">

<img src="<?= $proof ?>" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">

</div>

<?php else: ?>

<span style="color:var(--text-muted)">No proof</span>

<?php endif; ?>

</td>

<td style="padding:1.3rem 1rem;text-align:center;">

<?php
if($dep['status']==0){
echo "<span style='color:#f59e0b;'>Pending</span>";
}elseif($dep['status']==1){
echo "<span style='color:#22c55e;'>Approved</span>";
}else{
echo "<span style='color:#ef4444;'>Rejected</span>";
}
?>

</td>

<td style="padding:1.3rem 1rem;text-align:center;">
<?= date('Y-m-d H:i',strtotime($dep['created_at'])) ?>
</td>

<td style="padding:1.3rem 1rem;text-align:center;white-space:nowrap;">

<form method="POST" style="display:inline;">
<input type="hidden" name="deposit_id" value="<?= $dep['id'] ?>">
<input type="hidden" name="action" value="approve">

<button type="submit" class="btn green" style="padding:0.6rem 1.2rem;font-size:0.95rem;margin-right:0.5rem;">
Approve
</button>

</form>

<form method="POST" style="display:inline;">
<input type="hidden" name="deposit_id" value="<?= $dep['id'] ?>">
<input type="hidden" name="action" value="reject">

<button type="submit" class="btn red" style="padding:0.6rem 1.2rem;font-size:0.95rem;">
Reject
</button>

</form>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php endif; ?>


<!-- IMAGE PREVIEW -->

<div id="previewModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.95);z-index:2000;align-items:center;justify-content:center;">

<img id="previewImage" style="max-width:95%;max-height:95%;border-radius:12px;">

</div>

</main>


<script>

function openPreview(src){

document.getElementById("previewImage").src=src;
document.getElementById("previewModal").style.display="flex";

}

document.getElementById("previewModal").onclick=function(){

this.style.display="none";

}

</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
