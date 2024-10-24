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
                        <h1 class="h3 mb-0 text-gray-800">Daily logs</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Staffs</li>
                        </ol>
                    </div>
                    <!-- Input Group -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Staffs</h6>
                                </div>
                                <div class="table-responsive p-3" style="max-height: 600px; overflow-y: auto;"> 
                                    <button id="exportBtn" type="button" class="btn btn-primary" style="float: right; margin-top: 20px; margin-right: 100px">Export</button>
                                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>ID Picture</th>
                                                <th>UID</th>
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>Date</th>
                                                <th>Status</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php
                                            include '../Includes/dbcon.php';
                                            $sql = "SELECT d.lid, 
                                                    DATE_FORMAT(d.datetime, '%d/%m/%Y %h:%i %p') AS datetime,  -- Format datetime in 12-hour format
                                                    d.category, 
                                                    rfid.uid, 
                                                    info.name, 
                                                    info.department, 
                                                    rs.image 
                                                FROM dailylogs d
                                                LEFT JOIN registaff rs ON d.stid = rs.stid
                                                LEFT JOIN rfid ON rs.cid = rfid.cid
                                                LEFT JOIN information info ON rs.did = info.did
                                                WHERE info.category = 'staff'
                                                AND d.datetime >= NOW() - INTERVAL 7 DAY
                                                ORDER BY d.datetime DESC";


                                            $result = mysqli_query($conn, $sql);
                                            if (mysqli_num_rows($result) > 0) {
                                                $count = 1;
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $count . "</td>";
                                                    echo "<td><img src='" . $row['image'] . "' width='50' height='50'></td>";
                                                    echo "<td>" . $row['uid'] . "</td>";
                                                    echo "<td>" . $row['name'] . "</td>";
                                                    echo "<td>" . $row['department'] . "</td>";
                                                    echo "<td>" . $row['datetime'] . "</td>";
                                                    echo "<td>" . $row['category'] . "</td>";
                                                    echo "</tr>";
                                                    $count++;
                                                }
                                            } else {
                                                echo "<tr><td colspan='7'>No records found</td></tr>";
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
                    $('#dataTableHover').DataTable(); // ID From dataTable with Hover
                });

                // Updated event listener for the export button
                document.querySelector('#exportBtn').addEventListener('click', function() {
                    // Gather data from the table
                    let tableData = [];
                    const rows = document.querySelectorAll('#dataTableHover tbody tr');

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        tableData.push({
                            uid: cells[2].innerText,
                            name: cells[3].innerText,
                            department: cells[4].innerText,
                            datetime: cells[5].innerText,
                            category: cells[6].innerText
                        });
                    });

                    // Send data via AJAX
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "staffexport.php", true); // Update with the correct path
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.responseType = 'blob'; // Expecting a binary file response
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            // Create a blob link to download
                            const blob = new Blob([xhr.response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                            const link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = 'Staff_dailylogs.xlsx'; // Set default file name
                            document.body.appendChild(link);
                            link.click(); // Trigger the download
                            document.body.removeChild(link); // Cleanup
                        } else {
                            console.error('Download failed:', xhr.statusText);
                        }
                    };

                    // Prepare data to send
                    const data = new URLSearchParams();
                    data.append('data', JSON.stringify(tableData));

                    // Send the request
                    xhr.send(data);
                });
    </script>
</body>

</html>
