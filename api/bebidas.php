<?php
require_once "../config/database.php";

$lista_coffee_shop = [];
// Busca bebidas e suas categorias
if (!empty($pdo)) {
    try {
        $sql = "SELECT d.id_drink, d.name, d.price, d.stock, c.id_category, c.name AS category
                FROM drink d
                JOIN category c ON d.id_category = c.id_category
                ORDER BY c.name, d.name";
        $stmt = $pdo->query($sql);
        $lista_coffee_shop = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($lista_coffee_shop);
    } catch (Exception $e) {
        error_log('Error on getting drinks: ' . $e->getMessage());
    }
}
