<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/lib/solicitari.php';
$account = requireUser();
$id = idFrom($_GET['id'] ?? null);
$admin = $account['role'] === 'admin';
$sql = 'SELECT r.id, r.status, r.created_at, r.note, r.decided_at, u.name AS author, d.name AS decider
    FROM requests r JOIN users u ON u.id=r.user_id LEFT JOIN users d ON d.id=r.decided_by WHERE r.id=?';
$request = query($sql . ($admin ? '' : ' AND r.user_id=?'), $admin ? [$id] : [$id, $account['id']])->fetch();
if (!$request) fail(404, 'Solicitare negăsită', 'Solicitarea nu este disponibilă în contul tău.');
if (isPost()) {
    requireAdmin();
    protectPost();
    $decision = field($_POST, 'decizie');
    if (!in_array($decision, ['approved', 'rejected'], true)) fail(422, 'Decizie nevalidă', 'Alege Aprobă sau Respinge.');
    try {
        decideRequest($id, (int) $account['id'], $decision);
        flash($decision === 'approved' ? 'Solicitare aprobată. Cantitățile disponibile au fost actualizate.' : 'Solicitare respinsă.');
    } catch (DomainException $error) {
        flash($error->getMessage(), 'error');
    }
    redirect('pagini/solicitare.php', ['id' => $id]);
}
$lines = query('SELECT item_name, quantity FROM request_items WHERE request_id=? ORDER BY item_id', [$id])->fetchAll();
$titluPagina = 'Solicitare #' . $id;
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Detalii solicitare</p><h1>Solicitare #<?= e($id) ?></h1><p><?= e($request['author']) ?> · <?= e($request['created_at']) ?> · <?php statusLabel($request['status']); ?></p></div>
<section class="content-panel">
    <h2>Task-uri solicitate</h2>
    <?php if ($request['note'] !== ''): ?><p><strong>Notă:</strong> <?= nl2br(e($request['note'])) ?></p><?php endif; ?>
    <div class="table-wrap"><table><thead><tr><th scope="col">Task</th><th scope="col">Cantitate solicitată</th></tr></thead>
    <tbody><?php foreach ($lines as $line): ?><tr><th scope="row"><?= e($line['item_name']) ?></th><td><?= e($line['quantity']) ?></td></tr><?php endforeach; ?></tbody></table></div>
    <?php if ($request['decided_at']): ?><p class="quiet">Decizie: <?= e($request['decider']) ?> · <?= e($request['decided_at']) ?></p><?php endif; ?>
    <?php if ($admin && $request['status'] === 'pending'): ?>
    <form method="post" class="form-actions decision-actions">
        <?php csrfField(); ?>
        <button class="button primary" type="submit" name="decizie" value="approved">Aprobă</button>
        <button class="button secondary" type="submit" name="decizie" value="rejected">Respinge</button>
    </form>
    <?php endif; ?>
    <a class="text-link" href="<?= e(url('pagini/solicitari.php', $admin ? ['toate' => 1] : [])) ?>">Înapoi la solicitări</a>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
