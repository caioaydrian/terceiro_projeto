<?php
require_once "../config/database.php";

if (!empty($pdo)) {

    try {
        $stmt = $pdo->query("SELECT id_sale, quantity, unit_price FROM vw_sales_complete");
        $dadosDashboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $dadosDashboard = [];
    } catch (RuntimeException $e) {
        $dadosDashboard = [];
    }

    echo json_encode($dadosDashboard); //JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
}
