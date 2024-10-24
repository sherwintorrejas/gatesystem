<?php
include '../Includes/dbcon.php';

if (isset($_POST['search_id'])) {
    $search_id = $_POST['search_id'];
    $sql = "SELECT * FROM rfid WHERE uid LIKE '%$search_id%'";
    $result = $conn->query($sql);
    $data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode($data);
}
?>
