
<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

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
        xmlhttp.open("GET","ajaxClassArms.php?cid="+str,true);
        xmlhttp.send();
    }
}
function openPopup() {
  var popupUrl = 'import.php';
  var windowFeatures = 'width=600,height=400,menubar=no,toolbar=no,location=no,status=no';

  var left = (screen.width - 600) / 2;
  var top = (screen.height - 400) / 2;

  var popupWindow = window.open(popupUrl, 'Import Files', windowFeatures + ',left=' + left + ',top=' + top);

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
            <h1 class="h3 mb-0 text-gray-800">Staffs</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Unregistered Staffs</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">import Staff</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                <form method="post" enctype="multipart/form-data" action="registerstaff.php">
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
                                            <label for="department">Department</label>
                                            <input type="text" class="form-control" id="department" name="department"
                                                placeholder="Department/Course">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="category">Category:</label>
                                            <input type="text" class="form-control" id="category" name="category" placeholder="Category">
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
                                                style="margin-top: 35px;  ">Search</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="uid">UID:</label>
                                        <input type="text" class="form-control" id="uid" name="uid"
                                            placeholder="Enter UID" required style="width: 25%;">
                                    </div>
                                    <div class="col-md-6" style="float: right; position: absolute; top: 150px; right: 20px; margin-top: 10px;">
                                      
                                            <div class="form-group">
                                                <label for="image">Choose Image</label>
                                                <input type="file" class="form-control-file" id="image" name="image"
                                                    accept="image/*">
                                            </div>
                                            <div class="form-group"">
                                                <img id="imagePreview" src="#" alt="Preview"
                                                style="max-width: 250px; display: none; height: 150px;">
                                                </div>
                                        
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
                    <h6 class="m-0 font-weight-bold text-primary">Unregistered Staffs</h6>
                  </div>
                  <div class="table-responsive p-3" style="max-height: 400px; overflow-y: auto;">
                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <form method="post">     
                    <button type="button" onclick="openPopup()" class="btn btn-primary" style="    float: right;
    margin-top: 20px; margin-right: 100px">Import</button>              
                    </form>  
                    <thead class="thead-light">
                        <tr>
                        <th>#</th>
                        <th>Staff ID Number</th>
                        <th>Staff Name</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Delete</th>


                        </tr>
                      </thead>
                    
                      <tbody>
                      <?php
                            // Fetch staff information from the database
                            $query =  "
                            SELECT i.*
                            FROM information i
                            LEFT JOIN registaff r ON i.did = r.did
                            WHERE i.category = 'STAFF' AND r.stid IS NULL
                        ";
                            $rs = $conn->query($query);
                            $num = $rs->num_rows;
                            $sn = 0;

                            if ($num > 0) { 
                                while ($rows = $rs->fetch_assoc()) {
                                    $sn++;
                                    echo "
                                    <tr onclick='populateForm(\"" . htmlspecialchars($rows['schoolid']) . "\", \"" . htmlspecialchars($rows['name']) . "\", \"" . htmlspecialchars($rows['department']) . "\", \"" . htmlspecialchars($rows['category']) . "\")'>
                                        <td>" . $sn . "</td>
                                        <td>" . htmlspecialchars($rows['schoolid']) . "</td>
                                        <td>" . htmlspecialchars($rows['name']) . "</td>
                                        <td>" . htmlspecialchars($rows['department']) . "</td>
                                        <td>" . htmlspecialchars($rows['category']) . "</td>
                                        <td><a href='?action=delete&Id=" . $rows['did'] . "'><i class='fas fa-fw fa-trash'></i></a></td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No Record Found!</td></tr>";
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

                      
                  function populateForm(id, name, department, category) {
                      document.getElementById('id').value = id;
                      document.getElementById('name').value = name;
                      document.getElementById('department').value = department;
                      document.getElementById('category').value = category;
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