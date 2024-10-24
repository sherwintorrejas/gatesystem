<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get search parameters
$dateSelected = isset($_POST['date']) ? $_POST['date'] : ''; // Empty string if no date is selected
$nameSearch = isset($_POST['name']) ? $_POST['name'] : ''; // Empty string if no name is provided

/// Prepare the query dynamically based on input
$query = "
SELECT r.stid, i.name, i.department,
       MIN(CASE WHEN d.category = 'IN' THEN DATE_FORMAT(TIME(d.datetime), '%h:%i %p') END) AS time_in,  -- Format IN time
       MAX(CASE WHEN d.category = 'OUT' THEN DATE_FORMAT(TIME(d.datetime), '%h:%i %p') END) AS time_out, -- Format OUT time
       DATE_FORMAT(DATE(d.datetime), '%d/%m/%Y') AS log_date  -- Format log_date to dd/mm/yyyy
FROM dailylogs d
JOIN registaff r ON d.stid = r.stid
JOIN information i ON r.did = i.did
WHERE 1 = 1"; // 1 = 1 for flexible appending of conditions

// If a date is selected, filter by date
if ($dateSelected) {
$query .= " AND DATE(d.datetime) = '$dateSelected'";
}

// If a name is searched, filter by name
if ($nameSearch) {
$query .= " AND i.name LIKE '%$nameSearch%'";
}

// Group by staff and log date, to get first IN and last OUT for each day
$query .= " GROUP BY r.stid, i.name, i.department, DATE(d.datetime)
        ORDER BY r.stid ASC, DATE(d.datetime) DESC";

$result = mysqli_query($conn, $query);

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
    <?php include 'includes/title.php';?>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <script>
        function classArmDropdown(str) {
            if (str == "") {
                document.getElementById("txtHint").innerHTML = "";
                return;
            } else {
                if (window.XMLHttpRequest) {
                    xmlhttp = new XMLHttpRequest();
                } else {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("txtHint").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "ajaxClassArms.php?cid=" + str, true);
                xmlhttp.send();
            }
        }
    </script>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "Includes/sidebar.php";?>
        <!-- Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php";?>
                <!-- Topbar -->
                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Reports </h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Attendance </li>
                        </ol>
                    </div>
                    <!-- Input Group -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Attendance </h6>
                                </div>
                                <div class="table-responsive p-3" style="max-height: 600px; overflow-y: auto;">
                                <div class="row mb-3">
                                
                                </div>
                                <form method="POST" action="" style="margin-left: 15px;">
                                    <div class="form-group" style="float:left; margin-right: 10px;" >
                                        <label for="name">Search Attendance:</label>
                                        <input type="text" id="name" name="name" value="<?php echo $nameSearch; ?>" class="form-control" placeholder="Enter name"  style="max-width: 200px;">
                                    </div>
                                    <div class="form-group">
                                        <label for="date">Select Date:</label>
                                        <input type="date" id="date" name="date" value="<?php echo $dateSelected; ?>" class="form-control" style="max-width: 20%; ">
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="margin-bottom: 10px;">Search</button>
                                </form>
                                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>time in</th>
                                                <th>time out</th>
                                                <th>Date</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                             <?php
                                            $count = 1;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $time_in = $row['time_in'] ?? '-';
                                                $time_out = $row['time_out'] ?? '-';
                                                $log_date = $row['log_date'];
                                                echo "<tr>
                                                    <td>{$count}</td>
                                                    <td>{$row['name']}</td>
                                                    <td>{$row['department']}</td>
                                                    <td>{$time_in}</td>
                                                    <td>{$time_out}</td>
                                                    <td>{$log_date}</td>
                                                </tr>";
                                                $count++;
                                            }
                                            ?>
                                                
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!---Container Fluid-->
          
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
            $('#dataTable').DataTable(); // ID From dataTable 
            $('#dataTableHover').DataTable(); // ID From dataTable with Hover
        });
    </script>
</body>

</html>
