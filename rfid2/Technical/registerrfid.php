

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
        xmlhttp.open("GET","ajaxClassArms2.php?cid="+str,true);
        xmlhttp.send();
    }
}

function openPopup() {
      // Replace 'import.php' with the URL of the popup page
      var popupUrl = 'studimport.php';
      var popupWindow = window.open(popupUrl, 'Import Files', 'width=600,height=400');
      // Check if the popup window is successfully opened
      if (!popupWindow) {
        alert('Please allow popups for this site');
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
            <h1 class="h3 mb-0 text-gray-800">Register</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Register</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Register</h6>
                </div>
                <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="id">ID:</label>
                                            <input type="text" class="form-control" id="id" name="id" placeholder="ID">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="name">Name:</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="department">Department/Course:</label>
                                            <input type="text" class="form-control" id="department" name="department"
                                                placeholder="Department/Course">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="role_year">Role/Year:</label>
                                            <input type="text" class="form-control" id="role_year" name="role_year"
                                                placeholder=" Role/Year">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="search_id">Search ID:</label>
                                            <input type="text" class="form-control" id="search_id" name="search_id"
                                                placeholder="Enter UID" autocomplete="on">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="button" id="searchButton" class="btn btn-primary"
                                                style="margin-top: 35px">Search</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="uid">UID:</label>
                                        <input type="text" class="form-control" id="uid" name="uid"
                                            placeholder="Enter UID" required style="width: 50%;">
                                    </div>
                                    <div class="col-md-6">
                                        <h2>Upload Image</h2>
                                        <form id="imageForm" action="upload.php" method="post"
                                            enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="image">Choose Image</label>
                                                <input type="file" class="form-control-file" id="image" name="image"
                                                    accept="image/*">
                                            </div>
                                            <div class="form-group">
                                                <img id="imagePreview" src="#" alt="Preview"
                                                    style="max-width: 100%; display: none;">
                                            </div>
                                        </form>
                                    </div>
                                    <button type="submit" name="register" class="btn btn-success">Register</button>
                                </form>
                            </div>
                        </div>

              <!-- Input Group -->
                 <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Student Lists</h6>
                </div>
                <form method="get">
                                                <select class="form-control" name="filter" id="dataFilter"
                                                    onchange="this.form.submit()" style="width: 50%; margin-left: 20px;">
                                                    <option value="all"
                                                        <?php if (!isset($_GET['filter']) || $_GET['filter'] == 'all') echo 'selected'; ?>>
                                                        All
                                                    </option>
                                                    <option value="student"
                                                        <?php if (isset($_GET['filter']) && $_GET['filter'] == 'student') echo 'selected'; ?>>
                                                        Students
                                                    </option>
                                                    <option value="staff"
                                                        <?php if (isset($_GET['filter']) && $_GET['filter'] == 'staff') echo 'selected'; ?>>
                                                        Staff
                                                    </option>
                                                </select>
                                            </form>
                                              <div class="table-responsive p-3">
                                                <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                                  <thead class="thead-light">
                                                  <tr>
                                                  <th>#</th>
                                                                                  <th>ID</th>
                                                                                  <th>Name</th>
                                                                                  <th>Department/Course</th>
                                                                                  <th>Role/Year</th>
                                                                                  <th>Delete</th>
                                                </tr>
                                                  </thead>
                                              
                                                  <tbody>

                                                  <?php
                                                include '../Includes/dbcon.php';

                                                $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
                                                $query = "";

                                                if ($filter == 'student') {
                                                    $query = "SELECT * FROM student_list";
                                                } elseif ($filter == 'staff') {
                                                    $query = "SELECT * FROM staff_list";
                                                } else {
                                                    $query = "SELECT * FROM student_list UNION SELECT * FROM staff_list";
                                                }

                                                $rs = $conn->query($query);
                                                $sn = 0;

                                                if ($rs->num_rows > 0) {
                                                    while ($rows = $rs->fetch_assoc()) {
                                                        $sn++;
                                                        if ($filter == 'student') {
                                                            echo "
                                                            <tr onclick='populateForm(\"" . $rows['student_id_number'] . "\", \"" . $rows['student_name'] . "\", \"" . $rows['course'] . "\", \"" . $rows['year'] . "\")'>
                                                              <td>" . $sn . "</td>
                                                              <td>" . $rows['student_id_number'] . "</td>
                                                              <td>" . $rows['student_name'] . "</td>
                                                              <td>" . $rows['course'] . "</td>
                                                              <td>" . $rows['year'] . "</td>
                                                              <td><a href='?action=delete&Id=" . $rows['student_id'] . "'><i class='fas fa-fw fa-trash'></i></a></td>
                                                            </tr>";
                                                        } elseif ($filter == 'staff') {
                                                            echo "
                                                            <tr onclick='populateForm(\"" . $rows['staff_id_number'] . "\", \"" . $rows['staff_name'] . "\", \"" . $rows['department'] . "\", \"" . $rows['role'] . "\")'>
                                                              <td>" . $sn . "</td>
                                                              <td>" . $rows['staff_id_number'] . "</td>
                                                              <td>" . $rows['staff_name'] . "</td>
                                                              <td>" . $rows['department'] . "</td>
                                                              <td>" . $rows['role'] . "</td>
                                                              <td><a href='?action=delete&Id=" . $rows['staff_id'] . "'><i class='fas fa-fw fa-trash'></i></a></td>
                                                            </tr>";
                                                        } else {
                                                            echo "
                                                            <tr onclick='populateForm(\"" . $rows['student_id_number'] . "\", \"" . $rows['student_name'] . "\", \"" . $rows['course'] . "\", \"" . $rows['year'] . "\")'>
                                                              <td>" . $sn . "</td>
                                                              <td>" . $rows['student_id_number'] . "</td>
                                                              <td>" . $rows['student_name'] . "</td>
                                                              <td>" . $rows['course'] . "</td>
                                                              <td>" . $rows['year'] . "</td>
                                                              <td><a href='?action=delete&Id=" . $rows['student_id'] . "'><i class='fas fa-fw fa-trash'></i></a></td>
                                                            </tr>";
                                                        }
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='6' class='text-center'>No records found.</td></tr>";
                                                }
                                                ?>
                    </tbody>
                  </table>
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
       <?php include "Includes/footer.php";?>
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
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });

    $(document).ready(function () {
                            $('#searchButton').on('click', function () {
                                var searchID = $('#search_id').val();
                                if (searchID) {
                                    $.ajax({
                                        url: 'search_uid.php',
                                        method: 'POST',
                                        data: { search_id: searchID },
                                        dataType: 'json',
                                        success: function (data) {
                                            if (data.length > 0) {
                                                
                                                $('#uid').val(data[0].uid);
                                            } else {
                                                alert('No data found for this UID');
                                            }
                                        }
                                    });
                                } else {
                                    alert('Please enter a UID to search');
                                }
                            });
                        });

                        function populateForm(id, name, department, role_year) {
                            $('#id').val(id);
                            $('#name').val(name);
                            $('#department').val(department);
                            $('#role_year').val(role_year);
                        }

                        const imageInput = document.getElementById('image');
                        const imagePreview = document.getElementById('imagePreview');

                        imageInput.addEventListener('change', function () {
                            const file = this.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function (event) {
                                    imagePreview.src = event.target.result;
                                    imagePreview.style.display = 'block';
                                }
                                reader.readAsDataURL(file);
                            } else {
                                imagePreview.style.display = 'none';
                            }
                        });
  </script>
</body>

</html>