<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';


// Fetching data from registaff table
$sql = "SELECT r.stid, r.image, rf.uid AS rfid_no, i.schoolid AS id_number, i.name, i.department, i.category 
        FROM registaff r
        JOIN information i ON r.did = i.did
        JOIN rfid rf ON r.cid = rf.cid
        LEFT JOIN blockrfd b ON r.stid = b.stid
        WHERE b.stid IS NULL";

$result = mysqli_query($conn, $sql);

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
            <h1 class="h3 mb-0 text-gray-800">Staffs</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Registered Staffs</li>
            </ol>
          </div>
          <!-- Input Group -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Registered Staffs</h6>
                </div>
                <div class="table-responsive p-3" style="max-height: 600px; overflow-y: auto;">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>ID Picture</th>
                        <th>RFID No</th>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Block</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0) {
                                            // Output data of each row
                                            while($row = mysqli_fetch_assoc($result)) {
                                              echo "<tr onclick=\"showStudentModal(
                                                '{$row['image']}', 
                                                '{$row['rfid_no']}', 
                                                '{$row['id_number']}',
                                                '{$row['name']}', 
                                                '{$row['department']}', 
                                                '{$row['category']}'
                                            )\">";
                                                echo "<td>" . $row['stid'] . "</td>";
                                                echo "<td><img src='" . $row['image'] . "' alt='ID Picture' style='width: 50px; height: auto;'></td>";
                                                echo "<td>" . $row['rfid_no'] . "</td>";
                                                echo "<td>" . $row['id_number'] . "</td>";
                                                echo "<td>" . $row['name'] . "</td>";
                                                echo "<td>" . $row['department'] . "</td>";
                                                echo "<td>" . $row['category'] . "</td>";
                                                echo "<td><a href='block_staff.php?stid=" . $row['stid'] . "' class='btn btn-danger btn-sm'>Block</a></td>";

                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>No registered staffs found.</td></tr>";
                                        } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--Row-->

      <!-- Documentation Link -->
      <!-- <div class="row">
            <div class="col-lg-12 text-center">
              <p>For more documentations you can visit<a href="https://getbootstrap.com/docs/4.3/components/forms/"
                  target="_blank">
                  bootstrap forms documentations.</a> and <a
                  href="https://getbootstrap.com/docs/4.3/components/input-group/" target="_blank">bootstrap input
                  groups documentations</a></p>
            </div>
          </div> -->
           <!-- Modal Structure -->
        <div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="studentModalLabel">Student Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img id="modalImage" src="" alt="ID Picture" style="width: 450px; height: 250px;">
                        <p><strong>RFID:</strong> <span id="modalRfid"></span></p>
                        <p><strong>Staff ID:</strong> <span id="modalid"></span></p>
                        <p><strong>Name:</strong> <span id="modalName"></span></p>
                        <p><strong>Department:</strong> <span id="modalDepartment"></span></p>
                        <p><strong>Category:</strong> <span id="modalcategory"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>                              
    </div>
    <!---Container Fluid-->
  </div>
  <!-- Footer -->
  <?php include "Includes/footer.php"; ?>
  <!-- Footer -->
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
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
    function showStudentModal(image, rfid_no, id_number, name, department, category) {
        // Populate modal with the student's info
        document.getElementById('modalImage').src = image;
        document.getElementById('modalRfid').textContent = rfid_no;
        document.getElementById('modalid').textContent = id_number;
        document.getElementById('modalName').textContent = name;
        document.getElementById('modalDepartment').textContent = department;
        document.getElementById('modalcategory').textContent = category;

        // Show the modal
        $('#studentModal').modal('show');
    
    };
  </script>
</body>

</html>