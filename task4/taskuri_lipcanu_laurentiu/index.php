<?php
$titluPagina = 'Acasă';
$paginaActiva = 'acasa';
require __DIR__ . '/header.php';
$diapozitive = [
    ['planificare.svg', 'Planificarea task-urilor într-un panou cu activități.', '01 / PLANIFICARE', 'Activitățile tale, într-un singur loc.', 'Grupează sarcinile pe proiecte și păstrează o imagine clară a lucrului de făcut.'],
    ['colaborare.svg', 'Membrii unei echipe conectați la același proiect.', '02 / COLABORARE', 'Fiecare task are un responsabil.', 'Solicită unitățile de lucru necesare proiectului echipei.'],
    ['progres.svg', 'O listă de activități finalizate și indicatorul de progres.', '03 / PROGRES', 'Vezi ce ai realizat și ce urmează.', 'Urmărește solicitările trimise și deciziile administratorului.'],
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
<section class="overview">
    <p class="eyebrow">Catalog și solicitări</p>
    <h2>De la task disponibil la solicitare aprobată</h2>
    <p>Consultă catalogul, verifică disponibilitatea și solicită unități de lucru din contul tău. Deciziile și cantitățile cerute rămân vizibile în istoric.</p>
    <div class="form-actions">
        <a class="button primary" href="<?= e(url('pagini/taskuri.php')) ?>">Vezi catalogul</a>
        <?php if (user()): ?>
            <a class="button secondary" href="<?= e(url('pagini/solicitari.php')) ?>">Solicitările mele</a>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
