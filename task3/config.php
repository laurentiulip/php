<?php
// Configurația comună tuturor paginilor.
const DENUMIRE_PROIECT = 'Sistem de gestionare a task-urilor';
const VERSIUNE_APLICATIE = '3.0';
const AUTOR = 'Lipcanu Laurentiu';
const GRUPA = 'AAW-231';
const ORE_ALOCATE_PE_ZI = 4;

function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
function numar(float $valoare): string {
    return number_format($valoare, 2, ',', ' ');
}
// Același URL de bază funcționează în rădăcină și într-un folder din htdocs.
$director = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
if (basename($director) === 'pagini') {
    $director = str_replace('\\', '/', dirname($director));
}
$caleBaza = rtrim($director, '/.');
function url(string $pagina = 'index.php', array $parametri = [], bool $pastreazaRol = true): string {
    global $caleBaza, $rol;
    if ($pastreazaRol && $rol !== 'vizitator' && str_ends_with($pagina, '.php')) {
        $parametri = ['rol' => $rol] + $parametri;
    }
    $query = http_build_query($parametri);
    return $caleBaza . '/' . ltrim($pagina, '/') . ($query !== '' ? '?' . $query : '');
}

function campText(array $sursa, string $cheie): string {
    $valoare = $sursa[$cheie] ?? '';
    return is_string($valoare) ? preg_replace('/^\s+|\s+$/u', '', trim($valoare)) ?? '' : '';
}
function lungime(string $text): int {
    return preg_match_all('/./us', $text) ?: 0;
}
function textCautare(string $text): string {
    return strtolower(strtr($text, [
        'Ă' => 'a', 'Â' => 'a', 'Î' => 'i', 'Ș' => 's', 'Ț' => 't', 'Ş' => 's', 'Ţ' => 't',
        'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't', 'ş' => 's', 'ţ' => 't',
    ]));
}
require_once __DIR__ . '/roluri.php';
