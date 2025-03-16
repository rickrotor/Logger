<?php
return function ($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS modx_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        domen VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        type ENUM('info', 'warning', 'error') NOT NULL DEFAULT 'info',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    $pdo->exec($sql);
    echo "✅ logs-migrate выполнен.\n";
};
