<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';
if (isset($_GET['action']) && $_GET['action'] == 'block' && isset($_GET['Id'])) {
  $sid = $_GET['Id'];

  // Check if the student is already blocked
  $checkQuery = "SELECT * FROM blockrfd WHERE sid = ?";
  $stmt = $conn->prepare($checkQuery);
  $stmt->bind_param("i", $sid);
  $stmt->execute();
  $resultCheck = $stmt->get_result();

  if ($resultCheck->num_rows == 0) {
      // Insert the sid into blockrfd table
      $insertQuery = "INSERT INTO blockrfd (sid) VALUES (?)";
      $stmt = $conn->prepare($insertQuery);
      $stmt->bind_param("i", $sid);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
          echo "<script>alert('Student blocked successfully.');</script>";
      } else {
          echo "<script>alert('Failed to block student. Please try again.');</script>";
      }
  } else {
      echo "<script>alert('Student is already blocked.');</script>";
  }
  $stmt->close();
}

// Fetching the data from registaff, information, and rfid tables

$query = "SELECT registudent.sid, registudent.did, registudent.cid, registudent.guardiannumber, registudent.image, 
                 information.name AS student_name, information.schoolid AS student_id, information.department AS course, 
                 rfid.uid AS rfid
          FROM registudent
          JOIN information ON registudent.did = information.did
          JOIN rfid ON registudent.cid = rfid.cid
          LEFT JOIN blockrfd ON registudent.sid = blockrfd.sid
          WHERE blockrfd.sid IS NULL";  // Only show students not in blockrfd
          
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
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp = new XMLHttpRequest();
        } else {
          // code for IE6, IE5
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("txtHint").innerHTML = this.responseText;
          }
        };
        xmlhttp.open("GET", "ajaxClassArms2.php?cid=" + str, true);
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
            <h1 class="h3 mb-0 text-gray-800">Registered Students</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Registereds Students</li>
            </ol>
          </div>
          <!-- Input Group -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Student Lists</h6>
                </div>
                <div class="table-responsive p-3" style="max-height: 600px; overflow-y: auto;">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>ID Picture</th>
                        <th>RFID:</th>
                        <th>Student ID:</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Guardian Number</th>
                        <th>Block</th>
                      </tr>
                    </thead>

                             
                      <tbody>
                      <?php
            
                      $count = 1;
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr onclick='showStudentInfo(\"" . htmlspecialchars($row['student_id']) . "\", \"" . htmlspecialchars($row['student_name']) . "\", \"" . htmlspecialchars($row['course']) . "\", \"" . htmlspecialchars($row['guardiannumber']) . "\", \"" . htmlspecialchars($row['image']) . "\", \"" . htmlspecialchars($row['rfid']) . "\")'>";
                          echo "<td>" . $count . "</td>";
                          echo "<td><img src='" . $row['image'] . "' width='50' height='50'></td>";
                          echo "<td>" . $row['rfid'] . "</td>";
                          echo "<td>" . $row['student_id'] . "</td>";
                          echo "<td>" . $row['student_name'] . "</td>";
                          echo "<td>" . $row['course'] . "</td>";
                          echo "<td>" . $row['guardiannumber'] . "</td>";
                          echo "<td><a href='?action=block&Id=" . $row['sid'] . "' onclick='return blockStudentConfirm()'>Block</a></td>";
                          echo "</tr>";
                          echo "</tr>";
                          $count++;
                      }
                      
                        ?>

                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- Modal Structure -->
              <!-- Student Info Modal -->
                <div class="modal fade" id="studentInfoModal" tabindex="-1" role="dialog" aria-labelledby="studentInfoModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="studentInfoModalLabel">Student Information</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <img id="modalImage" src="" alt="ID Picture" width="450" height="auto"><br>
                                <strong>RFID:</strong> <span id="modalRfid"></span><br>
                                <strong>Student ID:</strong> <span id="modalStudentId"></span><br>
                                <strong>Name:</strong> <span id="modalStudentName"></span><br>
                                <strong>Course:</strong> <span id="modalCourse"></span><br>
                                <strong>Guardian Number:</strong> <span id="modalGuardianNumber"></span><br>
                               
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

    function blockStudentConfirm() {
      return confirm("Are you sure you want to block this student?");
    }
    function showStudentInfo(studentId, studentName, course, guardianNumber, image, rfid) {
    document.getElementById("modalStudentId").innerText = studentId;
    document.getElementById("modalStudentName").innerText = studentName;
    document.getElementById("modalCourse").innerText = course;
    document.getElementById("modalGuardianNumber").innerText = guardianNumber;
    document.getElementById("modalRfid").innerText = rfid;
    document.getElementById("modalImage").src = image;
    
    $('#studentInfoModal').modal('show');
}

  </script>
</body>

</html>