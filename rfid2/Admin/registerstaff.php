<?php
// Remove error suppression to debug potential issues
// error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']); // School ID
    $uid = mysqli_real_escape_string($conn, $_POST['uid']); // UID
    $idPicture = ''; // Default value

    // Handling file upload
    if ($_FILES['image']['size'] > 0) {
        $targetDir = "../../idpic/";
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $idPicture = $targetFilePath;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG, GIF files are allowed.";
            exit;
        }
    }

    // Fetching did from information table
    $selectStaffQuery = "SELECT did FROM information WHERE schoolid = '$id' AND category = 'staff'";
    $resultStaff = mysqli_query($conn, $selectStaffQuery);
    if ($resultStaff && mysqli_num_rows($resultStaff) > 0) {
        $rowStaff = mysqli_fetch_assoc($resultStaff);
        $staffDid = $rowStaff['did'];

        // Fetching cid from rfid table
        $selectRfidQuery = "SELECT cid FROM rfid WHERE uid = '$uid'";
        $resultRfid = mysqli_query($conn, $selectRfidQuery);
        if ($resultRfid && mysqli_num_rows($resultRfid) > 0) {
            $rowRfid = mysqli_fetch_assoc($resultRfid);
            $rfidCid = $rowRfid['cid'];

            // Inserting into registaff table
            $insertRegisteredQuery = "INSERT INTO registaff (stid, did, cid, image) 
                                     VALUES ('$staffDid', '$staffDid', '$rfidCid', '$idPicture')";
            if (mysqli_query($conn, $insertRegisteredQuery)) {
                // Redirect back to staffsunre.php
                echo "<script>window.location = 'staffsunre.php';</script>";
                echo "<script>alert('Staff registered successfully');</script>";
                exit; // Ensure script execution stops after redirection
            } else {
                echo "Error inserting into registaff table: " . mysqli_error($conn);
            }
        } else {
            echo "Error fetching cid from rfid table: " . mysqli_error($conn);
        }
    } else {
        echo "Error fetching did from information table: " . mysqli_error($conn);
    }
}
