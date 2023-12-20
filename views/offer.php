<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Mazoš.cz<?= ' - ' . getFormData("title", $offer) ?></title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="stylesheet" type="text/css" href="./css/offer.css">
    <script src="scripts/login.js"></script>
</head>
<body>
<header>
    <nav>
        <a id="logo" href="index.php">Mazoš.cz</a>
    </nav>
</header>
<main>
    <div class="container offer-container">
        <?php if (isset($offer)): ?>
            <div class="offer-image">
                <!-- Dynamically set the image source -->
                <img class="image" src="<?= $offer['image_filepath'] ?>" alt="<?= getFormData('title',$offer) ?>"/>
            </div>
            <div class="offer-info">
                <h1><?= getFormData('title',$offer) ?></h1>
                <p><?= nl2br(getFormData('description',$offer)) ?></p>
                <p>Vytvořeno: <?= $offer['created'] ?></p>
                <p>Uživatelem: <?= getFormData('username',$owner) ?></p>
                <h3><?= getFormData('price', $offer)?> Kč</h3>

                <?php if (($userIsOwner || $userIsAdmin) && !$offer['sold_to']): ?>
                    <a href="offer_form.php?id=<?= $offer['offer_Id'] ?>">Upravit nabídku</a>
                <?php elseif ($userHasShownInterest): ?>
                    <p>Projevili jste zájem.</p>
                    <form method="post">
                        <button class="delete-button" name="cancel_interest" type="submit">Zrušit zájem</button>
                    </form>
                <?php elseif (!$offer['sold_to']): ?>
                    <?php if ($user): ?>
                        <form method="post">
                            <input type="hidden" name="interestedUser" value="<?= $user['user_Id'] ?>">
                            <label for="phone"></label>
                            <input id="phone" type="text" name="phone" placeholder="Tel. číslo">
                            <p>nebo</p>
                            <label for="email"></label>
                            <input id="email" type="email" name="email" placeholder="email">
                            <button class="primary-button" name="show_interest" type="submit">Projevit zájem</button>
                        </form>
                    <?php else: ?>
                        <p>Pouze přihlášení uživatelé mohou projevit zájem </p>
                        <a href="login.php">Přihlásit se</a>
                    <?php endif; ?>
                <?php elseif(!$offer['sold']): ?>
                    <h3>Zarezervováno</h3>
                <?php else: ?>
                    <p>Prodáno: <?= $offer['sold'] ?></p>
                    <?php if (($userIsOwner || $userIsAdmin)): ?>
                        <p>Uživateli: <?= getFormData('username',$interestedUser) ?></p>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (($userIsOwner || $userIsAdmin) && $offer['sold_to'] && !$offer['sold']): ?>
                    <div>
                        <form method="post">
                            <p>Projevil zájem: <?= getFormData('username',$interestedUser) ?></p>
                            <p>Kontakt: <?= getFormData('phone',$offer) ?>
                                , <?= getFormData('email',$offer) ?></p>
                            <button class="delete-button" type="submit" name="decline_sale">Zamítnout nákup</button>
                            <button class="primary-button" type="submit" name="confirm_sale">Potvrdit nákup</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>
