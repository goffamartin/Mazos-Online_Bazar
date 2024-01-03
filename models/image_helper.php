<?php

/**
 * Handles the uploading of an image file to a specified directory.
 * It performs checks for file existence, size, and format, and provides error messages.
 *
 * @param array $image The $_FILES array containing the image file information.
 * @param array &$errors A referenced array to store error messages encountered during the upload process.
 * @return bool Returns true if the image was successfully uploaded, false otherwise.
 */
function uploadImage($image, &$errors): bool
{
    $target_dir = "./offer-images/";
    $target_file = str_replace(" ", "_", $target_dir . basename($image["name"]));
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors['image'] = "Nahrajte obrázek";
        return false;
    }

    // Check file size
    if ($image["size"] > 2097152) {
        $errors['image'] = "překročena max. velikost souboru (2MB)";
        return false;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $errors['image'] = "Nepodporovaný formát (.jpg)";
        return false;
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file))
        return true;
    $errors['image'] = 'Soubor se nepodařilo nahrát';
    return false;
}
