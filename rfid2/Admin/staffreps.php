<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get the date from the query string
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Construct the query
$query = "
    SELECT 
        d.lid, 
        DATE_FORMAT(d.datetime, '%d/%m/%Y %h:%i %p') AS datetime,  -- Format datetime in 12-hour format
        d.category, 
        r.uid, 
        rs.did, 
        rs.image, 
        i.name, 
        i.department 
    FROM 
        dailylogs d
    JOIN 
        registaff rs ON d.stid = rs.stid
    JOIN 
        rfid r ON rs.cid = r.cid
    JOIN 
        information i ON rs.did = i.did
";

// Add date filter if a date is specified
if ($date) {
    $query .= " WHERE DATE(d.datetime) = '$date'"; // Filter by date
}

$query .= " ORDER BY d.datetime DESC"; // Order by date descending

$result = $conn->query($query);

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
                            <li class="breadcrumb-item active" aria-current="page">Staff</li>
                        </ol>
                    </div>
                    <!-- Input Group -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Staff</h6>
                                </div>
                                <div class="table-responsive p-3" style="max-height: 600px; overflow-y: auto;">
                                <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="date">Date:</label>
                                            <input type="date" class="form-control" id="dateInput" onchange="fetchData()">
                                        </div>

                                </div>
                                <button id="exportBtn" type="button" class="btn btn-primary" style="float: right; margin-top: 20px; margin-right: 100px">Export</button>
                                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Picture</th>
                                                <th>UID</th>
                                                <th>Staff ID</th>
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>Date</th>
                                                <th>Status</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            if ($result->num_rows > 0) {
                                                $counter = 1; // Initialize counter for serial numbers
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . $counter++ . "</td>"; // Display counter
                                                    echo "<td><img src='" . $row['image'] . "' width='50' height='50'></td>";
                                                    echo "<td>" . $row['uid'] . "</td>";
                                                    echo "<td>" . $row['did'] . "</td>"; // Staff ID
                                                    echo "<td>" . $row['name'] . "</td>";
                                                    echo "<td>" . $row['department'] . "</td>";
                                                    echo "<td>" . $row['datetime'] . "</td>"; // Date
                                                    echo "<td>" . $row['category'] . "</td>"; // Status
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
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
    // Initialize both DataTables
    var table = $('#dataTable').DataTable(); // ID From dataTable 
    var tableHover = $('#dataTableHover').DataTable(); // ID From dataTable with Hover

    // Event listener for date input change
    $("#dateInput").on("change", function() {
        var selectedDate = $(this).val(); // Get the selected date

        // Clear any previous custom filters
        $.fn.dataTable.ext.search.pop(); // Remove the last custom filter

        // Check if a date is selected
        if (selectedDate) {
            // Create a custom filter function
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                // Extract the date from the row data (assumes date is in the 7th column, index 6)
                var date = data[6]; // Adjust this index if needed

                // Check if the date matches the selected date
                return date.includes(selectedDate);
            });
        }

        // Redraw the table with the applied filter
        tableHover.draw();

        // Check if there are any rows displayed
        if (tableHover.rows({ filter: 'applied' }).count() === 0) {
            // No data available for the selected date, reset filters
            // Clear the date input
            $("#dateInput").val('');
            // Optionally, you can show a message in the table
            // For example, you can display a row indicating no data available
            $('#dataTableHover tbody').html('<tr><td colspan="8" class="text-center">No data available</td></tr>');
        } else {
            // If there is data, ensure to reinitialize the original data in the table
            tableHover.rows().every(function(rowIdx) {
                // Re-add original data to the table
                var originalData = table.row(rowIdx).data();
                tableHover.row.add(originalData);
            });
            tableHover.draw(); // Re-draw the table
        }
    });
});
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
