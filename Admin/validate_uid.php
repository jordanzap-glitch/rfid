<?php
include '../includes/dbcon.php';

header('Content-Type: application/json');

if (isset($_GET['uid'])) {
    $uid = mysqli_real_escape_string($conn, $_GET['uid']);
    $sql = "SELECT * FROM tbl_rfid_auth WHERE uid='$uid' AND status='valid' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(["status" => "valid"]);
    } else {
        echo json_encode(["status" => "invalid"]);
    }
} else {
    echo json_encode(["status" => "error"]);
}
 