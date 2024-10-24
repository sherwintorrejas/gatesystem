<?php
// Remove error suppression to debug potential issues
// error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']); // School ID
    $uid = mysqli_real_escape_string($conn, $_POST['uid']); // UID
    $guardiannumber = mysqli_real_escape_string($conn, $_POST['guardiannumber']); // Guardian Number
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
    $selectStudentQuery = "SELECT did FROM information WHERE schoolid = '$id' AND category = 'student'";
    $resultStudent = mysqli_query($conn, $selectStudentQuery);
    if ($resultStudent && mysqli_num_rows($resultStudent) > 0) {
        $rowStudent = mysqli_fetch_assoc($resultStudent);
        $studentDid = $rowStudent['did'];

        // Fetching cid from rfid table
        $selectRfidQuery = "SELECT cid FROM rfid WHERE uid = '$uid'";
        $resultRfid = mysqli_query($conn, $selectRfidQuery);
        if ($resultRfid && mysqli_num_rows($resultRfid) > 0) {
            $rowRfid = mysqli_fetch_assoc($resultRfid);
            $rfidCid = $rowRfid['cid'];

            // Inserting into registudent table
            $insertRegisteredQuery = "INSERT INTO registudent (sid, did, cid, guardiannumber, image) 
                                     VALUES ('$studentDid', '$studentDid', '$rfidCid', '$guardiannumber', '$idPicture')";
            if (mysqli_query($conn, $insertRegisteredQuery)) {
                // Redirect back to studentunre.php
                echo "<script>window.location = 'studentunre.php';</script>";
                echo "<script>alert('Student registered successfully');</script>";
                exit; // Ensure script execution stops after redirection
            } else {
                echo "Error inserting into registudent table: " . mysqli_error($conn);
            }
        } else {
            echo "Error fetching cid from rfid table: " . mysqli_error($conn);
        }
    } else {
        echo "Error fetching did from information table: " . mysqli_error($conn);
    }
}
