<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Mazoš.cz - Formulář nabídky</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="stylesheet" type="text/css" href="./css/offer_form.css">
    <script src="../scripts/offer-form.js"></script>
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
    <div class="container offer-form-container">
        <h1>Nabídka</h1>
        <span class="error-message"><?= getFormData('authorization', $errors); ?></span>
        <?php if (getFormData('authorization', $errors) === ""): ?>
            <!-- Product Offer Form -->
            <form class="form" method="post" action="<?= isset($data['offer_Id']) ? $_SERVER["PHP_SELF"]."?id={$data['offer_Id']}" : $_SERVER["PHP_SELF"]?>"
                  enctype="multipart/form-data">
                <span class="error-message"><?= getFormData('generic', $errors); ?></span>
                <?php if (isset($data['offer_Id'])): ?>
                    <input type="hidden" id="offerId" name="offerId" value="<?= getFormData('offer_Id', $data) ?>">
                    <input type="hidden" id="createdBy" name="createdBy" value="<?= getFormData('created_by', $data) ?>">
                    <input type="hidden" id="image_filepath" name="image_filepath" value="<?= getFormData('image_filepath', $data)?>"
                <?php endif; ?>

                <label for="title">Název:</label>
                <input id="title"
                       class="<?= ((isset($errors['title'])) ? "error" : "") ?>"
                       type="text" name="title" value="<?= getFormData('title', $data) ?>">
                <span id="titleError" class="error-message"><?= $errors['title'] ?? "" ?></span>

                <label for="image">Obrázek:</label>
                <?php if (isset($data['offer_Id'])): ?>
                    <div class="offer-image">
                        <img alt="Obrázek nabídky" src="<?= "./" . getFormData('image_filepath', $data) ?>">
                    </div>
                    <span>Nahrát nový obrázek </span>
                <?php endif; ?>

                <input type="file" id="image" name="image"
                       accept="image/png, image/jpeg">
                <span id="imageError" class="error-message"><?= getFormData('image', $errors) ?></span>

                <label for="description">Popis:</label>
                <textarea id="description"
                          class="<?= ((isset($errors['description'])) ? "error" : "") ?>"
                          name="description"><?= getFormData('description', $data) ?></textarea>
                <span id="descriptionError" class="error-message"><?= getFormData('description', $errors) ?></span>


                <label for="price">Cena v Kč:</label>
                <input type="number"
                       id="price"
                       class="<?= ((isset($errors['price'])) ? "error" : "") ?>"
                       name="price"
                       value="<?= getFormData('price', $data); ?>">
                <span id="priceError" class="error-message"><?= $errors['price'] ?? "" ?></span>


                <label for="category">Kategorie:</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $row): ?>
                        <option value="<?= $row['category_id'] ?>" <?= $row['category_id'] == getFormData('category', $data) ? "selected" : "" ?>><?= $row['category_name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <?php if (isset($data['offer_Id'])): ?>
                    <button type="submit" name="action" value="delete" class="delete-button">Smazat nabídku</button>
                <?php endif; ?>
                <?php if (!isset($data['offer_Id']) || $data['created_by'] == $user['user_Id']): ?>
                    <button type="reset" class="secondary-button">Zrušit změny</button>
                    <button type="submit" name="action" value="save"
                            class="primary-button"><?= (isset($data['offer_Id'])) ? 'Aktualizovat nabídku' : 'Vytvořit nabídku' ?>
                    </button>
                <?php endif; ?>

            </form>
        <?php endif; ?>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>