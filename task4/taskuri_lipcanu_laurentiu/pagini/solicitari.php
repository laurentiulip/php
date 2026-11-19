<?php
require_once dirname(__DIR__) . '/config.php';
$account = requireUser();
$all = ($_GET['toate'] ?? '') === '1';
if ($all) requireAdmin();
$titluPagina = $all ? 'Toate solicitările' : 'Solicitările mele';
$paginaActiva = $all ? 'toate-solicitarile' : 'solicitari';
$requests = query('SELECT r.id, r.created_at, r.status, u.name AS author
    FROM requests r JOIN users u ON u.id=r.user_id ' . ($all ? '' : 'WHERE r.user_id=? ') . 'ORDER BY r.created_at DESC, r.id DESC',
    $all ? [] : [$account['id']])->fetchAll();
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Istoric</p><h1><?= e($titluPagina) ?></h1><p>Deschide o solicitare ca să vezi task-urile, cantitățile și statutul.</p></div>
<section class="content-panel">
    <div class="section-heading"><h2>Lista solicitărilor</h2><a class="button primary" href="<?= e(url('pagini/solicitare-noua.php')) ?>">Solicitare nouă</a></div>
    <?php if (!$requests): ?><p class="notice">Nu există solicitări în această listă.</p><?php else: ?>
        <div class="table-wrap"><table><thead><tr><th scope="col">Număr</th><th scope="col">Creată la</th><?php if ($all): ?><th scope="col">Utilizator</th><?php endif; ?><th scope="col">Statut</th></tr></thead>
        <tbody><?php foreach ($requests as $r): ?><tr><th scope="row"><a class="table-link" href="<?= e(url('pagini/solicitare.php', ['id' => $r['id']])) ?>">#<?= e($r['id']) ?></a></th><td><?= e($r['created_at']) ?></td><?php if ($all): ?><td><?= e($r['author']) ?></td><?php endif; ?><td><?php statusLabel($r['status']); ?></td></tr><?php endforeach; ?></tbody></table></div>
    <?php endif; ?>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
