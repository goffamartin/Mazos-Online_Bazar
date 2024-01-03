<?php
session_start();

include './models/db_helper.php';
include './models/form_helper.php';

$db = new db_helper();
if (($majorError = $db->Connect()) !== null){
    include './views/error.php';
    die();
}

// Logout logic
if (isset($_GET['logout'])) {
    // clear all the $_SESSION variables
    $_SESSION = array();
    session_destroy();
    // Redirect to the homepage or login page
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['UserId'])) {
    $user = $db->GetUser($_SESSION['UserId']);
}

$categories = $db->GetAllCategories();

$perPage = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$title = $_GET['title'] ?? "";
$category = $_GET['category'] ?? "all";
$price_from = $_GET['price_from'] ?? "";
$price_to = $_GET['price_to'] ?? "";
$sort = $_GET['sort'] ?? 'created desc';
$show = $_GET['show'] ?? 'all';

$getMyOffers = $_GET['my'] ?? false;

if($getMyOffers || (isset($user) && $user['isAdmin'])){
    $all = $show === 'all';
    $new = $show === 'new';
    $sold = $show === 'sold';
    $interestShown = $show === 'interestShown';
}


if ($getMyOffers && !isset($user)) {
    header("Location: login.php");
    exit();
}

$results = $db->GetFilteredOffers($perPage, $page, $title, $category, $price_from, $price_to, $sort, $getMyOffers, $user ?? null, $all ?? false, $new ?? true, $interestShown ?? false, $sold ?? false);

if (isset($results)) {
    $totalResults = $db->GetFilteredOffersCount($title, $category, $price_from, $price_to, $sort, $getMyOffers, $user ?? null, $all ?? false, $new ?? true, $interestShown ?? false, $sold ?? false);
    $totalPages = ceil($totalResults / $perPage);

}
include "./views/index.php";




