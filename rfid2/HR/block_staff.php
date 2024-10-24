<?php
include '../Includes/dbcon.php'; // Include the database connection
include '../Includes/session.php'; // Include session for user data (if needed)

// Check if the 'stid' is passed via the URL
if (isset($_GET['stid'])) {
    $stid = $_GET['stid'];

    // Insert the staff ID into the blockrfd table
    $sql = "INSERT INTO blockrfd (stid) VALUES ('$stid')";

    if (mysqli_query($conn, $sql)) {
        // If insertion is successful, redirect back to the registered staffs page
        header("Location: createClassTeacher.php");
    } else {
        // If there is an error with the query, display it
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    // If 'stid' is not set in the URL, redirect back to the registered staffs page
    header("Location: createClassTeacher.php?error=1");
}
?>
