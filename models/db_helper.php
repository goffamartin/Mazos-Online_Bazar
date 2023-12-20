<?php

class db_helper
{
    private string $DbHost = "localhost";
    private string $DbName = "MazosDB";
    private string $DbUser = "goffamar";
    private string $DbPass = "webove aplikace";

    private $conn;

    public function Connect(): void
    {
        try {
            // Create a PDO connection
            $this->conn = new PDO("mysql:host=" . $this->DbHost . ";dbname=" . $this->DbName, $this->DbUser, $this->DbPass);

            // Set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

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