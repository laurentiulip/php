<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/lib/rapoarte.php';
requireAdmin();
$titluPagina = 'Rapoarte';
$paginaActiva = 'rapoarte';
$dates = query('SELECT CURRENT_DATE AS today, CURRENT_DATE - INTERVAL 29 DAY AS start_date')->fetch();
$from = isset($_GET['de_la']) ? field($_GET, 'de_la') : $dates['start_date'];
$to = isset($_GET['pana_la']) ? field($_GET, 'pana_la') : $dates['today'];
$errors = [];
if (!validDate($from)) $errors['de_la'] = 'Introdu data de început.';
if (!validDate($to)) $errors['pana_la'] = 'Introdu data de sfârșit.';
if (!$errors && $from > $to) $errors['pana_la'] = 'Data de sfârșit trebuie să fie egală cu sau ulterioară datei de început.';
formInvalid($errors);
$interval = $errors ? [] : requestsBetween($from, $to);
if (!$errors && ($_GET['export'] ?? '') === 'csv') exportRequests($interval, $from, $to);
$statusCounts = array_fill_keys(array_keys(STARI), 0);
foreach (query('SELECT status, COUNT(*) AS total FROM requests GROUP BY status')->fetchAll() as $row) {
    $statusCounts[$row['status']] = (int) $row['total'];
}
$top = query("SELECT i.name, SUM(ri.quantity) AS total FROM request_items ri
    JOIN requests r ON r.id=ri.request_id JOIN items i ON i.id=ri.item_id
    WHERE r.status='approved' GROUP BY i.id, i.name ORDER BY total DESC, i.name, i.id LIMIT 5")->fetchAll();
$activity = query('SELECT u.name, u.email, COUNT(r.id) AS total FROM users u
    LEFT JOIN requests r ON r.user_id=u.id GROUP BY u.id, u.name, u.email ORDER BY total DESC, u.name')->fetchAll();
$unavailable = query('SELECT c.name AS category, i.name, i.available_quantity, i.active FROM items i
    JOIN categories c ON c.id=i.category_id WHERE i.available_quantity=0 OR i.active=0 ORDER BY c.name, i.name')->fetchAll();
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Administrare</p><h1>Rapoarte</h1><p>Urmărește solicitările și disponibilitatea task-urilor.</p></div>
<div class="report-grid">
    <section class="content-panel">
        <h2>Solicitări pe statut</h2>
        <table><thead><tr><th scope="col">Statut</th><th scope="col">Solicitări</th></tr></thead><tbody>
        <?php foreach ($statusCounts as $status => $count): ?><tr><th scope="row"><?php statusLabel($status); ?></th><td><?= e($count) ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </section>
    <section class="content-panel">
        <h2>Cele mai solicitate 5 task-uri</h2><p class="quiet">Cantități din solicitările aprobate.</p>
        <?php if (!$top): ?><p class="notice">Nu există solicitări aprobate.</p><?php else: ?>
        <table><thead><tr><th scope="col">Task</th><th scope="col">Total</th></tr></thead><tbody>
        <?php foreach ($top as $row): ?><tr><th scope="row"><?= e($row['name']) ?></th><td><?= e($row['total']) ?></td></tr><?php endforeach; ?>
        </tbody></table><?php endif; ?>
    </section>
    <section class="content-panel">
        <h2>Activitatea utilizatorilor</h2>
        <table><thead><tr><th scope="col">Utilizator</th><th scope="col">Solicitări</th></tr></thead><tbody>
        <?php foreach ($activity as $row): ?><tr><th scope="row"><?= e($row['name']) ?><small class="quiet cell-note"><?= e($row['email']) ?></small></th><td><?= e($row['total']) ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </section>
    <section class="content-panel">
        <h2>Elemente indisponibile</h2><p class="quiet">Task-uri inactive sau fără unități disponibile.</p>
        <?php if (!$unavailable): ?><p class="notice">Toate task-urile sunt disponibile.</p><?php else: ?>
        <div class="table-wrap"><table><thead><tr><th scope="col">Categorie</th><th scope="col">Task</th><th scope="col">Unități</th></tr></thead><tbody>
        <?php foreach ($unavailable as $row): ?><tr><td><?= e($row['category']) ?></td><th scope="row"><?= e($row['name']) ?><?= $row['active'] ? '' : ' (inactiv)' ?></th><td><?= e($row['available_quantity']) ?></td></tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
    </section>
    <section class="content-panel full" id="interval">
        <div class="section-heading"><h2>Solicitări într-un interval</h2><?php if (!$errors): ?><a class="button secondary" href="<?= e(url('pagini/rapoarte.php', ['de_la' => $from, 'pana_la' => $to, 'export' => 'csv'])) ?>">Exportă CSV</a><?php endif; ?></div>
        <form method="get" action="<?= e(url('pagini/rapoarte.php')) ?>#interval" class="field-form search-form" novalidate>
            <div class="field"><label for="de_la">De la</label><input name="de_la" id="de_la" type="date" value="<?= e($from) ?>" required><?php formError($errors, 'de_la'); ?></div>
            <div class="field"><label for="pana_la">Până la</label><input name="pana_la" id="pana_la" type="date" value="<?= e($to) ?>" required><?php formError($errors, 'pana_la'); ?></div>
            <div class="form-actions"><button class="button primary" type="submit">Filtrează</button></div>
        </form>
        <?php if (!$errors): ?>
        <p class="quiet interval-count">Solicitări găsite: <?= count($interval) ?></p>
        <?php if (!$interval): ?><p class="notice">Nu există solicitări în acest interval.</p><?php else: ?>
        <div class="table-wrap"><table><thead><tr><th scope="col">Număr</th><th scope="col">Creată la</th><th scope="col">Utilizator</th><th scope="col">Statut</th><th scope="col">Task-uri</th><th scope="col">Unități</th></tr></thead><tbody>
        <?php foreach ($interval as $r): ?><tr><th scope="row"><a class="table-link" href="<?= e(url('pagini/solicitare.php', ['id' => $r['id']])) ?>">#<?= e($r['id']) ?></a></th><td><?= e($r['created_at']) ?></td><td><?= e($r['author']) ?></td><td><?php statusLabel($r['status']); ?></td><td><?= e($r['line_count']) ?></td><td><?= e($r['units']) ?></td></tr><?php endforeach; ?>
        </tbody></table></div><?php endif; endif; ?>
    </section>
</div>
<?php require dirname(__DIR__) . '/footer.php'; ?>
