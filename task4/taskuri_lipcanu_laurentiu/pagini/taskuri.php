<?php
require_once dirname(__DIR__) . '/config.php';
$titluPagina = 'Catalog de task-uri';
$paginaActiva = 'taskuri';
$cautare = field($_GET, 'q');
$selectie = field($_GET, 'categorie');
$errors = [];
$categorii = query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$categoryIds = array_map('strval', array_column($categorii, 'id'));
if (lengthOf($cautare) > 100) $errors['q'] = 'Introdu cel mult 100 de caractere.';
if ($selectie !== '' && !in_array($selectie, $categoryIds, true)) $errors['categorie'] = 'Alege o categorie din listă.';
formInvalid($errors);
$sql = 'SELECT i.id, i.name, i.description, i.available_quantity, c.name AS category FROM items i JOIN categories c ON c.id=i.category_id WHERE i.active=1';
$args = [];
if ($cautare !== '') {
    $sql .= ' AND i.name LIKE ? ESCAPE \'!\'';
    $args[] = '%' . strtr($cautare, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
}
if ($selectie !== '') { $sql .= ' AND c.id=?'; $args[] = (int) $selectie; }
$items = $errors ? [] : query($sql . ' ORDER BY c.name, i.name', $args)->fetchAll();
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Catalog</p><h1>Task-uri active</h1><p>Găsește activități după denumire sau categorie și verifică unitățile disponibile.</p></div>
<section class="content-panel">
    <div class="section-heading"><h2>Căutare în catalog</h2><?php if ($headerUser): ?><a class="button primary" href="<?= e(url('pagini/solicitare-noua.php')) ?>">Solicitare nouă</a><?php endif; ?></div>
    <form class="field-form search-form" method="get">
        <div class="field"><label for="q">Denumire</label><input id="q" name="q" type="search" maxlength="100" value="<?= e($cautare) ?>" placeholder="De exemplu: formular"><?php formError($errors, 'q'); ?></div>
        <div class="field"><label for="categorie">Categorie</label><select id="categorie" name="categorie"><option value="">Toate categoriile</option><?php foreach ($categorii as $c): ?><option value="<?= e($c['id']) ?>" <?= (string) $c['id'] === $selectie ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select><?php formError($errors, 'categorie'); ?></div>
        <div class="form-actions"><button class="button primary">Caută</button><a class="button secondary" href="<?= e(url('pagini/taskuri.php')) ?>">Resetează</a></div>
    </form>
</section>
<section class="content-panel">
    <h2>Rezultate</h2>
    <?php if ($errors): ?><p class="notice error">Verifică filtrele și încearcă din nou.</p>
    <?php elseif (!$items): ?><p class="notice">Nu există task-uri active pentru filtrele selectate.</p>
    <?php else: ?>
    <p class="quiet">Task-uri active găsite: <?= count($items) ?></p>
    <div class="table-wrap task-table"><table>
        <thead><tr><th scope="col">Task</th><th scope="col">Categorie</th><th scope="col">Unități disponibile</th></tr></thead>
        <tbody><?php foreach ($items as $i): ?><tr data-item-id="<?= e($i['id']) ?>"><th scope="row"><strong><?= e($i['name']) ?></strong><small class="quiet cell-note"><?= e($i['description']) ?></small></th><td><?= e($i['category']) ?></td><td><?= e($i['available_quantity']) ?></td></tr><?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
