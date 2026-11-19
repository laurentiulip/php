<?php
// Sarcina 2: datele proiectului individual.
$denumireProiect = 'Sistem de gestionare a task-urilor';
$autor = 'Lipcanu Laurentiu';
$grupa = 'AAW-231';
$descriere = 'Aplicație pentru organizarea task-urilor pe proiecte și urmărirea progresului acestora.';
$utilizatoriPrincipali = ['Administrator', 'Coordonator de proiect', 'Membru al echipei'];
$entitatiPlanificate = ['Utilizator', 'Proiect', 'Task'];
const VERSIUNE_APLICATIE = '1.0';
// Sarcina 4: numărul de ore alocate zilnic unui task, folosit pentru estimare.
const ORE_ALOCATE_PE_ZI = 4;

// Sarcinile 3 și 7: trei seturi de date demonstrative, selectate prin URL.
$scenariu = $_GET['scenariu'] ?? '2';
if (!is_string($scenariu) || !in_array($scenariu, ['1', '2', '3'], true)) {
    http_response_code(400);
    exit('Scenariu invalid. Folosește 1, 2 sau 3.');
}
$idTask = 101;                                    // int
$titluTask = 'Realizarea paginii de prezentare';    // string
$numeProiect = 'Organizarea activităților echipei';
$responsabil = $autor;
$prioritate = 'Medie';
$numarSubtaskuri = 8;
$subtaskuriFinalizate = 3;
$oreEstimate = 12.0;                               // float
if ($scenariu === '1') {
    $idTask = 102;
    $titluTask = 'Planificarea paginii de prezentare';
    $subtaskuriFinalizate = 0;
} elseif ($scenariu === '3') {
    $idTask = 103;
    $titluTask = 'Pregătirea structurii proiectului';
    $numarSubtaskuri = 6;
    $subtaskuriFinalizate = 6;
    $oreEstimate = 9.0;
}

// Validăm datele înaintea calculelor, inclusiv după modificarea lor manuală.
if ($numarSubtaskuri <= 0 || $subtaskuriFinalizate < 0 ||
    $subtaskuriFinalizate > $numarSubtaskuri || $oreEstimate < 0) {
    http_response_code(400);
    exit('Date invalide: totalul trebuie să fie pozitiv, iar valorile în intervalele permise.');
}
$esteFinalizat = ($subtaskuriFinalizate === $numarSubtaskuri); // bool
$stare = $esteFinalizat ? 'Finalizat' : ($subtaskuriFinalizate === 0 ? 'Neînceput' : 'În lucru');

// Sarcina 5: subtask-urile au ponderi egale. Orele sunt estimări, nu timp măsurat.
$subtaskuriRamase = $numarSubtaskuri - $subtaskuriFinalizate;
$progres = $subtaskuriFinalizate / $numarSubtaskuri * 100;
$oreRealizateEstimate = $oreEstimate * $progres / 100;
$oreRamase = $oreEstimate - $oreRealizateEstimate;
$zileNecesare = (int) ceil($oreRamase / ORE_ALOCATE_PE_ZI);

// Sarcina 6: funcții mici pentru afișarea sigură și lizibilă în HTML.
function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
function numar(float $valoare): string {
    return number_format($valoare, 2, ',', ' ');
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($denumireProiect) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <header>
        <p class="eyebrow">Lucrarea de laborator nr. 1</p>
        <h1><?= e($denumireProiect) ?></h1>
        <p><?= e($descriere) ?></p>
        <p class="meta">Autor: <?= e($autor) ?> · Grupa: <?= e($grupa) ?></p>
    </header>
    <section aria-labelledby="proiect">
        <h2 id="proiect">Prezentarea proiectului</h2>
        <p><strong>Utilizatori principali:</strong> <?= e(implode(', ', $utilizatoriPrincipali)) ?>.</p>
        <p><strong>Entități planificate:</strong> <?= e(implode(', ', $entitatiPlanificate)) ?>.</p>
        <p>În laboratoarele următoare: introducerea și salvarea datelor, operații CRUD și autentificare.</p>
    </section>
    <nav aria-label="Scenarii de testare">
        <?php foreach (['1' => '1. Neînceput', '2' => '2. În lucru', '3' => '3. Finalizat'] as $cheie => $eticheta): ?>
            <a href="?scenariu=<?= e((string) $cheie) ?>" <?= (string) $cheie === $scenariu ? 'aria-current="page"' : '' ?>><?= e($eticheta) ?></a>
        <?php endforeach; ?>
    </nav>
    <section aria-labelledby="modul">
        <p class="eyebrow">Modulul Task-uri</p>
        <h2 id="modul"><?= e($titluTask) ?></h2>
        <dl>
            <div><dt>ID task</dt><dd><?= $idTask ?></dd></div>
            <div><dt>Proiect</dt><dd><?= e($numeProiect) ?></dd></div>
            <div><dt>Responsabil</dt><dd><?= e($responsabil) ?></dd></div>
            <div><dt>Prioritate</dt><dd><?= e($prioritate) ?></dd></div>
            <div><dt>Stare</dt><dd><?= e($stare) ?></dd></div>
            <div><dt>Este finalizat</dt><dd><?= $esteFinalizat ? 'Da' : 'Nu' ?></dd></div>
            <div><dt>Subtask-uri finalizate</dt><dd><?= $subtaskuriFinalizate ?> din <?= $numarSubtaskuri ?></dd></div>
            <div><dt>Durată estimată</dt><dd><?= numar($oreEstimate) ?> ore</dd></div>
        </dl>
    </section>
    <section aria-labelledby="calcule">
        <h2 id="calcule">Rezultatele calculelor</h2>
        <p class="progress-label">Progres: <strong id="progres"><?= numar($progres) ?>%</strong></p>
        <progress max="100" value="<?= $progres ?>" aria-label="Progresul task-ului"><?= numar($progres) ?>%</progress>
        <div class="table-wrap"><table>
            <thead><tr><th>Indicator</th><th>Rezultat</th></tr></thead>
            <tbody>
                <tr><td>Subtask-uri rămase</td><td id="subtaskuri-ramase"><?= $subtaskuriRamase ?></td></tr>
                <tr><td>Ore realizate estimate</td><td><?= numar($oreRealizateEstimate) ?> ore</td></tr>
                <tr><td>Ore rămase estimate</td><td id="ore-ramase"><?= numar($oreRamase) ?> ore</td></tr>
                <tr><td>Zile de lucru necesare</td><td id="zile-necesare"><?= $zileNecesare ?></td></tr>
            </tbody>
        </table></div>
        <p class="meta">Estimare cu subtask-uri de aceeași pondere și <?= ORE_ALOCATE_PE_ZI ?> ore alocate pe zi. Zilele se rotunjesc în sus.</p>
    </section>
    <footer>Autor: <?= e($autor) ?> · Versiunea aplicației: <?= VERSIUNE_APLICATIE ?></footer>
</main>
</body>
</html>
