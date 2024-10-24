<?php
// unblock.php
include '../Includes/dbcon.php'; // Database connection

// Check if stid is set
if (isset($_GET['sid'])) {
    $sid = intval($_GET['sid']); // Get the stid from the URL and ensure it is an integer

    // SQL query to delete the record from blockrfd table based on stid
    $query = "DELETE FROM blockrfd WHERE sid = $sid";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        // Redirect back to the blocked RFID list page after successful deletion
        header("Location: blockrfid.php?message=success");
        exit;
    } else {
        // If deletion fails, redirect with an error message
        header("Location: blockrfid.php?message=error");
        exit;
    }
} else {
    // If stid is not set, redirect back with an error
    header("Location: blockrfid.php?message=invalid");
    exit;
}
?>
