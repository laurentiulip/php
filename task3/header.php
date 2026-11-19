<?php
// Header reutilizabil. Fiecare pagină furnizează titlul și opțiunea activă.
require_once __DIR__ . '/config.php';
$titluPagina = $titluPagina ?? 'Acasă';
$paginaActiva = $paginaActiva ?? 'acasa';
$meniu = [
    ['acasa', 'index.php', 'Acasă', 'Privire de ansamblu'],
    ['taskuri', 'pagini/taskuri.php', 'Task-uri', 'Căutare și filtrare'],
    ['estimari', 'pagini/estimari.php', 'Estimări', 'Progres și timp rămas'],
    ['proiecte', 'pagini/proiecte.php', 'Proiecte', 'Organizarea activităților'],
    ['despre', 'pagini/despre.php', 'Despre', 'Scopul aplicației'],
    ['conectare', 'pagini/conectare.php', 'Rol demonstrativ', 'Schimbă perspectiva'],
];
if (areDrept('task_nou')) {
    $meniu[] = ['task-nou', 'pagini/task-nou.php', 'Task nou', 'Formular de propunere'];
}
if (areDrept('administrare')) {
    $meniu[] = ['administrare', 'pagini/administrare.php', 'Administrare', 'Drepturile rolurilor'];
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Organizarea task-urilor și urmărirea progresului pe proiecte.">
    <title><?= e($titluPagina) ?> — <?= e(DENUMIRE_PROIECT) ?></title>
    <link rel="stylesheet" href="<?= e(url('style.css', ['v' => VERSIUNE_APLICATIE])) ?>">
    <script src="<?= e(url('script.js', ['v' => VERSIUNE_APLICATIE])) ?>" defer></script>
</head>
<body>
<a class="skip-link" href="#continut">Salt la conținut</a>
<dialog class="drawer" id="meniu-lateral" aria-labelledby="titlu-meniu">
    <div class="drawer-top"><div><span class="eyebrow">Navigare</span><h2 id="titlu-meniu">Paginile site-ului</h2></div><button class="icon-button" id="inchide-meniu" type="button" aria-label="Închide meniul"><span aria-hidden="true">×</span></button></div>
    <nav aria-label="Navigare principală">
    <?php foreach ($meniu as [$id, $cale, $nume, $explicatie]): ?>
        <a class="drawer-link" href="<?= e(url($cale)) ?>" <?= $paginaActiva === $id ? 'aria-current="page"' : '' ?>><span><?= e($nume) ?></span><small><?= e($explicatie) ?></small></a>
    <?php endforeach; ?>
    </nav>
    <p class="drawer-note">Task-uri, proiecte și progres.<br>Versiunea <?= VERSIUNE_APLICATIE ?></p>
</dialog>
<div class="page-shell">
<header class="site-header">
    <div class="header-inner">
        <button class="icon-button menu-toggle" type="button" id="deschide-meniu" aria-label="Deschide meniul" aria-controls="meniu-lateral" aria-expanded="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <a href="<?= e(url()) ?>" class="brand"><span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 12 4 4 8-9"/></svg></span><span><?= e(DENUMIRE_PROIECT) ?></span></a>
        <a href="<?= e(url('pagini/conectare.php')) ?>" class="role-switch" aria-label="Schimbă rolul demonstrativ" title="Schimbă rolul demonstrativ"><span><?= e(ROLURI[$rol]) ?></span><span aria-hidden="true">⌄</span></a>
    </div>
</header>
<main id="continut" class="main-content">
