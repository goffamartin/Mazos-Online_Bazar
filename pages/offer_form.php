<?php
session_start();

include '../php/DB_Connector.php';
$conn = DB_Connector::Connect();

if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit();
} else {
    $userId = $_SESSION['UserId'];

    $getUserQuery = "SELECT * FROM `User` WHERE user_Id = ?";
    $getUserStmt = $conn->prepare($getUserQuery);
    $getUserStmt->execute([$userId]);
    $user = $getUserStmt->fetch(PDO::FETCH_ASSOC);
}

$offerId = $_GET['id'] ?? null;
$title = $description = $price = $categoryId = $genericError = $imageError = "";

// Check if offer ID is provided in the URL for editing
if (isset($offerId)) {

    if ($user['isAdmin'] === true)
        $getOfferQuery = "SELECT * FROM `Offer` WHERE offer_Id = ?";
    else
        $getOfferQuery = "SELECT * FROM `Offer` WHERE offer_Id = ? AND created_by = ?";

    $getOfferStmt = $conn->prepare($getOfferQuery);
    $getOfferStmt->execute([$offerId, $userId]);

    if ($getOfferStmt->rowCount() > 0) {
        $offer = $getOfferStmt->fetch(PDO::FETCH_ASSOC);

        $title = $offer['title'];
        $description = $offer['description'];
        $price = $offer['price'];
        $categoryId = $offer['category'];

    } else {
        $genericError = "Nabídka nenalezena, nebo nemáte právo na úpravu";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['offerId']))
        $offerId = $_POST['offerId'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $categoryId = $_POST['category'];

    if (isset($_POST['action']) && $_POST['action'] === 'save') {

        $canSaveToDB = $newImage = false;

        if (!isset($offerId) || $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

            $targetDirectory = '../offer-images';
            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0777, true);
            }

            $target_dir = "../offer-images/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageUploadOk = true;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is an actual image or fake image
            if (isset($_POST["submit"])) {
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check !== false) {
                    $imageUploadOk = true;
                } else {
                    $imageError = "Soubor není obrázek";
                    $imageUploadOk = false;
                }
            }
// Check file size
            if ($_FILES["image"]["size"] > 2097152) {
                $imageError = "překročena max. velikost souboru (2MB)";
                $imageUploadOk = false;
            }


// Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $imageError = "Nepodporovaný formát (.jpg)";
                $imageUploadOk = false;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($imageUploadOk === true) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file))
                    $canSaveToDB = true;
                    $newImage = true;
            }
        }
        else{
            $canSaveToDB = true;
        }


        if ($canSaveToDB === true) {
            if (!empty($offerId)) {
                // Update existing offer
                if($newImage) {
                    $stmt = $conn->prepare("UPDATE `Offer` SET title=?, description=?, price=?, category=?, image_filepath=? WHERE offer_Id=? AND created_by=?");
                    $stmt->execute([$title, $description, $price, $categoryId, "offer-images/" . basename($_FILES["image"]["name"]), $offerId, $userId]);
                } else {
                    $stmt = $conn->prepare("UPDATE `Offer` SET title=?, description=?, price=?, category=? WHERE offer_Id=? AND created_by=?");
                    $stmt->execute([$title, $description, $price, $categoryId, $offerId, $userId]);
                }
            } else {
                // Insert new offer
                $stmt = $conn->prepare("INSERT INTO `Offer` (title, description, price, created, created_by, category, image_filepath) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
                $stmt->execute([$title, $description, $price, $userId, $categoryId, "offer-images/" . basename($_FILES["image"]["name"])]);
            }
            // Redirect to a page after submission
            header("Location: ../index.php");
            exit();
        } else {
            $genericError = "Chyba při ukládání";
        }
    }
    if ($_POST['action'] === 'delete' && isset($offer)) {
        // Delete offer logic
        $imagePath = '../' . $offer['image_filepath'];

        if ($user['isAdmin'] === true) {
            $deleteStmt = $conn->prepare("DELETE FROM `Offer` WHERE offer_Id = ?");
            $deleteStmt->execute([$offer['offer_Id']]);
        } else {
            $deleteStmt = $conn->prepare("DELETE FROM `Offer` WHERE offer_Id = ? AND created_by = ?");
            $deleteStmt->execute([$offer['offer_Id'], $user['user_Id']]);
        }

        if (file_exists($imagePath))
            if (unlink($imagePath))
                header("Location: ../index.php");
        exit();
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mazoš.cz - Formulář nabídky</title>
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <script src="../scripts/offer-form.js"></script>
</head>
<body>
<header>
    <nav>
        <a id="logo" href="../index.php">Mazoš.cz</a>
    </nav>
</header>
<main>
    <div class="container offer-form-container">
        <h1>Nabídka</h1>
        <span class="error-message"><?php echo $genericError ?? ""; ?></span>
        <?php if ($genericError !== "Nabídka nenalezena, nebo nemáte právo na úpravu"): ?>
            <!-- Product Offer Form -->
            <form class="form" method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                  enctype="multipart/form-data">

                <?php if (isset($offer)): ?>
                    <input type="hidden" name="offerId" value="<?= htmlspecialchars($offer['offer_Id']); ?>">
                <?php endif; ?>

                <label for="title">Název:</label>
                <input id="title" type="text" name="title" value="<?= $title; ?>" required>

                <label for="image">Obrázek:</label>
                <?php if (isset($offer)): ?>
                    <div class="offer-image">
                        <img alt="Obrázek nabídky" src="<?= htmlspecialchars("../" . $offer['image_filepath']) ?>">
                    </div>
                    <span>Nahrát nový obrázek </span>
                <?php endif; ?>

                <input type="file" id="image" name="image"
                       accept="image/png, image/jpeg" <?= isset($offer) ? "" : "required"; ?>>
                <span id="imageError" class="error-message"><?= $imageError ?></span>

                <label for="description">Popis:</label>
                <textarea id="description" name="description"><?= htmlspecialchars($description); ?></textarea>

                <label for="price">Cena v Kč:</label>
                <input type="number"
                       max="2147483647"
                       min="0"
                       id="price"
                       name="price"
                       value="<?= htmlspecialchars($price); ?>"
                       required>

                <label for="category">Kategorie:</label>
                <?php
                $getCategoriesQuery = "SELECT * FROM `Category`";
                $getOfferStmt = $conn->prepare($getCategoriesQuery);
                $getOfferStmt->execute();
                ?>
                <select id=category name="category" required>
                    <?php
                    while ($row = $getOfferStmt->fetch(PDO::FETCH_ASSOC)):?>
                        <option value='<?= $row['category_id'] ?>' <?= $categoryId === $row['category_id'] ? "selected" : "" ?>><?= $row['category_name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <br>
                <?php if (isset($offerId) && $offerId): ?>
                    <button type="submit" name="action" value="delete" class="delete-button">Smazat nabídku</button>
                <?php endif; ?>

                <div>
                    <button type="reset" class="secondary-button">Zrušit změny</button>
                    <button type="submit" name="action" value="save"
                            class="primary-button"><?php echo (isset($offerId)) ? 'Aktualizovat nabídku' : 'Vytvořit nabídku'; ?>
                    </button>
                </div>

            </form>
        <?php endif; ?>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>