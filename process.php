<?php
session_start();

require 'Database.class.php';

$database = new Database();

if (isset($_POST['status']) && isset($_POST['id'])) {
    $database->updateClaimStatus($_POST['id'], $_POST['status']);
    die();
}

$claimNumber = $database->getOSDClaimCount() + 1;

if (isset($_POST['pictures'])) {
    $pictures = json_decode($_POST['pictures'], true);
    $index = 1;
    foreach ($pictures as $key => $value) {
        list($type, $value) = explode(';', $value);
        list(, $value)      = explode(',', $value);

        $data = base64_decode($value);

        $uploads_dir = realpath(dirname(getcwd())) . '/osd/backend/uploads/' . $claimNumber;
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir);
        }

        file_put_contents($uploads_dir . '/' . $index . '.png', $data);
        $index++;
    }
}

$damageType = '';
$productReceived = isset($_POST['yes']) ? 1 : 0;
$driverId = $_SESSION['driverId'];

if (isset($_POST['overage'])) {
    $damageType .= 'O';
}

if (isset($_POST['shortage'])) {
    $damageType .= 'S';
}

if (isset($_POST['damage'])) {
    $damageType .= 'D';
}

$database->addOSDClaim($_POST['date'], $_POST['tripNumber'], $_POST['fbNumber'], $_POST['cases'], $damageType, $productReceived, $driverId);
