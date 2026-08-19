<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="imagens/coffee_maker.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Datatype:wght@100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Libre+Caslon+Text:ital,wght@0,400;0,700;1,400&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">

    <?php if (isset($css_especifico)) {
        echo '<link rel="stylesheet" href="css/' . $css_especifico . '">';
    } ?>

    <title>Dark Cafeteria</title>
</head>

<body>
    <header>
        <nav>
            <img src="imagens/logo - Editado.png" alt="logo cafeteria" class="logo">
            <b>Dark Cafeteria</b>
            <ul>
                <li><a href="?paginas=inicio">Home</a></li>
                <li><a href="?paginas=menu">Menu</a></li>
                <li><a href="?paginas=historia">Our Story</a></li>
                <li><a href="?paginas=contato">Contact</a></li>

                <button class="button">Login</button>
            </ul>
        </nav>
    </header>