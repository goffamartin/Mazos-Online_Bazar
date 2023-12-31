<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Mazoš - Chyba</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>

<header>
    <nav>
        <a id="logo" href="index.php">Mazoš.cz</a>
    </nav>
</header>
<main>
    <div class="container centered-container">
        <h3>Chyba</h3>
        <p> - <?= $majorError ?? "Neznámá chyba"?></p>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>