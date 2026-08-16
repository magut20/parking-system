<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $driver = Env::get('DB_CONNECTION', 'sqlite');

        try {
            if ($driver === 'mysql') {
                $host = Env::get('DB_HOST', '127.0.0.1');
                $port = Env::get('DB_PORT', '3306');
                $name = Env::get('DB_NAME', 'parking');
                $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
                $pdo = new PDO($dsn, Env::get('DB_USER', 'root'), Env::get('DB_PASS', ''));
            } else {
                $path = Env::get('DB_SQLITE_PATH', 'database/parking.sqlite');
                if (!str_starts_with($path, '/')) {
                    $path = dirname(__DIR__) . '/' . $path;
                }
                $isNew = !is_file($path);
                $pdo = new PDO('sqlite:' . $path);
                $pdo->exec('PRAGMA foreign_keys = ON');
                if ($isNew) {
                    self::migrateSqlite($pdo);
                }
            }

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            self::$connection = $pdo;
            return $pdo;
        } catch (PDOException $e) {
            // Never leak connection strings or credentials to the browser.
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            $env = Env::get('APP_ENV', 'production');
            if ($env === 'local') {
                die('Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES));
            }
            die('Sorry, something went wrong. Please try again later.');
        }
    }

    private static function migrateSqlite(PDO $pdo): void
    {
        $schema = dirname(__DIR__) . '/database/schema.sqlite.sql';
        if (is_file($schema)) {
            $pdo->exec((string) file_get_contents($schema));
        }

        $numSlots = (int) Env::get('NUM_SLOTS', '20');
        $count = (int) $pdo->query('SELECT COUNT(*) FROM slots')->fetchColumn();
        if ($count === 0 && $numSlots > 0) {
            $stmt = $pdo->prepare('INSERT INTO slots (slot_number) VALUES (?)');
            for ($i = 1; $i <= $numSlots; $i++) {
                $stmt->execute([$i]);
            }
        }
    }
}
