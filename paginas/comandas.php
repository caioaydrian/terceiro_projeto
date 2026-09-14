<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['usuario_autenticado'])) {
    header('Location: ?paginas=login');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$mensagem = $_SESSION['flash_mensagem'] ?? '';
$erro = $_SESSION['flash_erro'] ?? '';
unset($_SESSION['flash_mensagem'], $_SESSION['flash_erro']);

$clientes = [];
$produtos = [];
$comandas = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    if ($acao === 'criar_comanda' && !empty($pdo)) {
        $clienteId = (int)($_POST['id_client'] ?? 0);
        $produtoId = (int)($_POST['id_drink'] ?? 0);
        $quantidade = (int)($_POST['quantidade'] ?? 0);
        $data = trim((string)($_POST['data_abertura'] ?? date('Y-m-d H:i:s')));

        if ($clienteId <= 0 || $produtoId <= 0 || $quantidade <= 0) {
            $_SESSION['flash_erro'] = 'Choose a valid client, product and quantity.';
        } else {
            $produtoStmt = $pdo->prepare('SELECT name, price FROM drink WHERE id_drink = ?');
            $produtoStmt->execute([$produtoId]);
            $produto = $produtoStmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                $_SESSION['flash_erro'] = 'Product not found.';
            } else {
                $pdo->beginTransaction();
                $total = (float)$produto['price'] * $quantidade;

                $saleStmt = $pdo->prepare('INSERT INTO sale (sale_date, id_client, total) VALUES (?, ?, ?)');
                $saleStmt->execute([$data, $clienteId, $total]);
                $idSale = $pdo->lastInsertId();

                $itemStmt = $pdo->prepare('INSERT INTO sale_item (id_sale, id_drink, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)');
                $itemStmt->execute([$idSale, $produtoId, $quantidade, $produto['price'], $total]);

                $pdo->commit();
                $_SESSION['flash_mensagem'] = 'Order created successfully.';
            }
        }

        header('Location: ?paginas=comandas');
        exit;
    }

    if ($acao === 'excluir_comanda' && !empty($pdo)) {
        $id = (int)($_POST['id_sale'] ?? 0);

        if ($id > 0) {
            try {
                $pdo->beginTransaction();

                $deleteItens = $pdo->prepare('DELETE FROM sale_item WHERE id_sale = ?');
                $deleteItens->execute([$id]);

                $deleteSale = $pdo->prepare('DELETE FROM sale WHERE id_sale = ?');
                $deleteSale->execute([$id]);

                $pdo->commit();
                $_SESSION['flash_mensagem'] = 'Order removed successfully.';
            } catch (Throwable $e) {
                $pdo->rollBack();
                $_SESSION['flash_erro'] = 'It was not possible to delete this order because it is linked to registered items.';
                error_log('Delete order error: ' . $e->getMessage());
            }
        }

        header('Location: ?paginas=comandas');
        exit;
    }
}

if (!empty($pdo)) {
    $clientes = $pdo->query('SELECT * FROM client ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    $produtos = $pdo->query('SELECT d.*, c.name AS categoria FROM drink d JOIN category c ON c.id_category = d.id_category ORDER BY d.name')->fetchAll(PDO::FETCH_ASSOC);
    $comandas = $pdo->query(
        'SELECT s.id_sale, s.sale_date, c.name AS cliente, SUM(si.quantity) AS total_itens, s.total
        FROM sale s
        JOIN client c ON c.id_client = s.id_client
        LEFT JOIN sale_item si ON si.id_sale = s.id_sale
        GROUP BY s.id_sale, s.sale_date, c.name, s.total
        ORDER BY s.sale_date DESC'
    )->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="admin-page">
    <section class="admin-shell">
        <header class="admin-header">
            <div>
                <span class="login-eyebrow">Admin</span>
                <h1 class="admin-title">Orders</h1>
            </div>
            <nav class="dashboard-actions" aria-label="Ações do painel">
                <a class="dashboard-link dashboard-link-primary" href="?paginas=dashboard">Dashboard</a>
                <a class="dashboard-link dashboard-link-light" href="?paginas=produtos">Products</a>
                <a class="dashboard-link dashboard-link-light" href="?paginas=clientes">Clients</a>
                <a class="dashboard-link dashboard-link-light" href="?paginas=comandas">Orders</a>
                <a class="dashboard-link" href="?paginas=logout">Log Out</a>
            </nav>
        </header>

        <?php if (!empty($mensagem)): ?>
            <div class="admin-alert success" role="status"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="admin-alert error" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="admin-grid">
            <section class="admin-card">
                <h2>New order</h2>

                <form method="post" action="?paginas=comandas" class="admin-form">
                    <input type="hidden" name="acao" value="criar_comanda">

                    <div class="form-field">
                        <label for="id_client">Client</label>
                        <select id="id_client" name="id_client" required>
                            <option value="">Select</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= (int)$cliente['id_client'] ?>"><?= htmlspecialchars($cliente['name'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="id_drink">Product</label>
                        <select id="id_drink" name="id_drink" required>
                            <option value="">Select</option>
                            <?php foreach ($produtos as $produto): ?>
                                <option value="<?= (int)$produto['id_drink'] ?>"><?= htmlspecialchars($produto['name'], ENT_QUOTES, 'UTF-8') ?> - R$ <?= number_format((float)$produto['price'], 2, ',', '.') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="quantidade">Quantity</label>
                            <input id="quantidade" name="quantidade" type="number" min="1" value="1" required>
                        </div>

                        <div class="form-field">
                            <label for="data_abertura">Date</label>
                            <input id="data_abertura" name="data_abertura" type="datetime-local" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                    </div>

                    <div class="admin-actions-inline">
                        <button type="submit" class="primary-btn">Create order</button>
                    </div>
                </form>
            </section>

            <section class="admin-card table-card">
                <h2>Order history</h2>

                <?php if (empty($comandas)): ?>
                    <p class="empty-state">No orders registered yet.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comandas as $comanda): ?>
                                    <tr>
                                        <td>#<?= (int)$comanda['id_sale'] ?></td>
                                        <td><?= htmlspecialchars($comanda['cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string)$comanda['sale_date'])), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= (int)$comanda['total_itens'] ?></td>
                                        <td>R$ <?= number_format((float)$comanda['total'], 2, ',', '.') ?></td>
                                        <td>
                                            <form method="post" action="?paginas=comandas" onsubmit="return confirm('Delete this order?');">
                                                <input type="hidden" name="acao" value="excluir_comanda">
                                                <input type="hidden" name="id_sale" value="<?= (int)$comanda['id_sale'] ?>">
                                                <button type="submit" class="small-btn danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </section>
</main>