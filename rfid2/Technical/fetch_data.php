<?php
include '../Includes/dbcon.php';

header('Content-Type: application/json');

// Query to fetch the latest data from the RFID table
$sql = "SELECT * FROM rfid ORDER BY timedate DESC";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
