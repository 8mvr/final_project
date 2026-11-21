<?php

/**
 * Saves an uploaded pet photo into ../uploads/ and returns its web path
 * (e.g. "uploads/pet_3_a1b2c3.jpg"). Returns null if no file was uploaded.
 * Throws RuntimeException on invalid/failed upload.
 */
function save_uploaded_photo()
{
    if (!isset($_FILES["photo"]) || $_FILES["photo"]["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES["photo"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Photo upload failed (error code " . $file["error"] . ").");
    }
    if ($file["size"] > 5 * 1024 * 1024) {
        throw new RuntimeException("Photo is too large (max 5 MB).");
    }

    // Verify it really is an image
    $info = @getimagesize($file["tmp_name"]);
    if ($info === false) {
        throw new RuntimeException("Uploaded file is not a valid image.");
    }

    $ext = image_type_to_extension($info[2], false); // jpeg, png, gif, webp...
    $allowed = ["jpeg", "png", "gif", "webp"];
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException("Only JPG, PNG, GIF and WEBP images are allowed.");
    }

    $dir = __DIR__ . "/../uploads";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $filename = "pet_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    if (!move_uploaded_file($file["tmp_name"], $dir . "/" . $filename)) {
        throw new RuntimeException("Could not save the uploaded photo.");
    }

    return "uploads/" . $filename;
}

/**
 * Turns a stored photo_url into something usable in <img src> from pages
 * inside userDashboard/. Local uploads ("uploads/xxx.png") get "../" prepended;
 * full URLs (http/https) are left alone. Returns null if there is no photo.
 */
function photo_src($photo_url)
{
    if ($photo_url === null || $photo_url === "") {
        return null;
    }
    if (preg_match('#^https?://#i', $photo_url)) {
        return $photo_url;
    }
    return "../" . ltrim($photo_url, "/");
}