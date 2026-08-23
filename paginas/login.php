<?php if (isset($_SESSION['usuario_autenticado'])): ?>
    <script>
        window.location.replace('index.php?paginas=dashboard');
    </script>
<?php endif; ?>

<main class="login-page">
    <section class="login-panel" aria-labelledby="login-title">
        <div class="login-copy">
            <span class="login-eyebrow">Dark Cafeteria</span>
            <h1 id="login-title">Bem-vindo de volta.</h1>
            <p>Acesse o painel para acompanhar sua cafeteria.</p>
        </div>

        <form class="login-form" method="post" action="?paginas=login">
            <?php if (!empty($erro_login)): ?>
                <p class="login-error" role="alert"><?= htmlspecialchars($erro_login, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <div class="form-field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" autocomplete="username" required value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-field">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" autocomplete="current-password" required>
            </div>

            <button type="submit">Entrar no painel</button>
        </form>
    </section>
</main>