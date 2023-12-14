<?php
session_start();
global $conn;
include_once 'pages/db.php';

if (isset($_SESSION['UserId'])) {
    $userId = $_SESSION['UserId'];
    $getUserQuery = "SELECT * FROM `User` WHERE user_Id = ?";
    $getUserStmt = $conn->prepare($getUserQuery);
    $getUserStmt->execute([$userId]);
    $user = $getUserStmt->fetch(PDO::FETCH_ASSOC);
}

$getMyOffers = $_GET['my'] ?? false;
$category = $_GET['category'] ?? "all";
$price_from = $_GET['price_from'] ?? null;
$price_to = $_GET['price_to'] ?? null;
$sort = $_GET['sort'] ?? 'created desc';

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // Number of results per page
$offset = ($page - 1) * $perPage;

// Start the query
$query = "SELECT * FROM `Offer` WHERE 1";

// Add conditions based on filters
$params = [];
if ($category && $category !== "all") {
    $query .= " AND `category` = ?";
    $params[] = $category;
}
if ($price_from) {
    $query .= " AND `price` >= ?";
    $params[] = $price_from;
}
if ($price_to) {
    $query .= " AND `price` <= ?";
    $params[] = $price_to;
}

if(isset($_GET['my']) && $_GET['my'] = true){
    $query .= " AND `created_by` = ?";
    $params[] = $userId;
}

// Add sorting
$query .= " ORDER BY $sort";

// Add pagination
$query .= " LIMIT " . intval($perPage) . " OFFSET " . intval($offset);

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
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>

<header>
    <nav>
        <a id="logo" href="index.php">Mazoš.cz</a>
        <div class="navButtons">
            <?php
            // Check if the user is logged in
            if (isset($_SESSION['UserId'])) {
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
    <div class="container">
        <h3>Filtry</h3>
        <form class="form" method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div>
                <label for="category">Kategorie:</label>
                <?php
                $getCategoriesQuery = "SELECT * FROM `Category`";
                $result = $conn->prepare($getCategoriesQuery);
                $result->execute();
                $currentCategory = $_GET['category'] ?? 'all';
                ?>
                <select id="category" name="category">
                    <option value="all" <?php echo $currentCategory == 'all' ? 'selected' : ''; ?>>Všechny kategorie
                    </option>
                    <?php
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $selected = $row['category_id'] == $currentCategory ? 'selected' : '';
                        echo "<option value='{$row['category_id']}' $selected>{$row['category_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="price_from">Cena od:</label>
                <input type="number" id="price_from" name="price_from"
                       value="<?php echo htmlspecialchars($_GET['price_from'] ?? ''); ?>">
            </div>
            <div>
                <label for="price_to">Cena do:</label>
                <input type="number" id="price_to" name="price_to"
                       value="<?php echo htmlspecialchars($_GET['price_to'] ?? ''); ?>">
            </div>
            <div>
                <label for="sort">Seřadit dle:</label>
                <?php $currentSort = $_GET['sort'] ?? ''; ?>
                <select id="sort" name="sort">
                    <option value="created desc" <?php echo $currentSort == 'created desc' ? 'selected' : ''; ?>>
                        Datum - Nejnovější
                    </option>
                    <option value="created asc" <?php echo $currentSort == 'created asc' ? 'selected' : ''; ?>>
                        Datum - Nejstarší
                    </option>
                    <option value="price asc" <?php echo $currentSort == 'price asc' ? 'selected' : ''; ?>>
                        Cena - Nejlevnější
                    </option>
                    <option value="price desc" <?php echo $currentSort == 'price desc' ? 'selected' : ''; ?>>
                        Cena - Nejdražší
                    </option>
                </select>
            </div>
            <button class="primary-button" type="submit" name="submit" value="search">Vyhledat</button>
            <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">Vymazat filtry</a>
        </form>

    </div>
    <div class="container" id="results-container">
        <h2>Výsledky<?php echo isset($_GET['my']) ? htmlspecialchars(' uživatele '.$user['username']) : ''?>:</h2>
        <?php foreach ($results as $offer): ?>
            <div class="container">
                <div class="offer-image">
                    <img src="<?= htmlspecialchars("offer-images/" . $offer['offer_Id']) ?>" alt="Offer Image">
                </div>
                <div class="offer-details">
                    <h2 class="offer-title"><?= htmlspecialchars($offer['title']) ?></h2>
                    <p class="offer-description"><?= htmlspecialchars($offer['description']) ?></p>
                    <div class="offer-price"><?= htmlspecialchars($offer['price']) ?> Kč</div>
                </div>
            </div>
        <?php endforeach;
        // Generate pagination links
        for ($i = 1; $i <= $totalPages; $i++) {

            echo "<a href='index.php?page=$i&category=$category&price_from=$price_from&price_to=$price_to&sort=$sort'>$i</a> ";
        }
        ?>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>
