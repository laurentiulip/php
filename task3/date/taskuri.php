<?php
// Date demonstrative. Acest fișier nu se modifică la trimiterea formularului.
$proiecte = [
    'site' => 'Organizarea activităților echipei',
    'testare' => 'Verificarea aplicației',
];
$responsabili = ['laurentiu' => AUTOR, 'ana' => 'Ana Popescu', 'victor' => 'Victor Rusu'];
$stari = ['nou' => 'Neînceput', 'lucru' => 'În lucru', 'gata' => 'Finalizat'];
$prioritati = ['scazuta' => 'Scăzută', 'medie' => 'Medie', 'ridicata' => 'Ridicată'];
$taskuri = [
    ['id' => 102, 'titlu' => 'Planificarea paginii de prezentare', 'proiect' => 'site', 'responsabil' => 'laurentiu', 'stare' => 'nou', 'public' => true, 'scenariu' => '1'],
    ['id' => 101, 'titlu' => 'Realizarea paginii de prezentare', 'proiect' => 'site', 'responsabil' => 'laurentiu', 'stare' => 'lucru', 'public' => true, 'scenariu' => '2'],
    ['id' => 103, 'titlu' => 'Pregătirea structurii proiectului', 'proiect' => 'site', 'responsabil' => 'laurentiu', 'stare' => 'gata', 'public' => true, 'scenariu' => '3'],
    ['id' => 104, 'titlu' => 'Verificarea formularului de task', 'proiect' => 'testare', 'responsabil' => 'laurentiu', 'stare' => 'lucru', 'public' => false],
    ['id' => 105, 'titlu' => 'Testarea navigării pe telefon', 'proiect' => 'testare', 'responsabil' => 'ana', 'stare' => 'gata', 'public' => false],
    ['id' => 106, 'titlu' => 'Revizuirea descrierilor de proiect', 'proiect' => 'site', 'responsabil' => 'victor', 'stare' => 'nou', 'public' => false],
];
