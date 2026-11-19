<?php
require_once dirname(__DIR__) . '/config.php';
cereDrept('administrare');
$titluPagina = 'Administrare';
$paginaActiva = 'administrare';
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro">
    <p class="eyebrow">Administrator</p>
    <h1>Administrarea accesului</h1>
    <p>Consultă matricea drepturilor folosite în demonstrație.</p>
</div>
<section class="content-panel">
    <h2>Permisiuni pentru pagini și acțiuni</h2>
    <p>Toate rolurile pot vedea Acasă, Proiecte, Estimări, Despre și pagina de alegere a rolului.</p>
    <?php require dirname(__DIR__) . '/componente/matrice-drepturi.php'; ?>
    <a class="text-link" href="<?= e(url('pagini/task-nou.php')) ?>">Deschide formularul de task</a>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
