# Laboratorul 1 — Sistem de gestionare a task-urilor

Autor: Lipcanu Laurentiu. Grupa: AAW-231. Versiunea aplicației: 1.0.

Proiect didactic pentru variabile, constante, tipuri de date, expresii PHP și afișare HTML/CSS. Entități planificate: Utilizator, Proiect, Task. Baza de date, formularele CRUD și autentificarea sunt prevăzute pentru laboratoarele următoare.

## Pornire în Windows cu XAMPP

1. Instalează XAMPP din https://www.apachefriends.org/download.html în `C:\xampp` și instalează Visual Studio Code și Postman din paginile lor oficiale.
2. Verifică versiunea PHP. Fișa cere **PHP 8.3 x64**. Pachetul XAMPP Windows listat de Apache Friends conține PHP 8.2.12, deci instalarea lui singură nu îndeplinește această cerință. Urmează secțiunea următoare pentru PHP 8.3.
3. Copiază întregul folder `taskuri_lipcanu_laurentiu` în `C:\xampp\htdocs\`. Fișierul final trebuie să fie `C:\xampp\htdocs\taskuri_lipcanu_laurentiu\index.php`.
4. În XAMPP Control Panel apasă Start lângă Apache. Verifică `http://localhost/`.
5. Deschide folderul proiectului cu File → Open Folder în Visual Studio Code.
6. Deschide `http://localhost/taskuri_lipcanu_laurentiu/verifica_mediul.php`. Verifică PHP 8.3, 64 biți și interfața Apache.
7. Deschide `http://localhost/taskuri_lipcanu_laurentiu/`. Nu deschide `index.php` prin dublu clic.
8. Selectează pe pagină scenariile 1, 2 și 3 și compară rezultatele cu tabelul de mai jos.

## Configurarea PHP 8.3 x64 în XAMPP

Acești pași sunt pentru o instalare locală XAMPP Windows în `C:\xampp`. Adaptarea nu a fost executată pe calculatorul autorului. Păstrează copia configurației vechi pentru revenire.

1. Oprește Apache. Salvează copii ale `C:\xampp\php` și `C:\xampp\apache\conf\extra\httpd-xampp.conf`.
2. Din https://www.php.net/downloads.php?os=windows&version=8.3 descarcă arhiva **PHP 8.3, x64, Thread Safe (TS)**. Pentru modulul Apache este necesar TS. Instalează runtime-ul Visual C++ x64 indicat de pagina PHP dacă lipsește.
3. Redenumește directorul vechi `php` în `php_backup`. Extrage arhiva într-un director nou `C:\xampp\php`, astfel încât `php.exe` și `php8apache2_4.dll` să se afle direct în el. Nu amesteca fișiere din două versiuni.
4. Copiază `php.ini-development` ca `php.ini`. În acesta setează `extension_dir = "C:/xampp/php/ext"` și `date.timezone = "Europe/Chisinau"`. Pentru laborator nu sunt necesare extensii de bază de date.
5. În `httpd-xampp.conf`, verifică blocul PHP existent. Trebuie să existe o singură directivă activă `LoadModule php_module "C:/xampp/php/php8apache2_4.dll"`, iar `PHPINIDir` să indice `C:/xampp/php`. Păstrează asocierea extensiei `.php` cu `application/x-httpd-php`. Nu adăuga a doua încărcare a aceluiași modul. Dacă Apache raportează un fișier lipsă într-o directivă `LoadFile`, verifică acea cale față de noua distribuție și compatibilitatea indicată de documentația PHP.
6. În PowerShell rulează:

```powershell
& 'C:\xampp\php\php.exe' -v
& 'C:\xampp\apache\bin\httpd.exe' -t
```

7. Dacă verificarea configurației arată `Syntax OK`, pornește Apache. Accesează `verifica_mediul.php` prin localhost și confirmă `Cerință PHP 8.3 x64: ÎNDEPLINITĂ`. Versiunea din browser este verificarea decisivă, deoarece executabilul din terminal poate fi diferit.
8. Dacă Apache nu pornește, citește `C:\xampp\apache\logs\error.log` și corectează eroarea indicată. Pentru revenire, oprește Apache și restaurează folderul și configurația salvate.

Documentație: https://www.php.net/manual/en/install.windows.apache2.php

## Calcule și ipoteze

Subtask-urile au ponderi egale. Orele realizate sunt estimate din progres, nu măsurate cu un cronometru. ORE_ALOCATE_PE_ZI = 4 este o regulă configurabilă a proiectului, nu o normă legală.

- subtaskuriRamase = numarSubtaskuri - subtaskuriFinalizate
- progres = subtaskuriFinalizate / numarSubtaskuri × 100
- oreRealizateEstimate = oreEstimate × progres / 100
- oreRamase = oreEstimate - oreRealizateEstimate
- zileNecesare = ceil(oreRamase / 4)

| Scenariu | Total | Finalizate | Ore estimate | Progres | Subtask-uri rămase | Ore realizate estimate | Ore rămase | Zile |
|---|---:|---:|---:|---:|---:|---:|---:|---:|
| 1 Neînceput | 8 | 0 | 12 | 0% | 8 | 0 | 12 | 3 |
| 2 În lucru | 8 | 3 | 12 | 37,50% | 5 | 4,50 | 7,50 | 2 |
| 3 Finalizat | 6 | 6 | 9 | 100% | 0 | 9 | 0 | 0 |

Acestea sunt rezultate așteptate, calculate independent. Execuția PHP și HTTP pe localhost trebuie confirmată local. Nu sunt prezentate drept teste executate.

## Teste automate în PHP

Deschide PowerShell în folderul proiectului:

```powershell
& 'C:\xampp\php\php.exe' -l .\index.php
& 'C:\xampp\php\php.exe' -l .\teste.php
& 'C:\xampp\php\php.exe' .\teste.php
```

Rezultatul așteptat este câte un `PASS` pentru fiecare scenariu și mesajul `Toate cele 3 scenarii au trecut.`. Testele încarcă exact pagina PHP și compară toate rezultatele cu valori așteptate independente. În `index.php`, poți modifica valorile din blocul de date pentru a verifica și alte exemple. Menține numărul total de subtask-uri pozitiv, numărul finalizat între 0 și total, iar orele nenegative.

## Teste Postman

Importă `Laborator1.postman_collection.json`. Variabila `base_url` are valoarea `http://localhost/taskuri_lipcanu_laurentiu`. Rulează colecția cu Collection Runner. Sunt incluse cele trei scenarii și un scenariu invalid care trebuie să primească HTTP 400. Răspunsurile valide conțin HTML, nu JSON. Colecția este pregătită pentru rulare, nu executată în mediul de creare.

## Dovezi pentru raport

După configurarea locală, păstrează capturi reale cu: Apache pornit, pagina de verificare PHP 8.3 x64, cele trei scenarii în browser cu URL vizibil și rezultatul testelor. Nu înlocui aceste dovezi cu capturi simulate. Raportul conține rezultatele așteptate și procedura de verificare.

## Fișiere

- `index.php`: toate datele, calculele și structura HTML cerute de laborator.
- `style.css`: stilizarea paginii și adaptarea la ecrane mici.
- `verifica_mediul.php`: versiunea PHP, arhitectura și interfața serverului.
- `teste.php`: verificarea automată a celor trei scenarii în CLI.
- `Laborator1.postman_collection.json`: teste HTTP de importat în Postman.
- `README.md`: instalare, rulare și explicații.
