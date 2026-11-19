-- Laboratorul 4. Import unic într-o bază nouă, MySQL 8.0.16+ sau MariaDB 10.4+.
CREATE DATABASE IF NOT EXISTS taskuri_lab4 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE taskuri_lab4;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(500) NOT NULL DEFAULT ''
) ENGINE=InnoDB;

CREATE TABLE items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    description VARCHAR(500) NOT NULL DEFAULT '',
    available_quantity INT NOT NULL DEFAULT 0,
    active TINYINT NOT NULL DEFAULT 1,
    CONSTRAINT ck_items_quantity CHECK (available_quantity >= 0),
    CONSTRAINT ck_items_active CHECK (active IN (0, 1)),
    CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    note VARCHAR(500) NOT NULL DEFAULT '',
    decided_by INT UNSIGNED NULL,
    decided_at DATETIME NULL,
    INDEX ix_requests_created (created_at),
    CONSTRAINT fk_requests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_requests_decider FOREIGN KEY (decided_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE request_items (
    request_id INT UNSIGNED NOT NULL,
    item_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    item_name VARCHAR(120) NOT NULL COMMENT 'Denumirea la momentul solicitarii',
    PRIMARY KEY (request_id, item_id),
    CONSTRAINT ck_request_items_quantity CHECK (quantity > 0),
    CONSTRAINT fk_request_items_request FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE RESTRICT,
    CONSTRAINT fk_request_items_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

START TRANSACTION;
-- Hash-uri generate cu password_hash(PASSWORD_DEFAULT) pentru conturile de demonstrație.
INSERT INTO users (id, name, email, password_hash, role) VALUES
(1, 'Administrator', 'admin@taskuri.test', '$2y$12$vfZZ/cNwD9LLsAdZFnLK3enZ.iDo3T2KnfD/ygLOdB4tL3L53QHpq', 'admin'),
(2, 'Lipcanu Laurentiu', 'laurentiu@taskuri.test', '$2y$12$QIL/AdT3pMYtnHn.fWE4W.klAKbV9wrUlFx9YLFlU9l2txhztf2MW', 'user'),
(3, 'Ana Popescu', 'ana@taskuri.test', '$2y$12$vGYcbDymuDO1yuY5LEFCQerKzh2LDDox8MkjaejRU7VxHvokQ/qpK', 'user');

INSERT INTO categories (id, name, description) VALUES
(1, 'Dezvoltare', 'Implementarea și întreținerea proiectului PHP.'),
(2, 'Testare', 'Verificarea funcțiilor și a interfeței.'),
(3, 'Organizare', 'Planificarea și documentarea activităților.');

-- Cantitatea reprezintă unități de lucru disponibile, după solicitările aprobate din exemplu.
INSERT INTO items (id, category_id, name, description, available_quantity, active) VALUES
(1, 1, 'Realizarea paginii de prezentare', 'Fiecare unitate acoperă o secțiune a paginii.', 5, 1),
(2, 1, 'Structurarea fișierelor PHP', 'Organizarea componentelor comune în proiect.', 3, 1),
(3, 1, 'Implementarea unui formular', 'Câmpuri, mesaje de eroare și confirmare.', 4, 1),
(4, 1, 'Integrarea unui raport SQL', 'Un raport cu date extrase din baza de date.', 2, 1),
(5, 1, 'Adaptarea unei pagini la mobil', 'Verificarea afișării la lățimi mici.', 0, 1),
(6, 2, 'Testarea navigării pe telefon', 'Un parcurs complet prin paginile aplicației.', 6, 1),
(7, 2, 'Verificarea formularului de task', 'Verificarea câmpurilor obligatorii și a mesajelor.', 3, 1),
(8, 2, 'Verificarea unui rol de acces', 'Compararea paginilor disponibile pentru un rol.', 4, 1),
(9, 2, 'Testarea exportului CSV', 'Verificarea coloanelor și a datelor exportate.', 0, 1),
(10, 2, 'Retestarea interfeței vechi', 'Activitate arhivată; planificarea este închisă.', 0, 0),
(11, 3, 'Planificarea activităților', 'Împărțirea unui obiectiv în activități.', 4, 1),
(12, 3, 'Redactarea documentației', 'Actualizarea unui capitol din documentație.', 5, 1),
(13, 3, 'Revizuirea descrierilor', 'Verificarea textului și a terminologiei.', 3, 1),
(14, 3, 'Pregătirea unei demonstrații', 'Configurarea și verificarea parcursului de prezentare.', 2, 1),
(15, 3, 'Actualizarea listei de activități', 'Revizuirea listei înaintea unei etape noi.', 7, 1);

INSERT INTO requests (id, user_id, created_at, status, note, decided_by, decided_at) VALUES
(1, 2, CURRENT_DATE - INTERVAL 10 DAY + INTERVAL 9 HOUR, 'approved', 'Pregătirea paginii principale.', 1, CURRENT_DATE - INTERVAL 9 DAY),
(2, 2, CURRENT_DATE - INTERVAL 7 DAY + INTERVAL 11 HOUR, 'rejected', 'Revizuire pentru următoarea etapă.', 1, CURRENT_DATE - INTERVAL 6 DAY),
(3, 1, CURRENT_DATE - INTERVAL 5 DAY + INTERVAL 14 HOUR, 'approved', 'Planificare și documentare.', 1, CURRENT_DATE - INTERVAL 4 DAY),
(4, 2, CURRENT_DATE - INTERVAL 3 DAY + INTERVAL 10 HOUR, 'pending', 'Verificarea versiunii curente.', NULL, NULL),
(5, 2, CURRENT_DATE - INTERVAL 1 DAY + INTERVAL 15 HOUR, 'pending', 'Verificări suplimentare înaintea prezentării.', NULL, NULL);

INSERT INTO request_items (request_id, item_id, quantity, item_name)
SELECT x.request_id, i.id, x.quantity, i.name
FROM (
    SELECT 1 AS request_id, 1 AS item_id, 2 AS quantity
    UNION ALL SELECT 1, 2, 1
    UNION ALL SELECT 1, 6, 3
    UNION ALL SELECT 2, 10, 1
    UNION ALL SELECT 3, 1, 1
    UNION ALL SELECT 3, 11, 2
    UNION ALL SELECT 3, 12, 4
    UNION ALL SELECT 4, 1, 2
    UNION ALL SELECT 4, 8, 1
    UNION ALL SELECT 5, 7, 2
    UNION ALL SELECT 5, 13, 1
) AS x
JOIN items i ON i.id = x.item_id;
COMMIT;
