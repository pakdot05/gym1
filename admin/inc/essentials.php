<?php
session_start(); // Ensure session_start is at the very top of the file

// Define paths and URLs
define('SITE_URL', 'http://v88wgc408g08wccwg0ocwcgo.146.190.103.211.sslip.io/');
define('ABOUT_IMG_PATH', SITE_URL . 'images/about/');
define('CAROUSEL_IMG_PATH', SITE_URL . 'images/carousel/');
define('TRAINORS_IMG_PATH', SITE_URL . 'images/trainors/');

// Backend paths
define('UPLOAD_IMAGE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/gymko/images/');
define('ABOUT_FOLDER', 'about/');
define('CAROUSEL_FOLDER', 'carousel/');
define('USERS_FOLDER', 'users/');
define('TRAINORS_FOLDER', 'trainors/');

// Admin login check
function adminLogin()
{
    if (!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] === true)) {
        header('Location: index.php'); // Redirect to the login page if not logged in
        exit();
    }
}

// Redirect function (prefer header() over JavaScript for security and reliability)
function redirect($url)
{
    header("Location: $url");
    exit();
}

// Alert function to display messages
function alert($type, $msg)
{
    $bs_class = ($type === "success") ? "alert-success" : "alert-danger";
    echo <<<alert
        <div class="alert $bs_class alert-dismissible fade show custom-alert text-center" role="alert">
            <strong class="me-3">$msg</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    alert;
}

// Image upload functions
function uploadImage($image, $folder)
{
    $valid_mime = ['image/jpeg', 'image/png', 'image/webp'];
    $img_mime = $image['type'];

    // Check if the MIME type is valid
    if (!in_array($img_mime, $valid_mime)) {
        return 'inv_img'; // Invalid image MIME or format
    } else {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $rname = 'IMG_' . random_int(11111, 99999) . "." . $ext;

        $img_path = UPLOAD_IMAGE_PATH . $folder . $rname;

        // Move the uploaded file to the destination path
        if (move_uploaded_file($image['tmp_name'], $img_path)) {
            return $rname; // Image upload successful
        } else {
            return 'upd_failed'; // Image upload failed
        }
    }
}

// Image delete function
function deleteImage($image, $folder)
{
    $imagePath = UPLOAD_IMAGE_PATH . $folder . $image;
    if (file_exists($imagePath)) {
        if (unlink($imagePath)) {
            return true; // Image deletion successful
        } else {
            return false; // Failed to delete the image
        }
    }
    return false; // Image does not exist
}

// User image upload function
function uploadUserImage($image)
{
    $valid_mime = ['image/jpeg', 'image/png', 'image/webp'];
    $img_mime = $image['type'];

    // Check if the MIME type is valid
    if (!in_array($img_mime, $valid_mime)) {
        return 'inv_img'; // Invalid image MIME or format
    } else {
        $rname = 'IMG_' . random_int(11111, 99999) . ".jpeg"; // Ensure JPEG format

        $img_path = UPLOAD_IMAGE_PATH . USERS_FOLDER . $rname;

        // Move the uploaded file to the destination path
        if (move_uploaded_file($image['tmp_name'], $img_path)) {
            return $rname; // Image upload successful
        } else {
            return 'upd_failed'; // Image upload failed
        }
    }
}
?>
