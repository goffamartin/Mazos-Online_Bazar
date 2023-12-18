<?php
session_start();

$dbConnectionError = "";

include './php/DB_Connector.php';
$conn = DB_Connector::Connect();
if ($conn === null) {
    $dbConnectionError = "Nepodařilo se připojit k Databázi";
}

if (isset($_SESSION['UserId'])) {
    $userId = $_SESSION['UserId'];
    $getUserQuery = "SELECT * FROM `User` WHERE user_Id = ?";
    $getUserStmt = $conn->prepare($getUserQuery);
    $getUserStmt->execute([$userId]);
    $user = $getUserStmt->fetch(PDO::FETCH_ASSOC);
}

$getMyOffers = $_GET['my'] ?? false;
$title = $_GET['title'] ?? "";
$category = $_GET['category'] ?? "all";
$price_from = $_GET['price_from'] ?? "";
$price_to = $_GET['price_to'] ?? "";
$sort = $_GET['sort'] ?? 'created desc';

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // Number of results per page
$offset = ($page - 1) * $perPage;

// Start the query
$query = "SELECT * FROM `Offer` WHERE 1";

// Add conditions based on filters
$params = [];
if ($title != "") {
    $query .= " AND `title` LIKE ?";
    $params[] = '%'.$title.'%';
}
if ($category !== "all") {
    $query .= " AND `category` = ?";
    $params[] = $category;
}

if ($price_from !== "") {
    $query .= " AND `price` >= ?";
    $params[] = $price_from;
}
if ($price_to !== "") {
    $query .= " AND `price` <= ?";
    $params[] = $price_to;
}

if ($getMyOffers && isset($user)) {
    $getMyOffers = true;
    $query .= " AND `created_by` = ?";
    $params[] = $user['user_Id'];
}

// Add sorting
$query .= " ORDER BY $sort";

// Add pagination
$query .= " LIMIT " . $perPage . " OFFSET " . intval($offset);

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$totalResults = $stmt->rowCount();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = ceil($totalResults / $perPage);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Mazoš - Tvůj online bazar</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
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
                <a href="index.php?my=true">Moje nabídky</a>
                <a href="pages/offer_form.php">Nová nabídka</a>
                <?php
            } else {
                // User is not logged in
                ?>
                <!-- Display "Login" and "Register" buttons -->
                <a href="pages/login.php">Přihlásit se</a>
                <a href="pages/registration.php">Registrovat se</a>
                <?php
            }
            ?>
        </div>
    </nav>
</header>
<main>
    <span class="error-message"><?= $dbConnectionError ?></span>
    <div class="container filters-container">
        <h3>Filtry</h3>
        <form class="form" method="get" action="<?= $_SERVER["PHP_SELF"] ?>">
            <label for="title">Hledat:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title)?>">

            <label for="category">Kategorie:</label>
            <?php
            $getCategoriesQuery = "SELECT * FROM `Category`";
            $result = $conn->prepare($getCategoriesQuery);
            $result->execute();
            $currentCategory = $_GET['category'] ?? 'all';
            ?>
            <select id="category" name="category">
                <option value="all" <?= $currentCategory === 'all' ? 'selected' : '' ?>>Všechny kategorie
                </option>
                <?php
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $selected = $row['category_id'] == $currentCategory ? 'selected' : '';
                    echo "<option value='{$row['category_id']}' $selected>{$row['category_name']}</option>";
                }
                ?>
            </select>
            <label for="price_from">Cena od:</label>
            <input type="number" id="price_from" name="price_from"
                   value="<?= htmlspecialchars($_GET['price_from'] ?? '') ?>">

            <label for="price_to">Cena do:</label>
            <input type="number" id="price_to" name="price_to"
                   value="<?= htmlspecialchars($_GET['price_to'] ?? '') ?>">

            <label for="sort">Seřadit dle:</label>
            <?php $currentSort = $_GET['sort'] ?? ''; ?>
            <select id="sort" name="sort">
                <option value="created desc" <?= $currentSort == 'created desc' ? 'selected' : '' ?>>
                    Datum - Nejnovější
                </option>
                <option value="created asc" <?= $currentSort == 'created asc' ? 'selected' : '' ?>>
                    Datum - Nejstarší
                </option>
                <option value="price asc" <?= $currentSort == 'price asc' ? 'selected' : '' ?>>
                    Cena - Nejlevnější
                </option>
                <option value="price desc" <?= $currentSort == 'price desc' ? 'selected' : '' ?>>
                    Cena - Nejdražší
                </option>
            </select>
            <button class="primary-button" type="submit" name="submit" value="search">Vyhledat</button>
            <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">Vymazat filtry</a>
        </form>

    </div>
    <div class="container results-container">
        <h2>Výsledky<?= $getMyOffers && isset($user) ? htmlspecialchars(' uživatele ' . $user['username']) : "" ?>:</h2>
        <span><?= $totalResults <= 0 ? "Nebyly nalezeny žádné výsledky" : ""; ?></span>
        <div class="results-container-items">
            <?php foreach ($results as $offer): ?>
                <a href="<?= "pages/offer-detail?id=" . $offer['offer_Id'] ?>">
                    <div class="offer-container">
                        <div class="offer-image">
                            <img src="<?= $offer['image_filepath'] ?>"
                                 alt="<?= $offer['title'] ?>">
                        </div>
                        <div class="offer-details">
                            <h3 class="offer-title"><?= htmlspecialchars($offer['title']) ?></h3>
                            <p class="offer-description"><?= htmlspecialchars($offer['description']) ?></p>
                            <div class="offer-price"><?= htmlspecialchars($offer['price']) ?> Kč</div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

        </div>
        <?php for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='index.php?page=$i&category=$category&price_from=$price_from&price_to=$price_to&sort=$sort'>$i</a>";
        }
        ?>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>
