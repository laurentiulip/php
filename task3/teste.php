<?php
// Rulare: php teste.php http://127.0.0.1:8080
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
$baza = rtrim($argv[1] ?? 'http://127.0.0.1:8080', '/');
$total = 0;
function cerere(string $cale, ?array $date = null): array {
    global $baza;
    $http = ['timeout' => 5, 'ignore_errors' => true, 'follow_location' => 0, 'method' => 'GET'];
    if ($date !== null) {
        $http['method'] = 'POST';
        $http['header'] = 'Content-Type: application/x-www-form-urlencoded';
        $http['content'] = http_build_query($date);
    }
    $html = @file_get_contents($baza . '/' . $cale, false, stream_context_create(['http' => $http]));
    if ($html === false) {
        throw new RuntimeException('Serverul local nu răspunde.');
    }
    preg_match('/^HTTP\/\S+ (\d{3})/', $http_response_header[0] ?? '', $status);
    return [(int) ($status[1] ?? 0), $html];
}
function verifica(bool $conditie, string $mesaj): void {
    global $total;
    if (!$conditie) {
        throw new RuntimeException('EȘEC: ' . $mesaj);
    }
    ++$total;
    echo "OK: $mesaj\n";
}
try {
    foreach (['index.php', 'pagini/taskuri.php', 'pagini/estimari.php', 'pagini/proiecte.php', 'pagini/despre.php',
        'pagini/conectare.php', 'pagini/task-nou.php?rol=coordonator', 'pagini/administrare.php?rol=administrator'] as $cale) {
        [$cod, $html] = cerere($cale);
        verifica($cod === 200 && str_contains($html, '<header class="site-header">')
            && str_contains($html, '<footer class="site-footer">') && str_contains($html, 'Versiunea 3.0'), $cale . ' folosește componentele comune');
    }
    foreach (['vizitator' => 3, 'membru' => 4, 'coordonator' => 6, 'administrator' => 6, 'necunoscut' => 3] as $rol => $numar) {
        [$cod, $html] = cerere('pagini/taskuri.php?rol=' . $rol);
        verifica($cod === 200 && substr_count($html, 'data-task-id=') === $numar, 'Lista pentru rolul ' . $rol);
    }
    [$cod, $html] = cerere('pagini/taskuri.php?rol=coordonator&q=navigarii&stare=gata&proiect=testare');
    verifica($cod === 200 && substr_count($html, 'data-task-id=') === 1 && str_contains($html, 'data-task-id="105"'), 'GET combină titlul, starea și proiectul');
    [$cod, $html] = cerere('pagini/taskuri.php?q=inexistent');
    verifica($cod === 200 && str_contains($html, 'id="rezultat-gol"'), 'GET cu zero rezultate');
    [$cod, $html] = cerere('pagini/taskuri.php?q%5B%5D=paginii');
    verifica($cod === 400 && str_contains($html, 'valori simple'), 'GET respinge parametrii de tip tablou');
    verifica(cerere('pagini/task-nou.php?rol=membru')[0] === 403, 'Membrul nu poate deschide direct formularul');
    verifica(cerere('pagini/administrare.php?rol=coordonator')[0] === 403, 'Coordonatorul nu poate accesa administrarea');
    $valid = ['titlu' => 'Verifică versiunea 3.0', 'proiect' => 'site', 'responsabil' => 'ana', 'prioritate' => 'medie', 'descriere' => 'Probă locală'];
    [$cod, $html] = cerere('pagini/task-nou.php?rol=coordonator', $valid);
    verifica($cod === 200 && str_contains($html, 'id="confirmare"') && str_contains($html, $valid['titlu']), 'POST valid returnează confirmarea');
    [$cod, $html] = cerere('pagini/task-nou.php?rol=coordonator', array_replace($valid, ['titlu' => '  ']));
    verifica($cod === 422 && str_contains($html, 'Introdu titlul task-ului.') && str_contains($html, 'value="ana" selected'), 'POST cu titlu lipsă păstrează celelalte valori');
    [$cod, $html] = cerere('pagini/task-nou.php?rol=administrator', array_replace($valid, ['prioritate' => 'inventata']));
    verifica($cod === 422 && str_contains($html, 'Alege o prioritate din listă.'), 'POST respinge o prioritate necunoscută');
    verifica(cerere('pagini/task-nou.php?rol=membru', $valid)[0] === 403, 'POST direct de la membru este refuzat');
    [$cod, $html] = cerere('pagini/taskuri.php?rol=coordonator');
    verifica($cod === 200 && substr_count($html, 'data-task-id=') === 6 && !str_contains($html, $valid['titlu']), 'POST nu modifică lista');
    foreach (['1' => '0,00%', '2' => '37,50%', '3' => '100,00%'] as $scenariu => $progres) {
        [$cod, $html] = cerere('pagini/estimari.php?rol=membru&scenariu=' . $scenariu);
        verifica($cod === 200 && str_contains($html, 'id="progres">' . $progres), 'Estimarea ' . $scenariu . ' este păstrată');
    }
    echo "\n$total verificări HTTP trecute.\n";
} catch (RuntimeException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
