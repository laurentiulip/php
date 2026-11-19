<?php
// Rulează fiecare scenariu într-un proces PHP separat, pentru a evita redefinirea funcțiilor.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Rulează testele din terminal.'); }
$cazuri = [
    ['1', 0.0, 8, 0.0, 12.0, 3, false],
    ['2', 37.5, 5, 4.5, 7.5, 2, false],
    ['3', 100.0, 0, 9.0, 0.0, 0, true],
];
if (isset($argv[1])) {
    $caz = $cazuri[(int) $argv[1] - 1];
    $_GET['scenariu'] = $caz[0];
    ob_start();
    require __DIR__ . '/index.php';
    $html = ob_get_clean();
    $actual = [$progres, $subtaskuriRamase, $oreRealizateEstimate, $oreRamase, $zileNecesare, $esteFinalizat];
    foreach ($actual as $i => $valoare) {
        if (is_bool($valoare) ? $valoare !== $caz[$i + 1] : abs($valoare - $caz[$i + 1]) > 0.00001) {
            fwrite(STDERR, 'FAIL scenariul ' . $caz[0] . ', indicator ' . $i . PHP_EOL); exit(1);
        }
    }
    if (!str_contains($html, '<html lang="ro">') || !str_contains($html, 'Versiunea aplicației: 1.0')) { exit(1); }
    echo 'PASS scenariul ' . $caz[0] . PHP_EOL;
    exit(0);
}
foreach ($cazuri as $caz) {
    passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . $caz[0], $cod);
    if ($cod !== 0) { exit($cod); }
}
echo 'Toate cele 3 scenarii au trecut.' . PHP_EOL;
