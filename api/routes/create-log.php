<?php
// Подключаем MODX
require_once dirname(__DIR__, 2) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

// Проверяем, что объект $modx существует
if (!$modx) {
    http_response_code(500);
    echo json_encode(["error" => "MODX initialization failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['domen'], $data['content'], $data['type'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$sql = "INSERT INTO `modx_logs` (`domen`, `content`, `type`) VALUES (:domen, :content, :type)";
$stmt = $modx->prepare($sql);
$stmt->bindParam(':domen', $data['domen']);
$stmt->bindParam(':content', $data['content']);
$stmt->bindParam(':type', $data['type']);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to insert data"]);
}
