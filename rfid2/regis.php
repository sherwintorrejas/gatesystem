<?php
 require_once 'db_connect.php';


if (isset($_POST['register'])) {
    $rfid = $_POST['rfid'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $userType = $_POST['userType'];

    // Fetch cid from rfid table
    $sql = "SELECT cid FROM rfid WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cid = $row['cid'];

        // Fetch did based on name (or rfid)
        $sql = "SELECT did FROM information WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $did = $row['did'];

            // Insert into admin table
            $sql = "INSERT INTO admin (stid, password, category) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $cid, $password, $userType);
            if ($stmt->execute()) {
                echo "Registration successful!";
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Name not found.";
        }
    } else {
        echo "RFID not found.";
    }

    $stmt->close();
}
$conn->close();
?>
