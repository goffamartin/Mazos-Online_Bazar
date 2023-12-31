<?php
session_start();

include './models/db_helper.php';
include './models/form_helper.php';

$db = new db_helper();
if (($majorError = $db->Connect()) !== null){
    include './views/error.php';
    die();
}

$user = null;
$offer = null;
$interestedUser = null;

$userIsOwner = false;
$userIsAdmin = false;
$userHasShownInterest = false;


if (isset($_SESSION['UserId'])) {
    $user = $db->GetUser($_SESSION['UserId']);
    if ($user && $user['isAdmin'])
        $userIsAdmin = true;
}
if (isset($_GET['id'])) {
    $offer = $db->GetOffer($_GET['id']);
    if ($offer) {
        $owner = $db->GetUser($offer['created_by']);
        if ($user && ($user['user_Id'] === $owner['user_Id'])) {
            $userIsOwner = true;
        }

        // Check if the logged-in user has shown interest
        if ($user && $user['user_Id'] == $offer['sold_to']) {
            $userHasShownInterest = true;
        }
        if ($offer['sold_to']) {
            $interestedUser = $db->GetUser($offer['sold_to']);
        }
    }
}

// Handle form submission for showing interest
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_interest']) && !$offer['sold_to']) {
    $interestedUser = $_POST['interestedUser'];
    $phone = $_POST['phone'] ?? null;
    $email = $_POST['email'] ?? null;

    $db->UpdateOffer_Sold_To($offer['offer_Id'], (int)$interestedUser, $email, $phone);
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $offer['offer_Id']);
}

// Handle form submission for cancelling interest or cancelling from the owner side
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['cancel_interest']) || isset($_POST['decline_sale'])) && !$offer['sold']) {
    // ... logic to reset the offer's sold_to, phone, email ...
    $db->UpdateOffer_Sold_To($offer['offer_Id'], null, null, null);
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $offer['offer_Id']);
}

// Handle form submission for confirming sale by owner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_sale']) && $userIsOwner && !$offer['sold']) {
    // ... logic to confirm the sale and update the 'sold' column ...
    $db->UpdateOffer_Sold($offer['offer_Id']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $offer['offer_Id']);
}

include './views/offer.php';
