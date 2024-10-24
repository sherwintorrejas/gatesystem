<?php
// Include Composer autoload
require __DIR__ . '/../phpspreadsheet/vendor/autoload.php';
// Database connection
require __DIR__ . '/../Includes/dbcon.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    $file_mimes = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);

        if ('csv' == $extension) {
            $reader = IOFactory::createReader('Csv');
        } else if ('xls' == $extension) {
            $reader = IOFactory::createReader('Xls');
        } else {
            $reader = IOFactory::createReader('Xlsx');
        }

        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        for ($i = 1; $i < count($sheetData); $i++) {
            $schoolId = $sheetData[$i][0];
            $name = $sheetData[$i][1];
            $department = $sheetData[$i][2];

            // Check if the school ID already exists
            $query = mysqli_query($conn, "SELECT * FROM information WHERE schoolid = '$schoolId'");
            $ret = mysqli_fetch_array($query);

            if ($ret) {
                echo "<div class='alert alert-danger'>School ID '$schoolId' Already Exists!</div>";
            } else {
                $query = mysqli_query($conn, "INSERT INTO information (did, schoolid, name, department, category) 
                VALUES (NULL, '$schoolId', '$name', '$department', 'STAFF')");

                if ($query) {
                    echo "<div class='alert alert-success'>Record for '$name' inserted successfully!</div>";
                    header("Location: studentunre.php");
                } else {
                    echo "<div class='alert alert-danger'>An error occurred while inserting data for '$name'!</div>";
                }
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid file type!</div>";
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
        <form action="import.php" method="post" enctype="multipart/form-data">
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