<?php
$titluPagina = 'Rol demonstrativ';
$paginaActiva = 'conectare';
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro">
    <p class="eyebrow">Roluri și drepturi</p>
    <h1>Alege un rol demonstrativ</h1>
    <p>Explorează ce poate vedea și face fiecare utilizator. Alegerea unui rol nu reprezintă autentificare.</p>
</div>
<section class="content-panel demo-form-panel">
    <h2>Perspectiva utilizatorului</h2>
    <form class="field-form" method="get" action="<?= e(url('pagini/taskuri.php', [], false)) ?>">
        <div class="field">
            <label for="rol">Rol</label>
            <select name="rol" id="rol">
                <?php foreach (ROLURI as $cod => $denumire): ?>
                    <option value="<?= e($cod) ?>" <?= $cod === $rol ? 'selected' : '' ?>><?= e($denumire) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="button primary" type="submit">Aplică rolul</button>
    </form>
</section>
<section class="content-panel">
    <h2>Drepturi pe roluri</h2>
    <?php require dirname(__DIR__) . '/componente/matrice-drepturi.php'; ?>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
