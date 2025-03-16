<?php
// Подключаем MODX
require_once dirname(__DIR__) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

// Подключаем .env
require_once MODX_CORE_PATH . 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Получаем API-ключ
$apiKey = $_ENV['API_KEY'] ?? null;

// Устанавливаем заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// 📌 Получаем путь запроса
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// 📌 Убираем `/api/` из пути
$path = trim(str_replace('/api/', '', parse_url($requestUri, PHP_URL_PATH)), '/');

// 📌 Проверяем API-ключ
$headers = getallheaders();
if (!isset($headers['Authorization']) || $headers['Authorization'] !== 'Bearer ' . $apiKey) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized " . $headers['Authorization']]);
    exit;
}

// 📌 Роутинг API
switch ($path) {
    case 'create-log':
        if ($method === 'POST') {
            require_once __DIR__ . '/routes/create-log.php';
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
        break;

    case 'get-logs':
        if ($method === 'POST') {
            require_once __DIR__ . '/routes/get-logs.php';
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Route not found"]);
}
exit;
