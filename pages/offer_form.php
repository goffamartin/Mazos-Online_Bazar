<?php
session_start();

global $conn;

include_once 'db.php';

if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit();
} else {
    $userId = $_SESSION['UserId'];
    $offerId = $_GET['id'] ?? null;

    $getUserQuery = "SELECT * FROM `User` WHERE user_Id = ?";
    $getUserStmt = $conn->prepare($getUserQuery);
    $getUserStmt->execute([$userId]);
    $user = $getUserStmt->fetch(PDO::FETCH_ASSOC);
}

$title = $description = $price = $category = $genericError = "";

// Check if offer ID is provided in the URL for editing
if ($offerId) {

    if ($user['isAdmin'] === true)
        $getOfferQuery = "SELECT * FROM `Offer` WHERE offer_Id = ?";
    else
        $getOfferQuery = "SELECT * FROM `Offer` WHERE offer_Id = ? AND created_by = ?";

    $result = $conn->prepare($getOfferQuery);
    $result->execute([$offerId, $userId]);

    if ($result->rowCount() > 0) {
        $offer = $result->fetch(PDO::FETCH_ASSOC);

        $title = $offer['title'];
        $description = $offer['description'];
        $price = $offer['price'];
        $category = $offer['category'];

    } else {
        $genericError = "Nabídka nenalezena, nebo nemáte právo na úpravu";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $categoryId = $_POST['category'];


    if (isset($_POST['action']) && $_POST['action'] === 'delete' && !empty($offerId)) {
        // Delete offer logic
        if ($user['isAdmin'] === true) {
            $deleteStmt = $conn->prepare("DELETE FROM `Offer` WHERE offer_Id = ?");
            $deleteStmt->execute([$offerId]);
        } else {
            $deleteStmt = $conn->prepare("DELETE FROM `Offer` WHERE offer_Id = ? AND created_by = ?");
            $deleteStmt->execute([$offerId, $userId]);
        }

        header("Location: ../index.php");
        exit();
    }
    if (isset($_POST['action']) && $_POST['action'] === 'save') {
        if (!empty($offerId)) {
            // Update existing offer
            $stmt = $conn->prepare("UPDATE `Offer` SET title=?, description=?, price=?, category=? WHERE offer_Id=? AND created_by=?");
            $stmt->execute([$title, $description, $price, $categoryId, $offerId, $userId]);
        } else {
            // Insert new offer
            $stmt = $conn->prepare("INSERT INTO `Offer` (title, description, price, created, created_by, category) VALUES (?, ?, ?, NOW(), ?, ?)");
            $stmt->execute([$title, $description, $price, $userId, $categoryId]);
        }
        $targetDirectory = '../offer-images';
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }


        $image = $_FILES['image'] ?? null;
        $imagePath = '';

        // Check if we are updating or creating a new offer to get the offerId
        if (empty($offerId)) {
            // Insert new offer logic here (as before) and get the last inserted ID
            $offerId = $conn->lastInsertId();
        }

        // Determine the path to save the image
        $imagePath = '../offer-images/' . $offerId;
        // Add file type validation and error handling as necessary
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            // You may want to add file extension to the image based on MIME type
            // For example, if the image is a jpeg, you should append '.jpg' to the filename
            $fileType = mime_content_type($image['tmp_name']);
            switch ($fileType) {
                case 'image/jpeg':
                    $imagePath .= '.jpg';
                    break;
                case 'image/png':
                    $imagePath .= '.png';
                    break;
                case 'image/gif':
                    $imagePath .= '.gif';
                    break;
                default:
                    die("Unsupported image type.");
            }

            // Move the uploaded file to the new location, this will overwrite an existing file with the same name
            if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
                die("Failed to save uploaded file.");
            }
        }


        // Redirect to a page after submission
        header("Location: ../index.php");
        exit();
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail</title>
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
    <div class="container">
        <h1>Nabídka</h1>
        <span class="error-message"><?php echo $genericError ?? ""; ?></span>
        <?php if ($genericError === ""): ?>
            <!-- Product Offer Form -->
            <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                  enctype="multipart/form-data">

                <?php if ($offerId): ?>
                    <input type="hidden" name="offerId" value="<?php echo htmlspecialchars($offerId); ?>">
                <?php endif;?>

                <label for="title">Název:</label>
                <input id="title" type="text" name="title" value="<?php echo $title; ?>" required>

                <label for="image">Obrázek:</label>
                <input type="file" id="image" name="image" required>

                <label for="description">Popis:</label>
                <textarea id="description" name="description"><?php echo $description; ?></textarea>

                <label for="price">Cena v Kč:</label>
                <input type="number"
                       id="price"
                       name="price"
                       value="<?php echo $price; ?>"
                       required>

                <label for="category">Kategorie:</label>
                <?php
                $getCategoriesQuery = "SELECT * FROM `Category`";
                $result = $conn->prepare($getCategoriesQuery);
                $result->execute();
                ?>
                <select id=category name="category" required>
                    <?php
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['category_id']}'>{$row['category_name']}</option>";
                    }
                    ?>
                </select>
                <br>
                <?php if (isset($offerId) && $offerId): ?>
                    <button type="submit" name="action" value="delete" class="delete-button">Smazat nabídku</button>
                <?php endif; ?>

                <div>
                    <button type="reset" class="secondary-button">Zrušit změny</button>
                    <button type="submit" name="action" value="save"
                            class="primary-button"><?php echo (isset($offerId)) ? 'Aktualizovat nabídku' : 'Vytvořit nabídku'; ?></button>
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