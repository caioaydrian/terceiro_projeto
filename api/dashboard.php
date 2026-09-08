<?php
header('Content-Type: application/json; charset=UTF-8');
require_once "../config/database.php";

if (empty($pdo)) {
    http_response_code(503);
    echo json_encode(['error' => 'Database unavailable.']);
    exit;
}

try {
    $stmt = $pdo->query("CALL sp_dashboard_metrics(100, NULL, NULL, NULL)");
    $dadosDashboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $dadosDashboard]);
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Dashboard query error: ' . $e->getMessage());
    echo json_encode(['error' => "Couldn't load the dashboard."]);
} catch (RuntimeException $e) {
    http_response_code(500);
    error_log('Dashboard runtime error: ' . $e->getMessage());
    echo json_encode(['error' => "Couldn't load the dashboard."]);
}
