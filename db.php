<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dir = dirname(DB_PATH);
    if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) {
        throw new RuntimeException('Unable to create storage directory.');
    }

    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA synchronous=NORMAL');
    $pdo->exec('PRAGMA busy_timeout=5000');
    $pdo->exec('PRAGMA foreign_keys=ON');

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS rates (
            rate_type TEXT PRIMARY KEY,
            rate REAL NOT NULL,
            fetched_at INTEGER NOT NULL,
            updated_at TEXT NOT NULL
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS calculations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            net1_usd REAL NOT NULL,
            net2_usd REAL NOT NULL,
            total_usd REAL NOT NULL,
            rate REAL NOT NULL,
            total_idr REAL NOT NULL,
            per_part REAL NOT NULL,
            created_at TEXT NOT NULL
        )'
    );

    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_calculations_created_at ON calculations(created_at DESC)');

    return $pdo;
}
