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

$cliente_edicao = null;
$clientes = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    if ($acao === 'salvar_cliente' && !empty($pdo)) {
        $id = (int)($_POST['id_client'] ?? 0);
        $nome = trim((string)($_POST['nome'] ?? ''));
        $telefone = trim((string)($_POST['telefone'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));

        if ($nome === '') {
            $_SESSION['flash_erro'] = 'Inform the client name.';
        } else {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE client SET name = ?, phone = ?, email = ? WHERE id_client = ?');
                $stmt->execute([$nome, $telefone, $email, $id]);
                $_SESSION['flash_mensagem'] = 'Client updated successfully.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO client (name, phone, email) VALUES (?, ?, ?)');
                $stmt->execute([$nome, $telefone, $email]);
                $_SESSION['flash_mensagem'] = 'Client registered successfully.';
            }
        }

        header('Location: ?paginas=clientes');
        exit;
    }

    if ($acao === 'excluir_cliente' && !empty($pdo)) {
        $id = (int)($_POST['id_client'] ?? 0);

        if ($id > 0) {
            $check = $pdo->prepare('SELECT COUNT(*) FROM sale WHERE id_client = ?');
            $check->execute([$id]);

            if ((int)$check->fetchColumn() > 0) {
                $_SESSION['flash_erro'] = 'This client has purchase history and cannot be removed.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM client WHERE id_client = ?');
                $stmt->execute([$id]);
                $_SESSION['flash_mensagem'] = 'Client removed successfully.';
            }
        }

        header('Location: ?paginas=clientes');
        exit;
    }
}

if (isset($_GET['editar']) && !empty($pdo)) {
    $id = (int)$_GET['editar'];
    $stmt = $pdo->prepare('SELECT * FROM client WHERE id_client = ?');
    $stmt->execute([$id]);
    $cliente_edicao = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!empty($pdo)) {
    $clientes = $pdo->query('SELECT * FROM client ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="admin-page">
    <section class="admin-shell">
        <header class="admin-header">
            <div>
                <span class="login-eyebrow">Admin</span>
                <h1 class="admin-title">Clients</h1>
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
                <h2><?= $cliente_edicao ? 'Edit client' : 'New client' ?></h2>

                <form method="post" action="?paginas=clientes" class="admin-form">
                    <input type="hidden" name="acao" value="salvar_cliente">
                    <input type="hidden" name="id_client" value="<?= htmlspecialchars((string)($cliente_edicao['id_client'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">

                    <div class="form-field">
                        <label for="nome">Name</label>
                        <input id="nome" name="nome" type="text" value="<?= htmlspecialchars((string)($cliente_edicao['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="telefone">Phone</label>
                        <input id="telefone" name="telefone" type="text" value="<?= htmlspecialchars((string)($cliente_edicao['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="form-field">
                        <label for="email">E-mail</label>
                        <input id="email" name="email" type="email" value="<?= htmlspecialchars((string)($cliente_edicao['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="admin-actions-inline">
                        <button type="submit" class="primary-btn">Save</button>
                        <?php if ($cliente_edicao): ?>
                            <a href="?paginas=clientes" class="secondary-btn">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="admin-card table-card">
                <h2>Registered clients</h2>

                <?php if (empty($clientes)): ?>
                    <p class="empty-state">No clients registered yet.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>E-mail</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($cliente['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($cliente['phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($cliente['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="?paginas=clientes&editar=<?= (int)$cliente['id_client'] ?>" class="small-btn light">Edit</a>
                                                <form method="post" action="?paginas=clientes" onsubmit="return confirm('Delete this client?');">
                                                    <input type="hidden" name="acao" value="excluir_cliente">
                                                    <input type="hidden" name="id_client" value="<?= (int)$cliente['id_client'] ?>">
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