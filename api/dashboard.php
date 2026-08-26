<?php
require_once "../config/database.php";

// $lista_coffee_shop = [];
// Busca bebidas e suas categorias
// if (!empty($pdo)) {
//     try {
//         $sql = "SELECT d.id_drink, d.name, d.price, d.stock, c.id_category, c.name AS category
//                 FROM drink d
//                 JOIN category c ON d.id_category = c.id_category
//                 ORDER BY c.name, d.name";
//         $stmt = $pdo->query($sql);
//         $lista_coffee_shop = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         echo json_encode($lista_coffee_shop);
//     } catch (Exception $e) {
//         error_log('Error on getting drinks: ' . $e->getMessage());
//     }
// }


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
