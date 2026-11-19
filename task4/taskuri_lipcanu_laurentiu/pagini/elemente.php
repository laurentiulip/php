<?php
require_once dirname(__DIR__) . '/config.php';
requireAdmin();
$titluPagina = 'Elemente';
$paginaActiva = 'elemente';
$id = isset($_GET['edit']) ? idFrom($_GET['edit']) : null;
$item = $id ? query('SELECT name, description, category_id, available_quantity FROM items WHERE id=?', [$id])->fetch() : null;
if ($id && !$item) fail(404, 'Element negăsit', 'Task-ul selectat nu există.');
$categories = query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$categoryIds = array_map('intval', array_column($categories, 'id'));
$values = ['name' => '', 'description' => '', 'category_id' => '', 'available_quantity' => '0'];
if ($item) $values = array_map('strval', $item);
$errors = [];

if (isPost()) {
    protectPost();
    $action = field($_POST, 'action');
    if (in_array($action, ['activate', 'deactivate', 'delete'], true)) {
        $target = idFrom($_POST['id'] ?? null);
        if ($action === 'delete') {
            try {
                $deleted = query('DELETE FROM items WHERE id=?', [$target])->rowCount();
                flash($deleted ? 'Înregistrare ștearsă' : 'Task-ul nu mai există.', $deleted ? 'success' : 'info');
            } catch (PDOException $error) {
                if ((int) ($error->errorInfo[1] ?? 0) !== 1451) throw $error;
                logDatabase($error);
                flash('Task-ul apare în solicitări. Îl poți dezactiva pentru a păstra istoricul.', 'error');
            }
        } else {
            query('UPDATE items SET active=? WHERE id=?', [$action === 'activate' ? 1 : 0, $target]);
            flash('Modificări salvate');
        }
        redirect('pagini/elemente.php');
    }
    if ($action !== 'save') fail(422, 'Acțiune nevalidă', 'Alege o acțiune din pagina Elemente.');
    foreach ($values as $key => $old) $values[$key] = field($_POST, $key);
    $category = wholeNumber($values['category_id'], 1, 4294967295);
    $quantity = wholeNumber($values['available_quantity'], 0);
    if ($values['name'] === '' || lengthOf($values['name']) > 120) $errors['name'] = 'Introdu o denumire de 1–120 de caractere.';
    if ((isset($_POST['description']) && !is_string($_POST['description'])) || lengthOf($values['description']) > 500) $errors['description'] = 'Introdu cel mult 500 de caractere.';
    if (!in_array($category, $categoryIds, true)) $errors['category_id'] = 'Alege o categorie din listă.';
    if ($quantity === null) $errors['available_quantity'] = 'Introdu o cantitate întreagă de la 0 la 2 147 483 647.';
    if (!$errors) {
        $params = [$values['name'], $values['description'], $category, $quantity];
        if ($id) {
            query('UPDATE items SET name=?, description=?, category_id=?, available_quantity=? WHERE id=?', [...$params, $id]);
        } else {
            query('INSERT INTO items (name, description, category_id, available_quantity) VALUES (?, ?, ?, ?)', $params);
        }
        flash($id ? 'Modificări salvate' : 'Înregistrare adăugată');
        redirect('pagini/elemente.php');
    }
    formInvalid($errors);
}
$items = query('SELECT i.id, i.name, i.available_quantity, i.active, c.name AS category,
    EXISTS (SELECT 1 FROM request_items ri WHERE ri.item_id=i.id) AS referenced
    FROM items i JOIN categories c ON c.id=i.category_id ORDER BY c.name, i.name')->fetchAll();
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Administrare</p><h1>Elemente din catalog</h1><p>Gestionează tipurile de task-uri și unitățile disponibile.</p></div>
<section id="editor" class="content-panel demo-form-panel">
    <h2><?= $id ? 'Modifică task-ul' : 'Adaugă un task' ?></h2>
    <form method="post" class="field-form task-form" novalidate>
        <?php csrfField(); ?><input type="hidden" name="action" value="save">
        <div class="field wide"><label for="name">Denumire *</label><input id="name" name="name" maxlength="120" value="<?= e($values['name']) ?>" required><?php formError($errors, 'name'); ?></div>
        <div class="field"><label for="category_id">Categorie *</label><select id="category_id" name="category_id" required><option value="">Alege o categorie</option><?php foreach ($categories as $c): ?><option value="<?= e($c['id']) ?>" <?= (string) $c['id'] === $values['category_id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select><?php formError($errors, 'category_id'); ?></div>
        <div class="field"><label for="available_quantity">Unități disponibile *</label><input id="available_quantity" name="available_quantity" type="number" min="0" step="1" value="<?= e($values['available_quantity']) ?>" required><?php formError($errors, 'available_quantity'); ?></div>
        <div class="field wide"><label for="description">Descriere</label><textarea id="description" name="description" maxlength="500" rows="2"><?= e($values['description']) ?></textarea><?php formError($errors, 'description'); ?></div>
        <div class="form-actions"><button class="button primary" type="submit"><?= $id ? 'Salvează modificările' : 'Adaugă task-ul' ?></button><?php if ($id): ?><a class="button secondary" href="<?= e(url('pagini/elemente.php')) ?>">Anulează</a><?php endif; ?></div>
    </form>
</section>
<section class="content-panel"><h2>Catalog administrativ</h2>
    <div class="table-wrap task-table"><table><thead><tr><th scope="col">Task</th><th scope="col">Categorie</th><th scope="col">Unități</th><th scope="col">Statut</th><th scope="col">Acțiuni</th></tr></thead>
    <tbody><?php foreach ($items as $i): ?><tr data-item-id="<?= e($i['id']) ?>">
        <th scope="row"><?= e($i['name']) ?></th><td><?= e($i['category']) ?></td><td><?= e($i['available_quantity']) ?></td>
        <td><span class="status <?= $i['active'] ? 'green' : 'neutral' ?>"><?= $i['active'] ? 'Activ' : 'Inactiv' ?></span></td>
        <td><div class="row-actions">
            <a class="table-link" href="<?= e(url('pagini/elemente.php', ['edit' => $i['id']])) ?>#editor">Modifică</a>
            <form method="post" action="<?= e(url('pagini/elemente.php')) ?>"><?php csrfField(); ?><input type="hidden" name="id" value="<?= e($i['id']) ?>"><button type="submit" class="link-button" name="action" value="<?= $i['active'] ? 'deactivate' : 'activate' ?>"><?= $i['active'] ? 'Dezactivează' : 'Activează' ?></button></form>
            <?php if (!$i['referenced']): ?>
            <form method="post" action="<?= e(url('pagini/elemente.php')) ?>" data-confirm="Ștergi acest task din catalog?"><?php csrfField(); ?><input type="hidden" name="id" value="<?= e($i['id']) ?>"><button type="submit" class="link-button danger" name="action" value="delete">Șterge</button></form>
            <?php else: ?><span class="quiet">În istoric</span><?php endif; ?>
        </div></td>
    </tr><?php endforeach; ?></tbody></table></div>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
