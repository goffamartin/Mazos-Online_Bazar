<?php

function uploadImage($image, &$errors): bool
{
    $target_dir = "./offer-images/";
    $target_file = str_replace(" ","_",$target_dir . basename($image["name"]));
    $imageUploadOk = true;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size
    if ($image["size"] > 2097152) {
        $errors['image'] = "překročena max. velikost souboru (2MB)";
        $imageUploadOk = false;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $errors['image'] = "Nepodporovaný formát (.jpg)";
        $imageUploadOk = false;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($imageUploadOk === true) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file))
            return true;
        $errors['image'] = 'Soubor se nepodařilo nahrát';
        return false;
    }
    return false;
}
