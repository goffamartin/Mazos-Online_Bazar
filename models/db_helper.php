<?php

/**
 * Database helper class for managing database operations.
 */
class db_helper
{
    /**
     * Database hostname.
     */
    private const HOSTNAME = "localhost";
    /**
     * Database name.
     */
    private const DATABASE = "MazosDB";
    /**
     * Database username.
     */
    private const USERNAME = "goffamar";
    /**
     * Database password.
     */
    private const PASSWORD = "webove aplikace";

    /**
     * @var PDO|null Connection to the database.
     */
    private $conn;

    /**
     * Connect to the database using PDO.
     *
     * @return string|null Returns null on success, or the error message on failure.
     */
    public function Connect(): ?string
    {
        try {
            // Create a PDO connection
            $this->conn = new PDO("mysql:host=" . self::HOSTNAME . ";dbname=" . self::DATABASE, self::USERNAME, self::PASSWORD);

            // Set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return null;
        } catch (PDOException $e) {
            return ("Připojení selhalo: " . $e->getMessage());
        }
    }

    /**
     * Insert a new user into the database.
     *
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     * @return bool Returns true on success, or false on failure.
     */
    public function InsertUser($username, $password): bool
    {
        try {
            $userExists = $this->getUserByUsername($username);

            if (!isset($userExists)) {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $this->conn->prepare("INSERT INTO `User` (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $hashedPassword]);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }

    /**
     * Get a user by user ID.
     *
     * @param int $userId The ID of the user.
     * @return array|null Returns an associative array of user data or null if not found.
     */
    public function GetUser($userId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `User` WHERE `user_Id` = ?");
            $stmt->execute([$userId]);
            if ($stmt->rowCount() === 1) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (PDOException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Get a user by username.
     *
     * @param string $username The username of the user.
     * @return array|null Returns an associative array of user data or null if not found.
     */
    public function GetUserByUsername($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `User` WHERE `username` = ?");
            $stmt->execute([$username]);
            if ($stmt->rowCount() === 1) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (PDOException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Get an offer by offer ID.
     *
     * @param int $offerId The ID of the offer.
     * @return array|null Returns an associative array of offer data or null if not found.
     */
    public function GetOffer($offerId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `Offer` WHERE `offer_Id` = ?");
            $stmt->execute([$offerId]);

            if ($stmt->rowCount() === 1) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (PDOException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Get an offer that a user can edit.
     *
     * @param int $offerId The ID of the offer.
     * @param int $userId The ID of the user.
     * @return array|null Returns an associative array of offer data or null if not found or not editable.
     */
    public function GetOfferToEdit($offerId, $userId)
    {
        try {
            // Gets the User according to ID.
            $user = $this->GetUser($userId);
            // Admin can Delete offers of others.
            if ($user['isAdmin']){
                $query = "SELECT * FROM `Offer` WHERE `offer_Id` = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$offerId]);
            } else{
                $query = "SELECT * FROM `Offer` WHERE `offer_Id` = ? AND `created_by` = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$offerId, $userId]);
            }

            if ($stmt->rowCount() === 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if($result['sold']){
                    return null;
                }
                return $result;
            }
            return null;
        } catch (PDOException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Get a count of offers based on various filters.
     *
     * @param string $title The title to filter by.
     * @param string $category The category to filter by.
     * @param float $price_from The minimum price to filter by.
     * @param float $price_to The maximum price to filter by.
     * @param string $sort The sort order.
     * @param bool $getMyOffers Whether to get offers created by the user.
     * @param array $user The user array containing user details.
     * @param bool $all Whether to get all offers regardless of status.
     * @param bool $new Whether to get only new offers.
     * @param bool $interestShown Whether to get offers with interest shown.
     * @param bool $sold Whether to get only sold offers.
     * @return int|null Returns the count of offers or null on failure.
     */
    public function GetFilteredOffersCount($title, $category, $price_from, $price_to, $sort, $getMyOffers, $user, $all, $new, $interestShown, $sold)
    {
        try {
            // Start the query
            $query = "SELECT * FROM `Offer` WHERE 1";

            // Add conditions based on filters
            $params = [];
            if ($title != "") {
                $query .= " AND title LIKE ?";
                $params[] = '%' . $title . '%';
            }
            if ($category !== "all") {
                $query .= " AND category = ?";
                $params[] = $category;
            }

            if ($price_from !== "") {
                $query .= " AND price >= ?";
                $params[] = $price_from;
            }
            if ($price_to !== "") {
                $query .= " AND price <= ?";
                $params[] = $price_to;
            }

            if ($getMyOffers && isset($user)) {
                $query .= " AND created_by = ?";
                $params[] = $user['user_Id'];
            }

            if(!$all){
                if($sold){
                    $query .= " AND sold IS NOT NULL";
                }

                if($interestShown){
                    $query .= " AND sold_to IS NOT NULL";
                    $query .= " AND sold is NULL";
                }
                if($new){
                    $query .= " AND sold_to IS NULL";
                    $query .= " AND sold is NULL";
                }
            }


            // Add sorting
            $query .= " ORDER BY $sort";


            // Prepare and execute the query
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Get a list of filtered offers.
     *
     * @param int $perPage Number of offers per page.
     * @param int $page Current page number.
     * @param string $title Filter by title.
     * @param string $category Filter by category.
     * @param float $price_from Filter by minimum price.
     * @param float $price_to Filter by maximum price.
     * @param string $sort Sort order.
     * @param bool $getMyOffers Whether to get offers by the user.
     * @param array $user User details.
     * @param bool $all Whether to get all offers.
     * @param bool $new Whether to get new offers.
     * @param bool $interestShown Whether to get offers with interest shown.
     * @param bool $sold Whether to get sold offers.
     * @return array|null Returns an array of offers or null on failure.
     */
    public function GetFilteredOffers($perPage, $page, $title, $category, $price_from, $price_to, $sort, $getMyOffers, $user, $all, $new, $interestShown, $sold)
    {
        try {
            // Start the query
            $query = "SELECT * FROM `Offer` WHERE 1";

            // Add conditions based on filters
            $params = [];
            if ($title != "") {
                $query .= " AND title LIKE ?";
                $params[] = '%' . $title . '%';
            }
            if ($category !== "all") {
                $query .= " AND category = ?";
                $params[] = $category;
            }

            if ($price_from !== "") {
                $query .= " AND price >= ?";
                $params[] = $price_from;
            }
            if ($price_to !== "") {
                $query .= " AND price <= ?";
                $params[] = $price_to;
            }

            if ($getMyOffers && isset($user)) {
                $query .= " AND created_by = ?";
                $params[] = $user['user_Id'];
            }

            if(!$all){
                if($sold){
                    $query .= " AND sold IS NOT NULL";
                }

                if($interestShown){
                    $query .= " AND sold_to IS NOT NULL";
                    $query .= " AND sold is NULL";
                }
                if($new){
                    $query .= " AND sold_to IS NULL";
                    $query .= " AND sold is NULL";
                }
            }


            // Add sorting
            $query .= " ORDER BY $sort";

            // Add pagination
            $offset = ($page - 1) * $perPage;

            $query .= " LIMIT " . $perPage . " OFFSET " . intval($offset);

            // Prepare and execute the query
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            if ($stmt->rowCount() > 0)
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            return null;
        } catch (PDOException $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Insert or update an offer in the database.
     *
     * @param string $title The title of the offer.
     * @param string $description The description of the offer.
     * @param float $price The price of the offer.
     * @param int $categoryId The category ID of the offer.
     * @param int $userId The user ID of the person creating the offer.
     * @param string|null $imageFilePath The file path of the offer image.
     * @param int|null $offerId The ID of the offer to update; pass null to create a new offer.
     * @return bool Returns true on success, or false on failure.
     */
    public function InsertOrUpdateOffer($title, $description, $price, $categoryId, $userId, $imageFilePath = null, $offerId = null)
    {
        try {
            if (isset($offerId)) {
                // Update existing offer
                if (isset($imageFilePath)) {
                    $stmt = $this->conn->prepare("UPDATE `Offer` SET title=?, description=?, price=?, created=NOW(), category=?, image_filepath=? WHERE offer_Id=? AND created_by=?");
                    $success = $stmt->execute([$title, $description, $price, $categoryId, $imageFilePath, $offerId, $userId]);
                } else {
                    $stmt = $this->conn->prepare("UPDATE `Offer` SET title=?, description=?, price=?, created=NOW(), category=? WHERE offer_Id=? AND created_by=?");
                    $success = $stmt->execute([$title, $description, $price, $categoryId, $offerId, $userId]);
                }
            } else {
                // Insert new offer
                $stmt = $this->conn->prepare("INSERT INTO `Offer` (title, description, price, created, created_by, category, image_filepath) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
                $success = $stmt->execute([$title, $description, $price, $userId, $categoryId, $imageFilePath]);
            }
            return $success;
        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }

    /**
     * Delete an offer from the database.
     *
     * @param int $offerId The ID of the offer.
     * @param int $userId The ID of the user attempting the delete.
     * @return bool Returns true on success, or false on failure.
     */
    public function DeleteOffer($offerId, $userId)
    {
        try {
            // Gets the User according to ID.
            $user = $this->GetUser($userId);
            // Admin can delete offers of others.
            if ($user['isAdmin'] === true) {
                $stmt = $this->conn->prepare("DELETE FROM `Offer` WHERE offer_Id = ?");
                $success = $stmt->execute([$offerId]);
            } else {
                $stmt = $this->conn->prepare("DELETE FROM `Offer` WHERE offer_Id = ? AND created_by = ?");
                $success = $stmt->execute([$offerId, $user['user_Id']]);
            }
            return $success;
        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }

    /**
     * Update the 'sold to' information for an offer.
     *
     * @param int $offerId The ID of the offer.
     * @param int $userId The ID of the user who is buying.
     * @param string $email The email of the buyer.
     * @param string $phone The phone number of the buyer.
     * @return bool Returns true on success, or false on failure.
     */
    public function UpdateOffer_Sold_To($offerId, $userId, $email, $phone)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE `Offer` SET sold_to=?, phone=?, email=? WHERE offer_Id=?");
            return $stmt->execute([$userId, $phone, $email, $offerId]);
        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }

    /**
     * Mark an offer as sold.
     *
     * @param int $offerId The ID of the offer to mark as sold.
     * @return bool Returns true on success, or false on failure.
     */
    public function UpdateOffer_Sold($offerId)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE `Offer` SET sold=NOW() WHERE offer_Id=?");
            return $stmt->execute([$offerId]);
        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }

    /**
     * Get all categories from the database.
     *
     * @return array|null Returns an array of categories or null on failure.
     */
    public function GetAllCategories()
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `Category`");
            $stmt->execute();
            if ($stmt->rowCount() > 0)
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            return null;

        } catch (PDOException $e) {
            echo $e;
            return null;
        }
    }
}