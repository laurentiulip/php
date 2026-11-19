<?php
// Configurația comună tuturor paginilor. Versiunea corespunde laboratorului 2.
const DENUMIRE_PROIECT = 'Sistem de gestionare a task-urilor';
const VERSIUNE_APLICATIE = '2.0';
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
function url(string $pagina = 'index.php'): string {
    global $caleBaza;
    return $caleBaza . '/' . ltrim($pagina, '/');
}
