<?php
require 'spreadsheet/vendor/autoload.php'; // Include PhpSpreadsheet autoload.php

use PhpOffice\PhpSpreadsheet\IOFactory;

// Database connection
require 'Includes/dbcon.php';

// Function to handle importing
function importStaffList($file)
{
    global $conn;

    try {
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $sheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $sheet->getHighestRow();

        // Prepare statement
        $stmt = $conn->prepare("INSERT INTO staff_list (staff_id_number, staff_name, department, role) VALUES (?, ?, ?, ?)");

        // Loop through rows
        for ($row = 2; $row <= $highestRow; $row++) {
            $staff_id_number = $sheet->getCell('A' . $row)->getValue();
            $staff_name = $sheet->getCell('B' . $row)->getValue();
            $department = $sheet->getCell('C' . $row)->getValue();
            $role = $sheet->getCell('D' . $row)->getValue();

            // Bind parameters
            $stmt->bind_param("ssss", $staff_id_number, $staff_name, $department, $role);
            $stmt->execute();
        }

        $stmt->close();

        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["file"])) {
        $file = $_FILES["file"];

        // Import data
        if (importStaffList($file)) {
            echo "Import successful";
        } else {
            echo "Import failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import Excel to Database</title>
    <script>
        // Function to close the popup window after importing
        function closePopup() {
            window.opener.location.reload();
            window.close();
        }
    </script>
</head>
<body>
    <h2>Import Excel to Database</h2>
    <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="file" name="file" required>
        <br><br>
        <input type="submit" value="Import">
    </form>
    <br>
    <button onclick="closePopup()">Close Popup</button>
</body>
</html>
