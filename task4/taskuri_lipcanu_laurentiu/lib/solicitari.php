<?php
// Operațiile cu mai multe scrieri reușesc integral sau sunt anulate.
function transaction(callable $operation): mixed {
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $result = $operation();
        $pdo->commit();
        return $result;
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $error;
    }
}

function createRequest(int $userId, array $quantities, string $note): int {
    return transaction(function () use ($userId, $quantities, $note): int {
        // Toate operațiile blochează elementele în ordinea ID-ului.
        ksort($quantities, SORT_NUMERIC);
        $items = [];
        foreach ($quantities as $id => $qty) {
            $item = query('SELECT id, name, active, available_quantity FROM items WHERE id=? FOR UPDATE', [$id])->fetch();
            if (!$item || !(int) $item['active'] || (int) $item['available_quantity'] < $qty) {
                throw new DomainException('Disponibilitatea a fost schimbată. Verifică din nou cantitățile.');
            }
            $items[] = [$id, $qty, $item['name']];
        }
        query('INSERT INTO requests (user_id, note) VALUES (?, ?)', [$userId, $note]);
        $requestId = (int) db()->lastInsertId();
        foreach ($items as [$id, $qty, $name]) {
            query('INSERT INTO request_items (request_id, item_id, quantity, item_name) VALUES (?, ?, ?, ?)', [$requestId, $id, $qty, $name]);
        }
        return $requestId;
    });
}

function decideRequest(int $requestId, int $adminId, string $decision): void {
    transaction(function () use ($requestId, $adminId, $decision): void {
        $request = query('SELECT status FROM requests WHERE id=? FOR UPDATE', [$requestId])->fetch();
        if (!$request || $request['status'] !== 'pending') {
            throw new DomainException('Solicitarea a fost deja procesată sau nu mai există.');
        }
        if ($decision === 'approved') {
            $items = query('SELECT item_id, quantity FROM request_items WHERE request_id=? ORDER BY item_id', [$requestId])->fetchAll();
            if (!$items) throw new DomainException('Solicitarea nu conține task-uri.');
            foreach ($items as $item) {
                // UPDATE este atomic: dacă stocul nu ajunge, întreaga aprobare se anulează.
                $changed = query('UPDATE items SET available_quantity=available_quantity-? WHERE id=? AND active=1 AND available_quantity>=?',
                    [$item['quantity'], $item['item_id'], $item['quantity']])->rowCount();
                if ($changed !== 1) {
                    throw new DomainException('Un task este inactiv sau are prea puține unități disponibile. Solicitarea rămâne în așteptare.');
                }
            }
        }
        query('UPDATE requests SET status=?, decided_by=?, decided_at=CURRENT_TIMESTAMP WHERE id=?', [$decision, $adminId, $requestId]);
    });
}
