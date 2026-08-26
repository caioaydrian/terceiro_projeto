<?php
require_once __DIR__ . "/../config/database.php";

$lista_coffee_shop = [];
$categorias = [];
$categoria_selecionada = $_GET["category"] ?? "All";
$erro_menu = null;

if (!empty($pdo)) {
    try {
        $sql = "SELECT d.id_drink, d.name, d.price, d.stock, c.id_category, c.name AS category
                FROM drink d
                JOIN category c ON d.id_category = c.id_category
                ORDER BY c.name, d.name";
        $stmt = $pdo->query($sql);
        $lista_coffee_shop = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($lista_coffee_shop as $drink) {
            $categorias[$drink["category"]][] = $drink;
        }
    } catch (Exception $e) {
        error_log("Error on getting drinks: " . $e->getMessage());
        $erro_menu = "Couldn't load the menu.";
    }
} else {
    $erro_menu = "Couldn't load the menu.";
}

$categorias_exibidas = $categoria_selecionada === "All"
    ? $categorias
    : (isset($categorias[$categoria_selecionada])
        ? [$categoria_selecionada => $categorias[$categoria_selecionada]]
        : []);
?>

<main>
    <section class="menu">
        <h1>Our Menu</h1>
        <div class="menu-options" role="tablist" aria-label="Categorias do menu">
            <?php if (!empty($categorias)): ?>
                <a class="menu-option<?= $categoria_selecionada === "All" ? " active" : "" ?>"
                    href="?paginas=menu&category=All"
                    role="tab"
                    aria-selected="<?= $categoria_selecionada === "All" ? "true" : "false" ?>">All</a>
                <?php foreach ($categorias as $categoria => $items): ?>
                    <a class="menu-option<?= $categoria_selecionada === $categoria ? " active" : "" ?>"
                        href="?paginas=menu&category=<?= urlencode($categoria) ?>"
                        role="tab"
                        aria-selected="<?= $categoria_selecionada === $categoria ? "true" : "false" ?>"><?= htmlspecialchars($categoria, ENT_QUOTES, "UTF-8") ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="category-list">
            <?php if ($erro_menu !== null): ?>
                <?= htmlspecialchars($erro_menu, ENT_QUOTES, "UTF-8") ?>
            <?php elseif (empty($categorias)): ?>
                No items in this category.
            <?php else: ?>
                <?php foreach ($categorias_exibidas as $categoria => $items): ?>
                    <section class="category">
                        <h2><?= htmlspecialchars($categoria, ENT_QUOTES, "UTF-8") ?></h2>
                        <ul class="drink-list">
                            <?php foreach ($items as $drink): ?>
                                <li class="drink">
                                    <span class="name"><?= htmlspecialchars($drink["name"], ENT_QUOTES, "UTF-8") ?></span>
                                    <span class="price">R$ <?= number_format((float) $drink["price"], 2, ",", ".") ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>