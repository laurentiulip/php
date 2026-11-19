<?php
$titluPagina = 'Despre';
$paginaActiva = 'despre';
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Despre aplicație</p><h1>Sistem de gestionare a task-urilor</h1><p>Laboratorul 4 · Integrarea bazei de date MySQL în PHP</p></div>
<section class="content-panel prose">
    <h2>Cum funcționează</h2>
    <p>Task-urile din catalog reprezintă tipuri de activități. Cantitatea disponibilă se exprimă în unități de lucru, de exemplu secțiuni ale unei pagini sau runde de testare. Un cont conectat poate cere mai multe tipuri de activități într-o singură solicitare.</p>
    <p>Administratorul gestionează categoriile și task-urile, apoi decide dacă acceptă o solicitare. Cantitatea disponibilă se reduce numai la aprobare. Solicitările decise și liniile lor rămân în istoric.</p>
    <h2>Autorul proiectului</h2>
    <p><?= e(AUTOR) ?> · Grupa <?= e(GRUPA) ?></p>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
