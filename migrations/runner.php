<?php
$dsn = 'mysql:host=db;dbname=modx;charset=utf8mb4';
$user = 'modx';
$password = 'secret';

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Создаём таблицу для хранения выполненных миграций
    $pdo->exec("CREATE TABLE IF NOT EXISTS modx_migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) UNIQUE,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");

    // Получаем список уже выполненных миграций
    $stmt = $pdo->query("SELECT migration FROM modx_migrations");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Загружаем и выполняем миграции
    foreach (glob(__DIR__ . '/scripts/*.php') as $file) {
        $migrationName = basename($file);

        if (!in_array($migrationName, $executedMigrations)) {
            $migration = require $file;
            $migration($pdo);

            // Фиксируем миграцию как выполненную
            $stmt = $pdo->prepare("INSERT INTO modx_migrations (migration) VALUES (:migration)");
            $stmt->execute(['migration' => $migrationName]);

            echo "✅ Миграция {$migrationName} выполнена.\n";
        } else {
            echo "⚠️  Миграция {$migrationName} уже была выполнена, пропускаем.\n";
        }
    }
} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}
