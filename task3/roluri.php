<?php
// Rolul este ales pentru demonstrație; parametrul nu autentifică utilizatorul.
const ROLURI = [
    'vizitator' => 'Vizitator',
    'membru' => 'Membru',
    'coordonator' => 'Coordonator',
    'administrator' => 'Administrator',
];

$rolCerut = $_GET['rol'] ?? 'vizitator';
$rol = is_string($rolCerut) && isset(ROLURI[$rolCerut]) ? $rolCerut : 'vizitator';

function areDrept(string $actiune): bool {
    global $rol;
    return match ($actiune) {
        'task_nou' => in_array($rol, ['coordonator', 'administrator'], true),
        'administrare' => $rol === 'administrator',
        default => false,
    };
}

function taskuriVizibile(array $taskuri): array {
    global $rol;
    return array_filter($taskuri, static fn(array $task): bool => match ($rol) {
        'administrator', 'coordonator' => true,
        'membru' => $task['responsabil'] === 'laurentiu',
        default => $task['public'],
    });
}

function cereDrept(string $actiune): void {
    global $rol;
    if (areDrept($actiune)) {
        return;
    }
    http_response_code(403);
    $titluPagina = 'Acces restricționat';
    require __DIR__ . '/header.php';
    require __DIR__ . '/componente/acces-restrictionat.php';
    require __DIR__ . '/footer.php';
    exit;
}
