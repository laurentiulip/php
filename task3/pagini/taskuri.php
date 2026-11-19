<?php
require_once dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/date/taskuri.php';

$titluPagina = 'Task-uri';
$paginaActiva = 'taskuri';
$cautare = campText($_GET, 'q');
$stare = campText($_GET, 'stare');
$proiect = campText($_GET, 'proiect');
$eroare = '';
foreach (['q', 'stare', 'proiect'] as $cheie) {
    if (isset($_GET[$cheie]) && !is_string($_GET[$cheie])) {
        $eroare = 'Parametrii de căutare trebuie să fie valori simple.';
    }
}
if (lungime($cautare) > 100) {
    $eroare = 'Căutarea poate avea cel mult 100 de caractere.';
}
if (($stare !== '' && !isset($stari[$stare])) || ($proiect !== '' && !isset($proiecte[$proiect]))) {
    $eroare = 'Alege o stare și un proiect din opțiunile afișate.';
}
if ($eroare !== '') {
    http_response_code(400);
}

$termen = textCautare($cautare);
// Se aplică drepturile înainte de filtrare, inclusiv când lista de filtre este goală.
$rezultate = array_filter(taskuriVizibile($taskuri), static fn(array $task): bool =>
    ($termen === '' || str_contains(textCautare($task['titlu']), $termen))
    && ($stare === '' || $task['stare'] === $stare)
    && ($proiect === '' || $task['proiect'] === $proiect)
);
$descrieriRol = [
    'vizitator' => 'Vezi task-urile publice.',
    'membru' => 'Vezi task-urile atribuite lui Laurentiu.',
    'coordonator' => 'Vezi toate task-urile și poți propune unul nou.',
    'administrator' => 'Vezi toate task-urile, poți propune unul nou și poți consulta administrarea.',
];
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro">
    <p class="eyebrow">Căutare și filtrare</p>
    <h1>Task-uri</h1>
    <p><?= e($descrieriRol[$rol]) ?></p>
</div>
<section class="content-panel">
    <div class="section-heading search-heading">
        <h2>Găsește un task</h2>
        <?php if (areDrept('task_nou')): ?>
            <a class="button primary" href="<?= e(url('pagini/task-nou.php')) ?>">+ Task nou</a>
        <?php endif; ?>
    </div>
    <form class="field-form search-form" method="get" action="<?= e(url('pagini/taskuri.php', [], false)) ?>">
        <?php if ($rol !== 'vizitator'): ?><input type="hidden" name="rol" value="<?= e($rol) ?>"><?php endif; ?>
        <div class="field">
            <label for="q">Titlul task-ului</label>
            <input id="q" name="q" type="search" maxlength="100" value="<?= e($cautare) ?>" placeholder="De exemplu: paginii">
        </div>
        <div class="field">
            <label for="stare">Stare</label>
            <select id="stare" name="stare">
                <option value="">Toate stările</option>
                <?php foreach ($stari as $cod => $denumire): ?>
                    <option value="<?= e($cod) ?>" <?= $cod === $stare ? 'selected' : '' ?>><?= e($denumire) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="proiect">Proiect</label>
            <select id="proiect" name="proiect">
                <option value="">Toate proiectele</option>
                <?php foreach ($proiecte as $cod => $denumire): ?>
                    <option value="<?= e($cod) ?>" <?= $cod === $proiect ? 'selected' : '' ?>><?= e($denumire) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <button class="button primary" type="submit">Caută</button>
            <a class="button secondary" href="<?= e(url('pagini/taskuri.php')) ?>">Resetează</a>
        </div>
    </form>
</section>
<section class="content-panel" aria-labelledby="rezultate">
    <h2 id="rezultate">Rezultate</h2>
    <?php if ($eroare !== ''): ?>
        <p class="notice error" role="alert"><?= e($eroare) ?></p>
    <?php elseif ($rezultate === []): ?>
        <p class="notice" id="rezultat-gol">Nu am găsit task-uri pentru filtrele selectate.</p>
        <a class="text-link" href="<?= e(url('pagini/taskuri.php')) ?>">Vezi lista completă pentru rolul curent</a>
    <?php else: ?>
        <p class="quiet" id="numar-rezultate">Rezultate: <?= count($rezultate) ?></p>
        <div class="table-wrap task-table">
            <table>
                <thead><tr><th scope="col">Task</th><th scope="col">Proiect</th><th scope="col">Responsabil</th><th scope="col">Stare</th></tr></thead>
                <tbody>
                    <?php foreach ($rezultate as $task): ?>
                        <tr data-task-id="<?= $task['id'] ?>">
                            <th scope="row">
                                <?php if (isset($task['scenariu'])): ?>
                                    <a class="table-link" href="<?= e(url('pagini/estimari.php', ['scenariu' => $task['scenariu']])) ?>"><?= e($task['titlu']) ?></a>
                                <?php else: ?>
                                    <?= e($task['titlu']) ?>
                                <?php endif; ?>
                            </th>
                            <td><?= e($proiecte[$task['proiect']]) ?></td>
                            <td><?= e($responsabili[$task['responsabil']]) ?></td>
                            <td><span class="status <?= e(['nou' => 'neutral', 'lucru' => 'amber', 'gata' => 'green'][$task['stare']]) ?>"><?= e($stari[$task['stare']]) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
