<?php
header('Content-Type: application/json; charset=UTF-8');
require_once "../config/database.php";

if (empty($pdo)) {
    http_response_code(503);
    echo json_encode(['error' => 'Database unavailable.']);
    exit;
}

$categoria = trim((string)($_GET['categoria'] ?? ''));
$dataInicio = trim((string)($_GET['inicio'] ?? ''));
$dataFim = trim((string)($_GET['fim'] ?? ''));

try {
    $stmt = $pdo->prepare("CALL sp_dashboard_metrics(100, :categoria, :inicio, :fim)");

    if ($categoria === '') {
        $stmt->bindValue(':categoria', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_STR);
    }

    if ($dataInicio === '') {
        $stmt->bindValue(':inicio', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':inicio', $dataInicio, PDO::PARAM_STR);
    }

    if ($dataFim === '') {
        $stmt->bindValue(':fim', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':fim', $dataFim, PDO::PARAM_STR);
    }

    $stmt->execute();
    $dadosDashboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'data' => $dadosDashboard,
        'filters' => [
            'categoria' => $categoria,
            'inicio' => $dataInicio,
            'fim' => $dataFim
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Dashboard query error: ' . $e->getMessage());
    echo json_encode(['error' => "Couldn't load the dashboard."]);
} catch (RuntimeException $e) {
    http_response_code(500);
    error_log('Dashboard runtime error: ' . $e->getMessage());
    echo json_encode(['error' => "Couldn't load the dashboard."]);
}
