# Laboratorul 3 — Sistem de gestionare a task-urilor

**Autor:** Lipcanu Laurentiu · **Grupa:** AAW-231 · **Versiune:** 3.0

## Pornire

**XAMPP:** extrage folderul `taskuri_lipcanu_laurentiu` în `C:\xampp\htdocs\`, pornește Apache și deschide `http://localhost/taskuri_lipcanu_laurentiu/`. Dacă ai deja folderul laboratorului 2, păstrează-l ca rezervă în afara `htdocs` și folosește folderul complet din această arhivă.

**Serverul PHP integrat:** din folderul proiectului rulează `php -S 127.0.0.1:8080`, apoi accesează `http://127.0.0.1:8080/`. În PowerShell poți folosi `& 'C:\xampp\php\php.exe' -S 127.0.0.1:8080`. Este necesar PHP 8.0 sau mai nou; nu este necesar MySQL.

## Date și drepturi

Toate rolurile au acces la Acasă, Task-uri, Estimări, Proiecte, Despre și Rol demonstrativ. În antet, apasă pe numele rolului ca să schimbi perspectiva.

| Rol | Lista de task-uri | Task nou | Administrare |
|---|---|---|---|
| Vizitator (implicit) | 3 publice | Nu | Nu |
| Membru (Laurentiu) | 4 atribuite | Nu | Nu |
| Coordonator | Toate cele 6 | Da | Nu |
| Administrator | Toate cele 6 | Da | Da |

Rolul este un parametru GET validat prin lista de roluri. Site-ul îl păstrează în linkurile interne și în căutare. Rolurile și datele sunt strict demonstrative, fără sesiune, conturi sau autentificare reală. Paginile restricționate verifică drepturile în PHP și răspund cu HTTP 403 dacă rolul curent nu are acces.

## Demonstrație

1. Ca Vizitator, deschide **Task-uri** și caută `paginii`: apar 2 rezultate. Încearcă `inexistent`: apare mesajul pentru zero rezultate.
2. Alege **Membru** și observă că apar doar task-urile atribuite lui Laurentiu. Alege **Coordonator**: apar toate cele 6.
3. Ca Coordonator, caută `navigarii`, alege **Finalizat** și **Verificarea aplicației**: apare „Testarea navigării pe telefon”.
4. Deschide **Task nou** și trimite formularul gol: PHP marchează cele patru câmpuri obligatorii (HTTP 422). Completează titlul, proiectul, responsabilul și prioritatea, apoi retrimite. Descrierea e opțională.
5. Verifică sumarul confirmării. Întoarce-te la listă: task-ul nu a fost salvat.
6. Alege **Administrator** și deschide **Administrare** din meniu. Revino la Vizitator și accesează direct `pagini/administrare.php`: primești HTTP 403.

## Structură

- `index.php`, `header.php`, `footer.php`: pagina principală cu carusel și componentele comune.
- `config.php`, `roluri.php`: configurație, acces, validare și generare de URL-uri.
- `date/taskuri.php`: tablouri cu 6 task-uri, 2 proiecte, responsabili, stări și priorități.
- `pagini/taskuri.php`: căutare și filtrare din `$_GET`. Pagina `estimari.php` păstrează calculele de progres.
- `pagini/task-nou.php`: prelucrarea și validarea datelor din `$_POST`.
- `pagini/administrare.php`, `conectare.php`, `proiecte.php`, `despre.php`: drepturi, selectorul de rol și paginile informative.

## Teste HTTP

Cu serverul pornit: `php teste.php http://127.0.0.1:8080` sau, în XAMPP, `& 'C:\xampp\php\php.exe' .\teste.php http://localhost/taskuri_lipcanu_laurentiu`. Testele verifică paginile, vizibilitatea pe roluri, căutarea, validarea POST, accesul direct și calculele moștenite.
