<?php
function validDate(string $date): bool {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date) || (int) substr($date, 0, 4) < 1000) return false;
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $parsed && $parsed->format('Y-m-d') === $date;
}
function requestsBetween(string $from, string $to): array {
    // DATETIME fără fracțiuni: ambele zile selectate sunt incluse integral.
    return query('SELECT r.id, r.created_at, u.name AS author, u.email, r.status,
        COUNT(ri.item_id) AS line_count, COALESCE(SUM(ri.quantity),0) AS units
        FROM requests r JOIN users u ON u.id=r.user_id LEFT JOIN request_items ri ON ri.request_id=r.id
        WHERE r.created_at BETWEEN ? AND ?
        GROUP BY r.id, r.created_at, u.name, u.email, r.status ORDER BY r.created_at DESC, r.id DESC',
        [$from . ' 00:00:00', $to . ' 23:59:59'])->fetchAll();
}
function csvCell(string $value): string {
    // Valorile introduse de utilizatori nu trebuie interpretate ca formule în Excel.
    return preg_match('/^[\x00-\x20]*[=+@-]/', $value) ? "'" . $value : $value;
}
function exportRequests(array $rows, string $from, string $to): never {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="solicitari-' . $from . '_' . $to . '.csv"');
    header('X-Content-Type-Options: nosniff');
    $stream = fopen('php://output', 'wb');
    fwrite($stream, "\xEF\xBB\xBF");
    fputcsv($stream, ['ID', 'Data creării', 'Utilizator', 'E-mail', 'Statut', 'Număr task-uri', 'Unități cerute'], ';', '"', '');
    foreach ($rows as $row) {
        fputcsv($stream, [$row['id'], $row['created_at'], csvCell($row['author']), csvCell($row['email']), STARI[$row['status']], $row['line_count'], $row['units']], ';', '"', '');
    }
    fclose($stream);
    exit;
}
