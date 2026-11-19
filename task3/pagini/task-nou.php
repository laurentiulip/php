<?php
require_once dirname(__DIR__) . '/config.php';
cereDrept('task_nou');
require dirname(__DIR__) . '/date/taskuri.php';

$titluPagina = 'Task nou';
$paginaActiva = 'task-nou';
$campuri = ['titlu', 'proiect', 'responsabil', 'prioritate', 'descriere'];
$valori = array_fill_keys($campuri, '');
$erori = [];
$trimis = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

// Validarea se face în PHP și funcționează și fără JavaScript.
if ($trimis) {
    foreach ($campuri as $camp) {
        $valori[$camp] = campText($_POST, $camp);
    }
    if ($valori['titlu'] === '') {
        $erori['titlu'] = 'Introdu titlul task-ului.';
    } elseif (lungime($valori['titlu']) > 100) {
        $erori['titlu'] = 'Titlul poate avea cel mult 100 de caractere.';
    }
    foreach ([
        'proiect' => [$proiecte, 'Alege un proiect din listă.'],
        'responsabil' => [$responsabili, 'Alege un responsabil din listă.'],
        'prioritate' => [$prioritati, 'Alege o prioritate din listă.'],
    ] as $camp => [$optiuni, $mesaj]) {
        if (!isset($optiuni[$valori[$camp]])) {
            $erori[$camp] = $mesaj;
        }
    }
    if (isset($_POST['descriere']) && !is_string($_POST['descriere'])) {
        $erori['descriere'] = 'Descrierea trebuie să fie un text.';
    } elseif (lungime($valori['descriere']) > 500) {
        $erori['descriere'] = 'Descrierea poate avea cel mult 500 de caractere.';
    }
    if ($erori !== []) {
        http_response_code(422);
    }
}
$confirmat = $trimis && $erori === [];
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro">
    <p class="eyebrow">Formular de propunere</p>
    <h1>Task nou</h1>
    <p>Completează datele și verifică propunerea. Datele apar doar în răspunsul curent.</p>
</div>
<?php if ($confirmat): ?>
    <section class="content-panel demo-form-panel" id="confirmare">
        <p class="eyebrow">Formular valid</p>
        <h2>Datele task-ului au fost validate</h2>
        <dl class="entity-details">
            <div><dt>Titlu</dt><dd><?= e($valori['titlu']) ?></dd></div>
            <div><dt>Proiect</dt><dd><?= e($proiecte[$valori['proiect']]) ?></dd></div>
            <div><dt>Responsabil</dt><dd><?= e($responsabili[$valori['responsabil']]) ?></dd></div>
            <div><dt>Prioritate</dt><dd><?= e($prioritati[$valori['prioritate']]) ?></dd></div>
            <div class="wide"><dt>Descriere</dt><dd><?= $valori['descriere'] === '' ? 'Nespecificată' : nl2br(e($valori['descriere'])) ?></dd></div>
        </dl>
        <p class="notice success">Propunerea este demonstrativă. Task-ul nu a fost salvat în listă.</p>
        <div class="form-actions">
            <a class="button primary" href="<?= e(url('pagini/task-nou.php')) ?>">Propune alt task</a>
            <a class="button secondary" href="<?= e(url('pagini/taskuri.php')) ?>">Înapoi la listă</a>
        </div>
    </section>
<?php else: ?>
    <section class="content-panel demo-form-panel">
        <h2>Datele propunerii</h2>
        <p class="quiet">Câmpurile marcate cu * sunt obligatorii.</p>
        <?php if ($erori !== []): ?><p class="notice error" role="alert">Verifică valorile din câmpurile marcate mai jos.</p><?php endif; ?>
        <form class="field-form task-form" method="post" action="<?= e(url('pagini/task-nou.php')) ?>" novalidate>
            <div class="field wide">
                <label for="titlu">Titlul task-ului *</label>
                <input id="titlu" name="titlu" required maxlength="100" value="<?= e($valori['titlu']) ?>" placeholder="De exemplu: Verificarea paginii de start" <?= isset($erori['titlu']) ? 'aria-invalid="true" aria-describedby="eroare-titlu"' : '' ?>>
                <?php if (isset($erori['titlu'])): ?><small id="eroare-titlu" class="field-error"><?= e($erori['titlu']) ?></small><?php endif; ?>
            </div>
            <?php foreach ([
                'proiect' => ['Proiect', $proiecte],
                'responsabil' => ['Responsabil', $responsabili],
                'prioritate' => ['Prioritate', $prioritati],
            ] as $camp => [$eticheta, $optiuni]): ?>
                <div class="field">
                    <label for="<?= e($camp) ?>"><?= e($eticheta) ?> *</label>
                    <select id="<?= e($camp) ?>" name="<?= e($camp) ?>" required <?= isset($erori[$camp]) ? 'aria-invalid="true" aria-describedby="eroare-' . e($camp) . '"' : '' ?>>
                        <option value="">Alege o opțiune</option>
                        <?php foreach ($optiuni as $cod => $denumire): ?>
                            <option value="<?= e($cod) ?>" <?= $cod === $valori[$camp] ? 'selected' : '' ?>><?= e($denumire) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erori[$camp])): ?><small id="eroare-<?= e($camp) ?>" class="field-error"><?= e($erori[$camp]) ?></small><?php endif; ?>
                </div>
            <?php endforeach; ?>
            <div class="field wide">
                <label for="descriere">Descriere (opțional)</label>
                <textarea id="descriere" name="descriere" rows="3" maxlength="500" <?= isset($erori['descriere']) ? 'aria-invalid="true" aria-describedby="eroare-descriere"' : '' ?>><?= e($valori['descriere']) ?></textarea>
                <?php if (isset($erori['descriere'])): ?><small id="eroare-descriere" class="field-error"><?= e($erori['descriere']) ?></small><?php endif; ?>
            </div>
            <div class="form-actions wide">
                <button class="button primary" type="submit">Verifică propunerea</button>
                <a class="button secondary" href="<?= e(url('pagini/taskuri.php')) ?>">Înapoi la listă</a>
            </div>
        </form>
    </section>
<?php endif; ?>
<?php require dirname(__DIR__) . '/footer.php'; ?>
