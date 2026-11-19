<?php
require_once dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/date/taskuri.php';
$titluPagina = 'Proiecte';
$paginaActiva = 'proiecte';
$vizibile = taskuriVizibile($taskuri);
$descrieri = [
    'site' => 'Planificarea conținutului, realizarea paginii de prezentare și organizarea fișierelor PHP.',
    'testare' => 'Verificarea navigării pe telefon și a prelucrării datelor din formular.',
];
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro">
    <p class="eyebrow">Modulul Proiecte</p>
    <h1>Activități organizate pe proiecte</h1>
    <p>Consultă exemplele și filtrează lista de task-uri după proiect.</p>
</div>
<?php foreach ($proiecte as $cod => $nume): ?>
    <section class="content-panel">
        <h2><?= e($nume) ?></h2>
        <p><?= e($descrieri[$cod]) ?></p>
        <p class="quiet">Task-uri vizibile: <?= count(array_filter($vizibile, static fn(array $task): bool => $task['proiect'] === $cod)) ?></p>
        <a class="text-link" href="<?= e(url('pagini/taskuri.php', ['proiect' => $cod])) ?>">Vezi task-urile proiectului</a>
    </section>
<?php endforeach; ?>
<?php require dirname(__DIR__) . '/footer.php'; ?>
