<?php
// Teste HTTP. Rulează numai din terminal, cu serverul PHP sau Apache pornit.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Acest fișier se execută numai din terminal.'); }
$base = rtrim($argv[1] ?? 'http://localhost/taskuri_lipcanu_laurentiu', '/');
function obtine(string $url): array {
    $context = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 10]]);
    $html = @file_get_contents($url, false, $context);
    if ($html === false) { throw new RuntimeException('Server indisponibil: ' . $url); }
    preg_match('/\s(\d{3})\s/', $http_response_header[0] ?? '', $m);
    return [(int) ($m[1] ?? 0), $html];
}
function verifica(bool $ok, string $nume): void {
    if (!$ok) { throw new RuntimeException('FAIL ' . $nume); }
    echo 'PASS ' . $nume . PHP_EOL;
}
function indicator(string $html, string $id): string {
    preg_match('/id="' . preg_quote($id, '/') . '"[^>]*>(.*?)<\/[^>]+>/s', $html, $m);
    return trim(strip_tags($m[1] ?? ''));
}
try {
    foreach (['index.php', 'pagini/taskuri.php', 'pagini/proiecte.php', 'pagini/despre.php', 'pagini/conectare.php'] as $pagina) {
        [$status, $html] = obtine($base . '/' . $pagina);
        verifica($status === 200 && str_contains($html, '<header class="site-header">') && str_contains($html, '<main id="continut"') && str_contains($html, '<footer class="site-footer">') && str_contains($html, 'Versiunea 2.0') && !str_contains($html, 'Fatal error'), $pagina . ' HTTP 200 și componente comune');
    }
    foreach ([1 => ['0,00%', '8', '12,00 ore', '3'], 2 => ['37,50%', '5', '7,50 ore', '2'], 3 => ['100,00%', '0', '0,00 ore', '0']] as $scenariu => $asteptat) {
        [$status, $html] = obtine($base . '/pagini/taskuri.php?scenariu=' . $scenariu);
        $obtinut = array_map(fn($id) => indicator($html, $id), ['progres', 'subtaskuri-ramase', 'ore-ramase', 'zile-necesare']);
        verifica($status === 200 && $obtinut === $asteptat, 'Scenariul ' . $scenariu . ' progres subtask-uri ore zile');
    }
    foreach (['0', '4', 'abc', '%3Cscript%3E', '%5B%5D'] as $valoare) {
        [$status, $html] = obtine($base . '/pagini/taskuri.php?scenariu=' . $valoare);
        verifica($status === 400 && str_contains($html, 'Scenariu invalid') && str_contains($html, '<footer class="site-footer">'), 'Scenariu invalid ' . $valoare . ' HTTP 400');
    }
    [$status, $html] = obtine($base . '/pagini/taskuri.php?scenariu[]=1');
    verifica($status === 400 && str_contains($html, 'Scenariu invalid'), 'Parametru array respins');
    foreach (['planificare.svg', 'colaborare.svg', 'progres.svg'] as $imagine) {
        [$status, $html] = obtine($base . '/imagini/' . $imagine);
        verifica($status === 200 && str_contains($html, '<svg'), 'Imagine locală ' . $imagine);
    }
    echo 'Toate cele 17 verificări HTTP au trecut.' . PHP_EOL;
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage() . PHP_EOL); exit(1); }
