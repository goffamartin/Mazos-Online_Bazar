<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Mazoš - Tvůj online bazar</title>
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/index.css">
</head>
<body>

<header>
    <nav>
        <a id="logo" href="index.php">Mazoš.cz</a>
        <div class="navButtons">
            <?php
            // Check if the user is logged in
            if (isset($user)) {
                ?>
                <a href="index.php?my=true&show=all">Moje nabídky</a>
                <a href="offer_form.php">Nová nabídka</a>

                <span>Přihlášen: <?= $user['username'] ?></span>
                <a href="index.php?logout=true">Odhlásit se</a>
                <?php
            } // User is not logged in
            else {
                ?>
                <a href="login.php">Přihlásit se</a>
                <a href="registration.php">Registrovat se</a>
                <?php
            }
            ?>
        </div>
    </nav>
</header>
<main>
    <div class="container filters-container">
        <h3>Filtry</h3>
        <form class="form" method="get" action="<?= $_SERVER["PHP_SELF"] ?>">
            <?php if($getMyOffers):?>
                <input type="hidden" name="my" value="true">
            <?php endif;?>

            <label for="title">Hledat:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>">

            <label for="category">Kategorie:</label>
            <select id="category" name="category">
                <option value="all" <?= $category === "all" ? "selected" : "" ?>>Všechny kategorie</option>
                <?php foreach ($categories as $row): ?>
                    <option value="<?= $row['category_id'] ?>" <?= $row['category_id'] == $category ? "selected" : "" ?>><?= $row['category_name'] ?></option>
                <?php endforeach; ?>
            </select>
            <label for="price_from">Cena od:</label>
            <input type="number" id="price_from" name="price_from"
                   value="<?= htmlspecialchars($_GET['price_from'] ?? '') ?>">

            <label for="price_to">Cena do:</label>
            <input type="number" id="price_to" name="price_to"
                   value="<?= htmlspecialchars($_GET['price_to'] ?? '') ?>">

            <label for="sort">Seřadit dle:</label>
            <select id="sort" name="sort">
                <option value="created desc" <?= $sort == 'created desc' ? 'selected' : '' ?>>
                    Datum - Nejnovější
                </option>
                <option value="created asc" <?= $sort == 'created asc' ? 'selected' : '' ?>>
                    Datum - Nejstarší
                </option>
                <option value="price asc" <?= $sort == 'price asc' ? 'selected' : '' ?>>
                    Cena - Nejlevnější
                </option>
                <option value="price desc" <?= $sort == 'price desc' ? 'selected' : '' ?>>
                    Cena - Nejdražší
                </option>
            </select>
            <?php if($getMyOffers || (isset($user) && $user['isAdmin'])):?>
                <label for="show">Zobrazit nabídky:</label>
                <select id="show" name="show">
                    <option value="all" <?= $show == 'all' ? 'selected' : '' ?>>
                        Všechny
                    </option>
                    <option value="new" <?= $show == 'new' ? 'selected' : '' ?>>
                        Nové
                    </option>
                    <option value="interestShown" <?= $show == 'interestShown' ? 'selected' : '' ?>>
                        Projeven Zájem
                    </option>
                    <option value="sold" <?= $show == 'sold' ? 'selected' : '' ?>>
                        Prodané
                    </option>
                </select>
            <?php endif;?>

            <button class="primary-button" type="submit" name="submit" value="search">Vyhledat</button>
            <a href="<?php if ($getMyOffers) {
                echo $_SERVER["PHP_SELF"].'?my=true';
            } else {
                echo $_SERVER["PHP_SELF"];
            } ?>">Vymazat filtry</a>
        </form>

    </div>
    <div class="container results-container">
        <h2>Výsledky<?= $getMyOffers && isset($user) ? ' uživatele ' . htmlspecialchars($user['username']) : "" ?>:</h2>
        <span><?= !isset($totalResults) ? "Nebyly nalezeny žádné výsledky" : "" ?></span>
        <div class="results-container-items">
            <?php if (isset($results))
                foreach ($results as $offer):?>
                    <a href="<?= "./offer.php?id=" . $offer['offer_Id'] ?>">
                        <div class="offer-item-container">
                            <div class="offer-image">
                                <img src="<?= './' . $offer['image_filepath'] ?>"
                                     alt="<?= getFormData('title', $offer) ?>">
                            </div>
                            <div class="offer-details">
                                <h3 class="offer-title"><?= getFormData('title', $offer) ?></h3>
                                <p class="offer-description"><?= getFormData('description', $offer) ?></p>
                                <div class="offer-price"><?= getFormData('price', $offer) ?> Kč</div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>

        </div>
        <?php if (isset($results)) {
            for ($i = 1; $i <= $totalPages; $i++) {
                $link = "index.php?page=".$i."&title=".urlencode($title)."&category=".$category."&price_from=".$price_from."&price_to=".$price_to."&sort=".urlencode($sort);
                if ($getMyOffers)
                    echo "<a href='$link&my=true'>$i</a>";
                else
                    echo "<a href='$link'>$i</a>";
            }
        }
        ?>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>