<?php

$pagina = isset($_GET["paginas"]) ? $_GET["paginas"] : "inicio";

$css_especifico = $pagina . ".css";

// require_once "config/database.php";

include "templates/header.php";

$rotas = [
    "inicio" => "paginas/inicio.php",
    "menu" => "paginas/menu.php",
    "historia" => "paginas/historia.php",
    "contato" => "paginas/contato.php"
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    echo "<main><h1 style='text-align:center; padding:50px;'>Página não encontrada</h1></main>";
}

include "templates/footer.php";
