<?php

session_start();

$pagina = isset($_GET["paginas"]) ? $_GET["paginas"] : "inicio";
$adminPages = ["dashboard", "produtos", "clientes", "comandas"];

if (in_array($pagina, $adminPages, true) && empty($_SESSION['usuario_autenticado'])) {
    header('Location: ?paginas=login');
    exit;
}

if ($pagina === 'logout') {
    $_SESSION = [];
    session_destroy();
    header('Location: ?paginas=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pagina === 'login') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === 'admin@darkcafeteria.com' && $senha === 'admin123') {
        $_SESSION['usuario_autenticado'] = true;
        $_SESSION['usuario_email'] = $email;
        header('Location: index.php?paginas=dashboard');
        exit;
    }

    $erro_login = 'E-mail ou senha inválidos.';
}

$css_especifico = in_array($pagina, ["produtos", "clientes", "comandas"], true)
    ? "admin.css"
    : $pagina . ".css";

include "templates/header.php";

$rotas = [
    "inicio" => "paginas/inicio.php",
    "menu" => "paginas/menu.php",
    "historia" => "paginas/historia.php",
    "contato" => "paginas/contato.php",
    "login" => "paginas/login.php",
    "dashboard" => "paginas/dashboard.php",
    "produtos" => "paginas/produtos.php",
    "clientes" => "paginas/clientes.php",
    "comandas" => "paginas/comandas.php"
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    echo "<main><h1 style='text-align:center; padding:50px;'>Página não encontrada</h1></main>";
}

include "templates/footer.php";
