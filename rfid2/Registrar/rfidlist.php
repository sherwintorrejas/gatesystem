<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Execute the Python script
// $pythonScriptPath = 'c:/xampp/htdocs/finalsystem/rfid2/Admin/send_uid.py';
// $command = escapeshellcmd("C:/Users/huawei/AppData/Local/Programs/Python/Python312/python.exe \"$pythonScriptPath\"");

$pythonScriptPath = 'd:/xampp/htdocs/finalsystem/rfid2/Registrar/send_uid.py';
$command = escapeshellcmd("C:/Users/User/AppData/Local/Programs/Python/Python312/python.exe \"$pythonScriptPath\"");
$output = null; // To capture any output from the Python script
$return_var = null; // To capture the return status of the executed command

exec($command, $output, $return_var); // Execute the command

if ($return_var !== 0) {
  // Handle error if the script failed to run
  error_log("Python script failed with status: $return_var");
}

// Pagination setup
$results_per_page = isset($_GET['results']) ? $_GET['results'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Query to fetch data from the RFID table
$sql = "
SELECT * 
FROM rfid
ORDER BY timedate DESC 
LIMIT $offset, $results_per_page";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/logo.jpg" rel="icon">
  <?php include 'includes/title.php'; ?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .table-container {
      max-height: 300px;
      overflow-y: auto;
    }

    .pagination-links {
      position: fixed;
      width: 100%;
      bottom: 0;
      left: 0;
      background-color: #f8f9fc;
      border-top: 1px solid #dee2e6;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 400px;
      text-align: center;
      position: relative;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    .done {
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
    }

    .done:hover {
      background-color: #45a049;
    }
  </style>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php"; ?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php"; ?>
        <!-- Topbar -->
        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">RFID List</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">RFID List</li>
            </ol>
          </div>
          <!-- Input Group -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">RFID List</h6>
                </div>
                <div class="table-responsive p-3" style="max-height: 600px; overflow-y: auto;">
                  <div class="card-body">

                    <!-- <button type="button" id="addrfidBtn" class="btn btn-success">Add RFid</button> -->
                    <br>

                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                      <thead class="thead-light">
                        <tr>
                          <th>#</th>
                          <th>UID</th>
                          <th>Timestamp</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        // Fetch and display data from the database
                        while ($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>" . $row['cid'] . "</td>"; // Display the 'cid' value
                          echo "<td>" . $row['uid'] . "</td>"; // Display the 'uid' value
                          echo "<td>" . $row['timedate'] . "</td>"; // Display the 'timedate' value
                          echo "</tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <?php include "Includes/footer.php"; ?>
      </div>
    </div>

    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
      $(document).ready(function() {
        $('#dataTableHover').DataTable();

        let lastFetchedCid = 0; // Initialize last fetched cid

        // Function to fetch new data from the database
        function fetchNewData() {
          $.ajax({
            url: 'fetch_data.php', // URL to fetch the data
            method: 'GET',
            dataType: 'json',
            success: function(data) {
              let tbodyContent = '';
              let newDataFound = false;
              let newDataNames = [];
              if (data.length > 0) {
                data.forEach(function(row) {
                  // If we find a new cid, set the newDataFound to true
                  if (row.cid > lastFetchedCid) {
                    newDataFound = true;
                    lastFetchedCid = row.cid; // Update the last fetched cid
                    newDataNames.push(row.uid);
                  }
                  tbodyContent += '<tr>';
                  tbodyContent += '<td>' + row.cid + '</td>'; // Display the 'cid' value
                  tbodyContent += '<td>' + row.uid + '</td>'; // Display the 'uid' value
                  tbodyContent += '<td>' + row.timedate + '</td>'; // Display the 'timedate' value
                  tbodyContent += '</tr>';
                });

                // Update the tbody with the new data
                $('#dataTableHover tbody').html(tbodyContent);

                // Alert if new data is found
                if (newDataFound) {
                  alert(newDataNames.join(', ') + ' has been added ');
                }
              }
            },
            error: function(xhr, status, error) {
              console.error('Error fetching data: ', error);
            }
          });
        }

        // Check for new data every 5 seconds
        setInterval(fetchNewData, 1000);
      });
    </script>
  </div>
</body>

</html>