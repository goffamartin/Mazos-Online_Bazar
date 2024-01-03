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
    <a id="logo" href="index.php">Mazoš.cz</a>
    <div class="myButtons">
        <?php
        // Check if the user is logged in
        if (isset($user)) {
            ?>

            <div class="navButtons">
                <a href="offer_form.php">Nová nabídka</a>

                <a href="index.php?my=true&show=all">Moje nabídky</a>
            </div>


            <div class="navButtons">
                <span>Přihlášen: <?= $user['username'] ?></span>
                <a href="index.php?logout=true">Odhlásit se</a>
            </div>

            <?php
        } // User is not logged in
        else {
            ?>
            <div class="navButtons">
                <a href="registration.php">Registrovat se</a>
                <a href="login.php">Přihlásit se</a>
            </div>
            <?php
        }
        ?>
    </div>

</header>
<main>
    <div class="container offer-container">
        <?php if (isset($offer)): ?>
            <div class="offer">
                <div class="offer-image-options">
                    <!-- Dynamically set the image source -->
                    <img class="image" src="<?= $offer['image_filepath'] ?>" alt="<?= getFormData('title', $offer) ?>">
                    <div class="offer-options">
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
                                    <input id="phone"
                                           class="<?= ((isset($errors['phone'])) ? "error" : "") ?>"
                                           type="text" name="phone" placeholder="Tel. číslo" value="<?= $phone ?? "" ?>">
                                    <span id="phoneError" class="error-message"><?= $errors['phone'] ?? "" ?></span>

                                    <p>nebo <span id="interestError" class="error-message"><?= $errors['interest'] ?? "" ?></span></p>

                                    <label for="email"></label>
                                    <input id="email"
                                           class="<?= ((isset($errors['email'])) ? "error" : "") ?>"
                                           type="email" name="email" placeholder="Email" value="<?= $email ?? "" ?>">
                                    <span id="phoneError" class="error-message"><?= $errors['email'] ?? "" ?></span>

                                    <button class="primary-button" name="show_interest" type="submit">Projevit zájem
                                    </button>
                                </form>
                            <?php else: ?>
                                <p>Pouze přihlášení uživatelé mohou projevit zájem </p>
                                <a href="login.php">Přihlásit se</a>
                            <?php endif; ?>
                        <?php elseif (!$offer['sold']): ?>
                            <h3>Zarezervováno</h3>
                        <?php else: ?>
                            <p>Prodáno: <?= $offer['sold'] ?></p>
                            <?php if (($userIsOwner || $userIsAdmin)): ?>
                                <p>Uživateli: <?= getFormData('username', $interestedUser) ?></p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (($userIsOwner || $userIsAdmin) && $offer['sold_to'] && !$offer['sold']): ?>
                            <div>
                                <form method="post">
                                    <p>Projevil zájem: <?= getFormData('username', $interestedUser) ?></p>
                                    <p>Kontakt: <?= getFormData('phone', $offer) ?>
                                        , <?= getFormData('email', $offer) ?></p>
                                    <button class="delete-button" type="submit" name="decline_sale">Zamítnout nákup</button>
                                    <button class="primary-button" type="submit" name="confirm_sale">Potvrdit nákup</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="offer-details">
                    <h1 class="offer-title"><?= getFormData('title', $offer) ?></h1>
                    <p class="offer-description"><?= nl2br(getFormData('description', $offer)) ?></p>
                    <div class="offer-info">
                        <div class="offer-created-by">
                            <p>Vytvořeno: <?= $offer['created'] ?></p>
                            <p>Uživatelem: <?= getFormData('username', $owner) ?></p>
                        </div>
                        <h2><?= getFormData('price', $offer) ?> Kč</h2>
                    </div>

                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<footer>
    <p>&copy; Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>
