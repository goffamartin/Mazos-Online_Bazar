<?php
session_start();

include './models/db_helper.php';
include './models/form_helper.php';
include './models/image_helper.php';

$db = new db_helper();
if (($majorError = $db->Connect()) !== null) {
    include './views/error.php';
    die();
}

if (isset($_SESSION['UserId'])) {
    $user = $db->GetUser($_SESSION['UserId']);
} else {
    header("Location: login.php");
    exit();
}
$errors = array();
$data = array();
$categories = $db->GetAllCategories();

// Form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'save') {

        if (!isset($_POST['title']) || trim($_POST['title']) == "")
            $errors['title'] = "Vyplňte název nabídky";
        else if (strlen($_POST['title']) > 100) {
            $errors['title'] = "Název je moc dlouhý max. 100 znaků";
        }
        if (isset($_POST['description'])){
            $maxLines = 50;
            $description = $_POST['description'];
            $lines = explode("\n", $description);

            if (count($lines) > $maxLines) {
                $errors['description'] = "popis je moc dlouhý max. 50 řádků";
            }
        }

        if (!isset($_POST['price']))
            $errors['price'] = "Vyplňte cenu";
        else
            if ($_POST['price'] > 2147483647 || $_POST['price'] < 0)
                $errors['price'] = "cena min. 0 kč max. 2147483647 kč)";

        if (!isset($_POST['offerId']) && $_FILES['image']['error'] == UPLOAD_ERR_NO_FILE)
            $error['image'] = "Nahrajte obrázek";

        if (!isset($_POST['offerId']) || $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image = $_FILES['image'];
            $imageFilePath = str_replace(" ", "_", "offer-images/" . basename($image["name"]));
            $canSaveToDB = uploadImage($image, $errors);
        }

        if (empty($errors)) {
            $success = $db->InsertOrUpdateOffer($_POST['title'], $_POST['description'], $_POST['price'], $_POST['category'], $user['user_Id'], $imageFilePath ?? null, $_POST['offerId'] ?? null);
            // Redirect to a page after submission
            if ($success) {
                header("Location: ./index.php?my=true");
                exit();
            }
        } else {
            if(!isset($_POST['offerId']))
            {
                $data = array(
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'price' => $_POST['price'],
                    'category' => $_POST['category']
                );
            }
            else{
                $data = array(
                    'offer_Id' => $_POST['offerId'],
                    'created_by' => $_POST['createdBy'],
                    'image_filepath' => $_POST['image_filepath'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'price' => $_POST['price'],
                    'category' => $_POST['category']
                );
            }
        }
    }
    if ($_POST['action'] === 'delete' && isset($_POST['offerId'])) {

        $success = $db->DeleteOffer($_POST['offerId'], $user['user_Id']);

        header("Location: ./index.php");
        exit();
    }
} // Form is loaded for the first time
else {
    if (isset($_GET['id'])) {
        if (($data = $db->GetOfferToEdit($_GET['id'], $user['user_Id'])) == null) {
            $errors['authorization'] = "Nabídka (už) neexistuje nebo nemáte práva na uprávu";
        }
        // Offer data was successfully loaded
    } else {
        // Default values
        $data = array(
            'title' => "",
            'image_filepath' => "",
            'description' => "",
            'price' => "",
            'category' => 0,
        );
    }

}
include './views/offer_form.php';