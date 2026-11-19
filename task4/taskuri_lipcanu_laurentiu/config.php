<?php
declare(strict_types=1);
const DENUMIRE_PROIECT = 'Sistem de gestionare a task-urilor';
const VERSIUNE_APLICATIE = '4.0';
const AUTOR = 'Lipcanu Laurentiu';
const GRUPA = 'AAW-231';
const STARI = ['pending' => 'În așteptare', 'approved' => 'Aprobată', 'rejected' => 'Respinsă'];

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('session.use_strict_mode', '1');
session_name('TaskuriLab4');
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax', 'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'use_only_cookies' => true]);
header('Cache-Control: no-store');

$director = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$caleBaza = rtrim(basename($director) === 'pagini' ? dirname($director) : $director, '/.');
function url(string $pagina = 'index.php', array $query = []): string {
    global $caleBaza;
    return $caleBaza . '/' . ltrim($pagina, '/') . ($query ? '?' . http_build_query($query) : '');
}

function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $c = require __DIR__ . '/db-config.php';
        $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $c['user'], $c['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}
function query(string $sql, array $values = []): PDOStatement {
    $stmt = db()->prepare($sql);
    $stmt->execute($values);
    return $stmt;
}
function logDatabase(PDOException $error): void {
    error_log('[Taskuri 4.0] SQL ' . $error->getCode() . ': ' . $error->getMessage());
}
set_exception_handler(function (Throwable $error): void {
    if ($error instanceof PDOException) {
        logDatabase($error);
    } else {
        error_log('[Taskuri 4.0] ' . $error);
    }
    fail(500, 'Serviciu indisponibil', 'Operația nu a putut fi finalizată. Încearcă din nou sau verifică setările bazei de date.');
});

function e(string|int|null $text): string {
    return htmlspecialchars((string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function field(array $data, string $name): string {
    return isset($data[$name]) && is_string($data[$name]) ? trim($data[$name]) : '';
}
function lengthOf(string $text): int {
    $length = preg_match_all('/./us', $text);
    return $length === false ? strlen($text) : $length;
}
function wholeNumber(mixed $value, int $min, int $max = 2147483647): ?int {
    if (!is_string($value) && !is_int($value)) return null;
    $result = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
    return $result === false ? null : $result;
}
function idFrom(mixed $value): int {
    $id = wholeNumber($value, 1, 4294967295);
    if ($id === null) fail(404, 'Pagină negăsită', 'Înregistrarea cerută nu a fost găsită.');
    return $id;
}
function isPost(): bool { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }
function redirect(string $page = 'index.php', array $query = []): never {
    header('Location: ' . url($page, $query), true, 303);
    exit;
}
function fail(int $code, string $title, string $message): never {
    http_response_code($code);
    $titluPagina = $title;
    // Pentru eroare de conexiune, antetul folosește identitatea deja cunoscută.
    $headerUser = $GLOBALS['authUser'] ?? null;
    require __DIR__ . '/header.php';
    echo '<section class="content-panel"><h1>' . e($title) . '</h1><p>' . e($message) . '</p><a class="text-link" href="' . e(url()) . '">Înapoi la pagina principală</a></section>';
    require __DIR__ . '/footer.php';
    exit;
}
function user(): ?array {
    if (array_key_exists('authUser', $GLOBALS)) return $GLOBALS['authUser'];
    $id = $_SESSION['user_id'] ?? null;
    return $GLOBALS['authUser'] = $id ? (query('SELECT id, name, email, role FROM users WHERE id = ?', [$id])->fetch() ?: null) : null;
}
function requireUser(): array {
    $u = user();
    if (!$u) {
        flash('Conectează-te pentru a continua.', 'info');
        redirect('pagini/conectare.php');
    }
    return $u;
}
function requireAdmin(): array {
    $u = requireUser();
    if ($u['role'] !== 'admin') fail(403, 'Acces restricționat', 'Această operație este disponibilă administratorului.');
    return $u;
}
function token(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function csrfField(): void {
    echo '<input type="hidden" name="_csrf" value="' . e(token()) . '">';
}
function protectPost(): void {
    if (!isPost()) {
        header('Allow: POST');
        fail(405, 'Metodă nepermisă', 'Această operație se efectuează prin formular.');
    }
    if (!hash_equals(token(), field($_POST, '_csrf'))) {
        fail(403, 'Cerere nevalidă', 'Reîncarcă pagina și trimite formularul din nou.');
    }
}
function flash(string $message, string $kind = 'success'): void {
    $_SESSION['flash'] = [$message, $kind];
}
function formError(array $errors, string $key): void {
    if (isset($errors[$key])) echo '<small class="field-error" role="alert">' . e($errors[$key]) . '</small>';
}
function formInvalid(array $errors): void {
    if ($errors) http_response_code(422);
}
function statusLabel(string $status): void {
    $class = ['pending' => 'amber', 'approved' => 'green', 'rejected' => 'neutral'][$status] ?? 'neutral';
    echo '<span class="status ' . $class . '">' . e(STARI[$status] ?? $status) . '</span>';
}
