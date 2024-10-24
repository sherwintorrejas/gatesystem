<?php
// Include PHPExcel classes
require __DIR__ . '/../phpspreadsheet/vendor/autoload.php'; // Adjust the path based on your project structure

// Include database connection and session files
include __DIR__ . '/../Includes/dbcon.php';
include __DIR__ . '/../Includes/session.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if form is submitted
if (isset($_FILES['file'])) {
  // Retrieve file details
  $file = $_FILES['file']['tmp_name'];
  $fileName = $_FILES['file']['name'];
  $fileType = $_FILES['file']['type'];

  // Check if uploaded file is a valid Excel file
  if ($fileType == 'application/vnd.ms-excel' || $fileType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
    try {
      // Load spreadsheet file
      $spreadsheet = IOFactory::load($file);

      // Get the first worksheet
      $worksheet = $spreadsheet->getActiveSheet();

      // Initialize an array to store SQL queries
      $sqlValues = [];

      // Iterate through each row of the worksheet (assuming data starts from row 2)
      foreach ($worksheet->getRowIterator(2) as $row) {
        // Get cell values
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Loop through all cells, even if they're empty
        $rowData = [];
        foreach ($cellIterator as $cell) {
          $rowData[] = $cell->getValue();
        }

        // Sanitize data
        $school_id = mysqli_real_escape_string($conn, $rowData[0]);
        $name = mysqli_real_escape_string($conn, $rowData[1]);
        $department = mysqli_real_escape_string($conn, $rowData[2]);

        // Prepare SQL query
        $sqlValues[] = "('NULL', '$school_id', '$name', '$department', 'STUDENT')";
      }

      // If there are rows to insert, construct the SQL query and execute
      if (!empty($sqlValues)) {
        $sql = "INSERT INTO information (did, schoolid, name, department, category) VALUES " . implode(",", $sqlValues);
        if ($conn->query($sql) === TRUE) {
          echo "<script>alert('Data imported successfully.');</script>";
        } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
        }
      }

      // Close spreadsheet
      $spreadsheet->disconnectWorksheets();
      unset($spreadsheet);
      echo "<script>alert('Data imported successfully.');</script>";
      // Redirect to the page where you display student lists
      header("Location: studentunre.php");
     

      exit();
    } catch (Exception $e) {
      echo 'Error loading file: ', $e->getMessage();
    }
  } else {
    echo "<script>alert('Invalid file format. Please upload an Excel file.');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Import Excel File</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <h2 class="mt-5">Import Data from Excel</h2>
    <form action="studimport.php" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="file">Select Excel File</label>
        <input type="file" name="file" class="form-control" id="file" required>
      </div>
      <button type="submit" name="import" class="btn btn-primary">Import</button>
    </form>
  </div>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>