<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Query to count the number of registered students not in the blockrfd table
$query = "
    SELECT COUNT(*) as total_students 
    FROM registudent r
    WHERE NOT EXISTS (
        SELECT 1 
        FROM blockrfd b 
        WHERE r.sid = b.sid
    )
";
$result = mysqli_query($conn, $query); // Assuming $conn is your database connection variable

if ($result) {
  $row = mysqli_fetch_assoc($result);
  $totalStudents = $row['total_students']; // Fetch the count of registered students
} else {
  $totalStudents = 0; // In case of query failure
}

$query = "
    SELECT COUNT(*) as number_students 
    FROM information i
    WHERE i.category = 'student' 
    AND NOT EXISTS (
        SELECT 1 
        FROM registudent r 
        WHERE i.did = r.did
    )
";
$result = mysqli_query($conn, $query); // Assuming $conn is your database connection variable

if ($result) {
  $row = mysqli_fetch_assoc($result);
  $numberStudents = $row['number_students']; // Fetch the count of non-registered students
} else {
  $numberStudents = 0; // In case of query failure
}
$query = "
    SELECT COUNT(*) as total_staff 
    FROM registaff r
    WHERE NOT EXISTS (
        SELECT 1 
        FROM blockrfd b 
        WHERE r.stid = b.stid
    )
";
$result = mysqli_query($conn, $query); // Assuming $conn is your database connection variable

if ($result) {
  $row = mysqli_fetch_assoc($result);
  $totalStaff = $row['total_staff']; // Fetch the count of non-blocked staff
} else {
  $totalStaff = 0; // In case of query failure
}

$query = "
    SELECT COUNT(*) as number_staff 
    FROM information i
    WHERE i.category = 'staff' 
    AND NOT EXISTS (
        SELECT 1 
        FROM registaff r 
        WHERE i.did = r.did
    )
";

$result = mysqli_query($conn, $query); // Assuming $conn is your database connection variable

if ($result) {
  $row = mysqli_fetch_assoc($result);
  $numberStaff = $row['number_staff']; // Fetch the count of non-registered staff
} else {
  $numberStaff = 0; // In case of query failure
}
$query = "
    SELECT COUNT(*) AS total_rfid 
    FROM rfid r
    WHERE NOT EXISTS (
        SELECT 1 
        FROM registaff rs 
        WHERE r.cid = rs.cid
    )
    AND NOT EXISTS (
        SELECT 1 
        FROM registudent ru 
        WHERE r.cid = ru.cid
    )
";

$result = mysqli_query($conn, $query); // Assuming $conn is your database connection variable

if ($result) {
  $row = mysqli_fetch_assoc($result);
  $totalRfid = $row['total_rfid']; // Fetch the count of non-registered RFID
} else {
  $totalRfid = 0; // In case of query failure
}
$query = "SELECT COUNT(*) AS total_records FROM blockrfd";

$result = mysqli_query($conn, $query); // Execute the query

if ($result) {
  $row = mysqli_fetch_assoc($result); // Fetch the result
  $totalRecords = $row['total_records']; // Get the count of records
} else {
  $totalRecords = 0; // Handle query failure
}
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
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

  <style>
    body {
      background-image: url('img/logo/scc2.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
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
            <h1 class="h3 mb-0 text-gray-800">Adminastrator Dashboard</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
            <!-- Students Card -->

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Students Registered</div>
                      <?php echo $totalStudents; ?>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                        <span>Since last month</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Unregistered Students</div>
                      <?php echo $numberStudents; ?>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                        <span>Since last month</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-danger"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Registered Staffs</div>
                      <?php echo $totalStaff; ?>
                      <div class="mt-2 mb-0 text-muted text-xs">
                      </div>

                    </div>
                    <div class="col-auto">
                      <i class="fas fa-chalkboard-teacher fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Unregistered Staffs</div>
                      <?php echo $numberStaff; ?>
                      <div class="mt-2 mb-0 text-muted text-xs">
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-chalkboard-teacher fa-2x text-danger"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">RFID ID'S</div>
                      <?php echo $totalRfid; ?>
                      <div class="mt-2 mb-0 text-muted text-xs">
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-solid fa-address-card fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Blocked ID'S</div>
                      <?php echo $totalRecords; ?>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-solid fa-address-book fa-2x "></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script src="../vendor/chart.js/Chart.min.js"></script>
    <script src="js/demo/chart-area-demo.js"></script>
</body>

</html>