<?php
require_once dirname(__DIR__) . '/config.php';
requireAdmin();
$titluPagina = 'Categorii';
$paginaActiva = 'categorii';
$id = isset($_GET['edit']) ? idFrom($_GET['edit']) : null;
$category = $id ? query('SELECT name, description FROM categories WHERE id=?', [$id])->fetch() : null;
if ($id && !$category) fail(404, 'Categorie negăsită', 'Categoria selectată nu există.');
$name = $category['name'] ?? '';
$description = $category['description'] ?? '';
$errors = [];
if (isPost()) {
    protectPost();
    $name = field($_POST, 'name');
    $description = field($_POST, 'description');
    if ($name === '' || lengthOf($name) > 100) $errors['name'] = 'Introdu o denumire de 1–100 de caractere.';
    if ((isset($_POST['description']) && !is_string($_POST['description'])) || lengthOf($description) > 500) $errors['description'] = 'Introdu cel mult 500 de caractere.';
    if (!$errors) {
        try {
            if ($id) {
                query('UPDATE categories SET name=?, description=? WHERE id=?', [$name, $description, $id]);
            } else {
                query('INSERT INTO categories (name, description) VALUES (?, ?)', [$name, $description]);
            }
            flash($id ? 'Modificări salvate' : 'Înregistrare adăugată');
            redirect('pagini/categorii.php');
        } catch (PDOException $error) {
            if ((int) ($error->errorInfo[1] ?? 0) !== 1062) throw $error;
            logDatabase($error);
            $errors['name'] = 'Există deja o categorie cu această denumire.';
        }
    }
    formInvalid($errors);
}
$categories = query('SELECT c.id, c.name, c.description, COUNT(i.id) AS item_count
    FROM categories c LEFT JOIN items i ON i.category_id=c.id GROUP BY c.id, c.name, c.description ORDER BY c.name')->fetchAll();
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Administrare</p><h1>Categorii</h1><p>Organizează catalogul și vezi numărul total de task-uri din fiecare categorie.</p></div>
<section id="editor" class="content-panel demo-form-panel"><h2><?= $id ? 'Modifică categoria' : 'Adaugă o categorie' ?></h2>
<form method="post" class="field-form" novalidate>
    <?php csrfField(); ?>
    <div class="field"><label for="name">Denumire *</label><input id="name" name="name" maxlength="100" value="<?= e($name) ?>" required><?php formError($errors, 'name'); ?></div>
    <div class="field"><label for="description">Descriere</label><textarea id="description" name="description" maxlength="500" rows="2"><?= e($description) ?></textarea><?php formError($errors, 'description'); ?></div>
    <div class="form-actions"><button class="button primary" type="submit"><?= $id ? 'Salvează modificările' : 'Adaugă categoria' ?></button><?php if ($id): ?><a class="button secondary" href="<?= e(url('pagini/categorii.php')) ?>">Anulează</a><?php endif; ?></div>
</form>
</section>
<section class="content-panel"><h2>Categorii existente</h2>
    <div class="table-wrap"><table><thead><tr><th scope="col">Categorie</th><th scope="col">Elemente</th><th scope="col">Acțiune</th></tr></thead>
    <tbody><?php foreach ($categories as $c): ?><tr data-category-id="<?= e($c['id']) ?>"><th scope="row"><strong><?= e($c['name']) ?></strong><small class="quiet cell-note"><?= e($c['description']) ?></small></th><td><?= e($c['item_count']) ?></td><td><a class="table-link" href="<?= e(url('pagini/categorii.php', ['edit' => $c['id']])) ?>#editor">Modifică</a></td></tr><?php endforeach; ?></tbody></table></div>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
