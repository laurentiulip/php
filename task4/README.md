# Laboratorul 4 — Sistem de gestionare a task-urilor

**Lipcanu Laurentiu · AAW-231 · Versiunea 4.0**

Proiectul folosește PDO cu driverul MySQL și păstrează permanent catalogul și solicitările. Un element este un tip de task. Cantitatea disponibilă reprezintă unități de lucru: de exemplu secțiuni de pagină sau runde de testare.

## Pornire în XAMPP

1. Pornește **Apache** și **MySQL** din XAMPP. Proiectul necesită PHP 8.1+ cu extensia `pdo_mysql`, MySQL 8.0.16+ sau MariaDB 10.4+.
2. Deschide phpMyAdmin la `http://localhost/phpmyadmin/`. La **Import**, alege `Taskuri_Laborator_4.sql` din arhivă. Scriptul creează baza separată `taskuri_lab4`, schema și datele inițiale. Importă o singură dată într-o bază nouă. Importul nu șterge date existente.
3. Copiază folderul `taskuri_lipcanu_laurentiu` în `C:\xampp\htdocs\`. Dacă există acolo versiunea 3, mut-o într-un folder de rezervă și copiază curat versiunea 4. Ține fișierul SQL în afara folderului servit de Apache.
4. Deschide `http://localhost/taskuri_lipcanu_laurentiu/`. Configurația locală implicită este `127.0.0.1:3306`, utilizatorul `root`, parolă vidă. Dacă setările diferă, editează `db-config.php` sau setează `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`.

Alternativ, cu serverul de baze de date pornit și schema importată, rulează `php -S 127.0.0.1:8080` din folderul aplicației și deschide `http://127.0.0.1:8080/`.

| Rol | E-mail | Parolă de demonstrație |
|---|---|---|
| Administrator | admin@taskuri.test | AdminLab4! |
| Utilizator | laurentiu@taskuri.test | TaskuriLab4! |
| Utilizator fără solicitări | ana@taskuri.test | AnaTaskuri4! |

Conturile sunt pentru rulare locală. În baza de date sunt stocate doar hash-urile. Pentru găzduire în afara mediului local, configurează un cont MySQL dedicat și înlocuiește conturile demonstrative.

## Baza de date și drepturile

- `users`: 3 conturi, e-mail unic și rol.
- `categories`: 3 categorii; `items`: 15 task-uri (14 active), câte 5 pe categorie.
- `requests`: 5 solicitări, dintre care 2 în așteptare, 2 aprobate și 1 respinsă.
- `request_items`: 11 linii, cu PK compus și două FK pentru relația N:M. Denumirea stocată pe linie păstrează textul original dacă task-ul este redenumit.
- Toate relațiile au `ON DELETE RESTRICT`. Catalogul poate șterge numai task-uri fără referințe; pentru cele folosite există dezactivare.
- Vizitatorul vede catalogul activ. Utilizatorul conectat trimite solicitări și le vede pe ale sale. Administratorul are și lista completă, decizii, CRUD de catalog și rapoarte.

Diagrama relațiilor se află în `Diagrama_Relatii_Taskuri.svg`.

## Parcurs pentru prezentare

1. Ca vizitator, deschide catalogul și filtrează după denumirea „formular” și categoria Testare.
2. Conectează-te ca Laurentiu. În **Solicitare nouă**, trimite prima dată fără cantități; apare eroarea. Introdu 2 unități la „Realizarea paginii de prezentare” și 1 la „Testarea navigării pe telefon”. Salvează și reține ID-ul. Deschide lista proprie și verifică statutul În așteptare. Pagina `pagini/solicitare.php?id=3` nu este disponibilă acestui cont.
3. Ieși din cont și conectează-te ca administrator. Deschide solicitarea din **Toate solicitările** și aprob-o. Verifică stocul: prima activitate scade de la 5 la 3, iar a doua de la 6 la 5. Respingerea unei solicitări nu scade stocul.
4. În **Categorii**, adaugă, modifică și verifică numărul de elemente. În **Elemente**, adaugă un task nou, modifică datele, dezactivează, reactivează și apoi șterge acel task nefolosit. Task-urile din istoric nu au buton de ștergere; baza respinge ștergerea și la solicitarea directă.
5. În **Rapoarte**, verifică toate cele cinci rapoarte. Alege intervalul și apasă **Exportă CSV**. Ambele date sunt incluse; la prima deschidere sunt selectate ultimele 30 de zile. Fișierul folosește UTF-8 și separatorul punct și virgulă.

## Organizarea codului

`config.php` pornește sesiunea și oferă conexiunea PDO, validarea, mesajele și controlul accesului. `lib/solicitari.php` conține tranzacțiile. `lib/rapoarte.php` conține filtrul de interval și exportul. Paginile reutilizează `header.php` și `footer.php`. Detaliile SQL se înregistrează în jurnalul PHP, de exemplu în jurnalul de erori al Apache sau în locația `error_log` din configurația PHP.
