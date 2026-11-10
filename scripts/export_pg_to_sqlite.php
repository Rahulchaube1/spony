<?php

$pg = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=coolify', 'coolify', 'password', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$sqliteFile = __DIR__ . '/../database/database.sqlite';
@unlink($sqliteFile);
$sqlite = new PDO('sqlite:' . $sqliteFile, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Get all tables in public schema
$stmt = $pg->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Found " . count($tables) . " tables in PostgreSQL.\n";

$sqlite->exec("PRAGMA foreign_keys = OFF;");

foreach ($tables as $table) {
    // Get columns
    $colStmt = $pg->prepare("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_schema = 'public' AND table_name = :t ORDER BY ordinal_position");
    $colStmt->execute(['t' => $table]);
    $cols = $colStmt->fetchAll();

    $colDefs = [];
    $colNames = [];
    foreach ($cols as $col) {
        $name = $col['column_name'];
        $type = strtoupper($col['data_type']);
        $sqliteType = 'TEXT';
        if (str_contains($type, 'INT')) $sqliteType = 'INTEGER';
        elseif (str_contains($type, 'BOOL')) $sqliteType = 'INTEGER';
        elseif (str_contains($type, 'FLOAT') || str_contains($type, 'DOUBLE') || str_contains($type, 'NUMERIC')) $sqliteType = 'REAL';
        elseif (str_contains($type, 'BLOB') || str_contains($type, 'BYTEA')) $sqliteType = 'BLOB';

        $colDefs[] = "\"{$name}\" {$sqliteType}";
        $colNames[] = "\"{$name}\"";
    }

    $createSql = "CREATE TABLE IF NOT EXISTS \"{$table}\" (" . implode(', ', $colDefs) . ");";
    $sqlite->exec($createSql);

    // Copy rows
    $rows = $pg->query("SELECT * FROM \"{$table}\"")->fetchAll();
    if (!empty($rows)) {
        $placeholders = implode(', ', array_fill(0, count($colNames), '?'));
        $insertSql = "INSERT INTO \"{$table}\" (" . implode(', ', $colNames) . ") VALUES ({$placeholders})";
        $insertStmt = $sqlite->prepare($insertSql);

        $sqlite->beginTransaction();
        foreach ($rows as $row) {
            $values = [];
            foreach ($cols as $col) {
                $val = $row[$col['column_name']];
                if (is_bool($val)) $val = $val ? 1 : 0;
                $values[] = $val;
            }
            $insertStmt->execute($values);
        }
        $sqlite->commit();
    }
    echo "Exported {$table} (" . count($rows) . " rows)\n";
}

echo "Database export complete! SQLite size: " . filesize($sqliteFile) . " bytes\n";
