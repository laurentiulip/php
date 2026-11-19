<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/lib/solicitari.php';
$account = requireUser();
$titluPagina = 'Solicitare nouă';
$paginaActiva = 'solicitare-noua';
$items = query('SELECT i.id, i.name, i.available_quantity, c.name AS category FROM items i JOIN categories c ON c.id=i.category_id WHERE i.active=1 ORDER BY c.name, i.name')->fetchAll();
$byId = array_column($items, null, 'id');
$errors = [];
$note = '';
$values = [];
if (isPost()) {
    protectPost();
    $note = field($_POST, 'nota');
    if ((isset($_POST['nota']) && !is_string($_POST['nota'])) || lengthOf($note) > 500) $errors['nota'] = 'Introdu cel mult 500 de caractere.';
    $input = $_POST['cantitati'] ?? [];
    $selected = [];
    if (!is_array($input)) {
        $errors['cantitati'] = 'Alege cel puțin un task din catalog.';
    } else {
        foreach ($input as $id => $raw) {
            $value = is_string($raw) ? trim($raw) : null;
            if ($value === '' || $value === '0') continue;
            if (!isset($byId[$id])) {
                $errors['cantitati'] = 'Un task selectat nu mai este disponibil.';
                continue;
            }
            $values[$id] = $value ?? '';
            $max = (int) $byId[$id]['available_quantity'];
            $qty = wholeNumber($value, 1, $max);
            if ($qty === null) {
                $errors['qty-' . $id] = $max === 0 ? 'Acest task nu are unități disponibile.' : 'Introdu un număr întreg între 1 și ' . $max . '.';
            } else {
                $selected[$id] = $qty;
            }
        }
    }
    if (!$selected && !$errors) $errors['cantitati'] = 'Introdu cantitatea pentru cel puțin un task.';
    if (!$errors) {
        try {
            $id = createRequest((int) $account['id'], $selected, $note);
            flash('Înregistrare adăugată. Solicitarea este în așteptare.');
            redirect('pagini/solicitare.php', ['id' => $id]);
        } catch (DomainException $error) {
            $errors['cantitati'] = $error->getMessage();
        }
    }
    formInvalid($errors);
}
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Solicitările mele</p><h1>Solicitare nouă</h1><p>Introdu cantități la task-urile de care ai nevoie. Lasă celelalte câmpuri goale.</p></div>
<section class="content-panel">
<form method="post" class="field-form" novalidate>
    <?php csrfField(); ?>
    <div class="field"><label for="nota">Notă (opțional)</label><textarea id="nota" name="nota" maxlength="500" rows="2"><?= e($note) ?></textarea><?php formError($errors, 'nota'); ?></div>
    <fieldset class="request-items"><legend>Task-uri și cantități</legend>
        <?php formError($errors, 'cantitati'); ?>
        <?php if (!$items): ?><p class="notice">Nu sunt task-uri active.</p><?php endif; ?>
        <?php foreach ($items as $item): $id = $item['id']; ?>
        <div class="quantity-row">
            <div><label for="qty-<?= e($id) ?>"><?= e($item['name']) ?></label><small class="quiet cell-note"><?= e($item['category']) ?> · <?= e($item['available_quantity']) ?> unități disponibile</small></div>
            <div class="field"><input type="number" id="qty-<?= e($id) ?>" name="cantitati[<?= e($id) ?>]" min="0" max="<?= e($item['available_quantity']) ?>" step="1" inputmode="numeric" value="<?= e($values[$id] ?? '') ?>" <?= (int) $item['available_quantity'] === 0 && !isset($errors['qty-' . $id]) ? 'disabled' : '' ?>><?php formError($errors, 'qty-' . $id); ?></div>
        </div>
        <?php endforeach; ?>
    </fieldset>
    <button class="button primary" type="submit">Trimite solicitarea</button>
</form>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
