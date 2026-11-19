<?php
require_once dirname(__DIR__) . '/config.php';
$titluPagina = 'Task-uri';
$paginaActiva = 'estimari';
// Calculele din laboratorul 1 sunt păstrate în pagina dedicată task-urilor.
$scenariu = $_GET['scenariu'] ?? '2';
$eroare = !is_string($scenariu) || !in_array($scenariu, ['1', '2', '3'], true);
if ($eroare) { http_response_code(400); }
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Modulul Task-uri</p><h1>Progres și estimări</h1><p>Urmărește etapele unui task și vezi timpul estimat pentru finalizare.</p></div>
<?php if ($eroare): ?>
<section class="content-panel"><h2>Scenariu invalid</h2><p>Alege unul dintre cele trei scenarii disponibile.</p><a class="button primary" href="<?= e(url('pagini/taskuri.php')) ?>">Înapoi la task-uri</a></section>
<?php else:
$idTask = 101; $titluTask = 'Realizarea paginii de prezentare';
$numarSubtaskuri = 8; $subtaskuriFinalizate = 3; $oreEstimate = 12.0;
if ($scenariu === '1') { $idTask = 102; $titluTask = 'Planificarea paginii de prezentare'; $subtaskuriFinalizate = 0; }
elseif ($scenariu === '3') { $idTask = 103; $titluTask = 'Pregătirea structurii proiectului'; $numarSubtaskuri = 6; $subtaskuriFinalizate = 6; $oreEstimate = 9.0; }
$esteFinalizat = $subtaskuriFinalizate === $numarSubtaskuri;
$stare = $esteFinalizat ? 'Finalizat' : ($subtaskuriFinalizate === 0 ? 'Neînceput' : 'În lucru');
$subtaskuriRamase = $numarSubtaskuri - $subtaskuriFinalizate;
$progres = $subtaskuriFinalizate / $numarSubtaskuri * 100;
$oreRealizateEstimate = $oreEstimate * $progres / 100;
$oreRamase = $oreEstimate - $oreRealizateEstimate;
$zileNecesare = (int) ceil($oreRamase / ORE_ALOCATE_PE_ZI);
?>
<nav class="scenario-nav" aria-label="Scenarii de testare"><?php foreach (['1' => 'Neînceput', '2' => 'În lucru', '3' => 'Finalizat'] as $id => $eticheta): ?><a href="<?= e(url('pagini/estimari.php', ['scenariu' => $id])) ?>" <?= (string) $id === $scenariu ? 'aria-current="page"' : '' ?>><?= e($eticheta) ?></a><?php endforeach; ?></nav>
<section class="content-panel"><div class="section-heading"><h2><?= e($titluTask) ?></h2><span class="demo-label">Scenariul <?= e($scenariu) ?></span></div><dl class="entity-details"><div><dt>ID task</dt><dd><?= $idTask ?></dd></div><div><dt>Proiect</dt><dd>Organizarea activităților echipei</dd></div><div><dt>Responsabil</dt><dd><?= e(AUTOR) ?></dd></div><div><dt>Prioritate</dt><dd>Medie</dd></div><div><dt>Stare</dt><dd><?= e($stare) ?></dd></div><div><dt>Este finalizat</dt><dd><?= $esteFinalizat ? 'Da' : 'Nu' ?></dd></div><div><dt>Subtask-uri finalizate</dt><dd><?= $subtaskuriFinalizate ?> din <?= $numarSubtaskuri ?></dd></div><div><dt>Durată estimată</dt><dd><?= numar($oreEstimate) ?> ore</dd></div></dl><div class="progress-row"><span>Progres</span><strong id="progres"><?= numar($progres) ?>%</strong></div><progress value="<?= $progres ?>" max="100" aria-label="Progresul task-ului"><?= numar($progres) ?>%</progress><div class="table-wrap"><table><thead><tr><th>Indicator</th><th>Rezultat</th></tr></thead><tbody><tr><td>Subtask-uri rămase</td><td id="subtaskuri-ramase"><?= $subtaskuriRamase ?></td></tr><tr><td>Ore realizate estimate</td><td><?= numar($oreRealizateEstimate) ?> ore</td></tr><tr><td>Ore rămase estimate</td><td id="ore-ramase"><?= numar($oreRamase) ?> ore</td></tr><tr><td>Zile de lucru necesare</td><td id="zile-necesare"><?= $zileNecesare ?></td></tr></tbody></table></div><p class="quiet">Subtask-uri cu ponderi egale, <?= ORE_ALOCATE_PE_ZI ?> ore alocate pe zi. Orele sunt estimări, iar zilele se rotunjesc în sus.</p></section>
<?php endif; require dirname(__DIR__) . '/footer.php'; ?>
