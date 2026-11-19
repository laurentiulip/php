<?php
$titluPagina = 'Acasă';
$paginaActiva = 'acasa';
require __DIR__ . '/header.php';
$diapozitive = [
    ['planificare.svg', 'Planificarea task-urilor într-un panou cu activități.', '01 / PLANIFICARE', 'Activitățile tale, într-un singur loc.', 'Grupează sarcinile pe proiecte și păstrează o imagine clară a lucrului de făcut.'],
    ['colaborare.svg', 'Membrii unei echipe conectați la același proiect.', '02 / COLABORARE', 'Fiecare task are un responsabil.', 'Organizează activitățile echipei și urmărește contribuția fiecărui membru.'],
    ['progres.svg', 'O listă de activități finalizate și indicatorul de progres.', '03 / PROGRES', 'Vezi ce ai realizat și ce urmează.', 'Compară task-urile finalizate cu cele planificate și estimează timpul rămas.'],
];
?>
<div class="page-heading"><p class="eyebrow">Panou principal</p><p class="quiet">Bun venit în proiectul tău de task-uri.</p></div>
<section class="carousel" aria-label="Prezentarea aplicației" aria-roledescription="carusel" tabindex="0">
    <div class="slides">
        <?php foreach ($diapozitive as $i => [$imagine, $alternativ, $eticheta, $titlu, $descriere]): ?>
        <article class="slide" data-slide role="group" aria-roledescription="diapozitiv" aria-label="<?= $i + 1 ?> din 3" <?= $i !== 0 ? 'hidden' : '' ?>>
            <div class="slide-copy"><p class="eyebrow"><?= e($eticheta) ?></p><h1><?= e($titlu) ?></h1><p><?= e($descriere) ?></p><a class="button primary" href="<?= e(url('pagini/taskuri.php')) ?>">Explorează task-urile <span aria-hidden="true">↗</span></a></div>
            <img class="slide-image" src="<?= e(url('imagini/' . $imagine)) ?>" alt="<?= e($alternativ) ?>" width="620" height="450" <?= $i === 0 ? 'fetchpriority="high"' : '' ?>>
        </article>
        <?php endforeach; ?>
    </div>
    <div class="carousel-controls"><div class="carousel-dots" aria-label="Selectarea imaginii"><?php for ($i = 0; $i < 3; $i++): ?><button type="button" class="dot <?= $i === 0 ? 'active' : '' ?>" data-dot="<?= $i ?>" aria-label="Afișează imaginea <?= $i + 1 ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"></button><?php endfor; ?></div><div class="carousel-arrows"><span class="slide-count" aria-hidden="true">01 / 03</span><button type="button" class="icon-button" data-prev aria-label="Imaginea precedentă">←</button><button type="button" class="icon-button" data-next aria-label="Imaginea următoare">→</button></div></div>
    <p class="sr-only" data-carousel-status aria-live="polite" aria-atomic="true">Imaginea 1 din 3</p>
</section>
<section class="overview" aria-labelledby="titlu-overview"><div class="section-heading"><div><p class="eyebrow">Exemplu de organizare</p><h2 id="titlu-overview">O privire asupra activităților</h2></div><span class="demo-label">Date demonstrative</span></div>
<div class="stats"><div><span class="stat-number">3</span><p>Task-uri în total</p></div><div><span class="stat-number">1</span><p>În lucru</p></div><div><span class="stat-number">1</span><p>Finalizat</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Task</th><th>Stare</th><th>Progres</th></tr></thead><tbody><tr><td>Planificarea paginii de prezentare</td><td><span class="status neutral">Neînceput</span></td><td>0,00%</td></tr><tr><td>Realizarea paginii de prezentare</td><td><span class="status amber">În lucru</span></td><td>37,50%</td></tr><tr><td>Pregătirea structurii proiectului</td><td><span class="status green">Finalizat</span></td><td>100,00%</td></tr></tbody></table></div>
<a class="text-link" href="<?= e(url('pagini/taskuri.php')) ?>">Vezi detaliile și calculele <span aria-hidden="true">→</span></a>
</section>
<?php require __DIR__ . '/footer.php'; ?>
