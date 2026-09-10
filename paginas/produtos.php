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

$produto_edicao = null;
$categorias = [];
$produtos = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    if ($acao === 'salvar_produto' && !empty($pdo)) {
        $id = (int)($_POST['id_drink'] ?? 0);
        $nome = trim((string)($_POST['nome'] ?? ''));
        $categoria = (int)($_POST['categoria_id'] ?? 0);
        $preco = (float)($_POST['preco'] ?? 0);
        $estoque = (int)($_POST['estoque'] ?? 0);

        if ($nome === '' || $categoria <= 0) {
            $_SESSION['flash_erro'] = 'Informe nome e categoria válidos.';
        } else {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE drink SET name = ?, id_category = ?, price = ?, stock = ? WHERE id_drink = ?');
                $stmt->execute([$nome, $categoria, $preco, $estoque, $id]);
                $_SESSION['flash_mensagem'] = 'Produto atualizado com sucesso.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO drink (name, id_category, price, stock) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nome, $categoria, $preco, $estoque]);
                $_SESSION['flash_mensagem'] = 'Produto cadastrado com sucesso.';
            }
        }

        header('Location: ?paginas=produtos');
        exit;
    }

    if ($acao === 'excluir_produto' && !empty($pdo)) {
        $id = (int)($_POST['id_drink'] ?? 0);

        if ($id > 0) {
            $check = $pdo->prepare('SELECT COUNT(*) FROM sale_item WHERE id_drink = ?');
            $check->execute([$id]);

            if ((int)$check->fetchColumn() > 0) {
                $_SESSION['flash_erro'] = 'Não foi possível excluir o produto, pois ele já está em comandas registradas.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM drink WHERE id_drink = ?');
                $stmt->execute([$id]);
                $_SESSION['flash_mensagem'] = 'Produto removido com sucesso.';
            }
        }

        header('Location: ?paginas=produtos');
        exit;
    }
}

if (isset($_GET['editar']) && !empty($pdo)) {
    $id = (int)$_GET['editar'];
    $stmt = $pdo->prepare('SELECT * FROM drink WHERE id_drink = ?');
    $stmt->execute([$id]);
    $produto_edicao = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!empty($pdo)) {
    $categorias = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    $produtos = $pdo->query('SELECT d.*, c.name AS categoria FROM drink d JOIN category c ON c.id_category = d.id_category ORDER BY d.name')->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="admin-page">
    <section class="admin-shell">
        <header class="admin-header ps-0">
            <div>
                <span class="login-eyebrow">Admin</span>
                <h1 class="admin-title">Products</h1>
            </div>
            <nav class="dashboard-actions" aria-label="Ações do painel">
                <a class="dashboard-link dashboard-link-primary" href="?paginas=dashboard">Dashboard</a>
                <a class="dashboard-link dashboard-link-light" href="?paginas=produtos">Products</a>
                <a class="dashboard-link dashboard-link-light" href="?paginas=clientes">Clients</a>
                <a class="dashboard-link dashboard-link-light" href="?paginas=comandas">Orders</a>
                <a class="dashboard-link dashboard-link-dark" href="?paginas=logout">Log Out</a>
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
                <h2><?= $produto_edicao ? 'Edit product' : 'New product' ?></h2>

                <form method="post" action="?paginas=produtos" class="admin-form">
                    <input type="hidden" name="acao" value="salvar_produto">
                    <input type="hidden" name="id_drink" value="<?= htmlspecialchars((string)($produto_edicao['id_drink'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">

                    <div class="form-field">
                        <label for="nome">Name</label>
                        <input id="nome" name="nome" type="text" value="<?= htmlspecialchars((string)($produto_edicao['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="categoria_id">Category</label>
                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">Select</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int)$categoria['id_category'] ?>" <?= (($produto_edicao['id_category'] ?? 0) == $categoria['id_category']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="preco">Price</label>
                            <input id="preco" name="preco" type="number" min="0" step="0.01" value="<?= htmlspecialchars((string)($produto_edicao['price'] ?? '0.00'), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="estoque">Stock</label>
                            <input id="estoque" name="estoque" type="number" min="0" step="1" value="<?= htmlspecialchars((string)($produto_edicao['stock'] ?? 0), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>

                    <div class="admin-actions-inline">
                        <button type="submit" class="primary-btn">Save</button>
                        <?php if ($produto_edicao): ?>
                            <a href="?paginas=produtos" class="secondary-btn">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="admin-card table-card">
                <h2>Registered products</h2>

                <?php if (empty($produtos)): ?>
                    <p class="empty-state">No products registered yet.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $produto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($produto['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($produto['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>R$ <?= number_format((float)$produto['price'], 2, ',', '.') ?></td>
                                        <td><?= (int)$produto['stock'] ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="?paginas=produtos&editar=<?= (int)$produto['id_drink'] ?>" class="small-btn light">Edit</a>
                                                <form method="post" action="?paginas=produtos" onsubmit="return confirm('Do you want to delete this product?');">
                                                    <input type="hidden" name="acao" value="excluir_produto">
                                                    <input type="hidden" name="id_drink" value="<?= (int)$produto['id_drink'] ?>">
                                                    <button type="submit" class="small-btn danger">Delete</button>
                                                </form>
                                            </div>
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