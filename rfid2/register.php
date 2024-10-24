<?php
require_once 'includes/dbcon.php'; // Include the database connection file

// Initialize variables
$name = "";
$rfid = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $rfid = $_POST['rfid'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $userType = $_POST['userType'];

    // Fetch cid from rfid table
    $sql = "SELECT cid FROM rfid WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cid = $row['cid'];

        // Fetch did based on name (or rfid)
        $sql = "SELECT did FROM information WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $did = $row['did'];

            // Check if the stid is already registered
            $sql = "SELECT * FROM admin WHERE stid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $cid); // Use cid as stid
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
              
                echo "<script>alert('This RFID is already registered.'); window.location.href='register.php';</script>"; // Alert if already registered
            
            } else {
                // Insert into admin table
                $sql = "INSERT INTO admin (stid, password, category) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $cid, $password, $userType);
                if ($stmt->execute()) {
                    header("Location: index.php"); // Redirect to dashboard or any other page
                    exit;
                } else {
                    echo "<p>Error: " . $stmt->error . "</p>";
                }
            }
        } else {
            echo "<p>Name not found.</p>";
        }
    } else {
        echo "<p>RFID not found.</p>";
    }

    $stmt->close();
}

// Fetch name based on RFID when the input loses focus
if (isset($_POST['rfid']) && !empty($_POST['rfid'])) {
    $rfid = $_POST['rfid'];

    // Fetch cid from rfid table
    $sql = "SELECT cid FROM rfid WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cid = $row['cid'];

        // Fetch name based on cid
        $sql = "SELECT name FROM information WHERE did = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $name = $row['name'];
            echo json_encode(['success' => true, 'name' => $name]);
            exit;
        }
    }
    echo json_encode(['success' => false]);
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <title>Homepage/Register</title>
    <link rel="stylesheet" href="logn.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <form action="" method="POST" id="registrationForm">
            <h2 align="center">ST. CECILIA'S COLLEGE-CEBU, INC.</h2>
            <div class="text-center">
                <img src="logo/logo.jpg" style="width:100px;height:100px">
                <br><br>
            </div>
            <h2>Registration</h2>
            <div class="form-group">
                <select required name="userType" class="form-control mb-3" id="userType">
                    <option value="">--Select User Roles--</option>
                    <option value="Technical Support">Technical Support</option>
                    <option value="HR">HR</option>
                    <option value="Registrar">Registrar</option>
                </select>
            </div>
            <div class="input-field">
                <input type="text" name="rfid" id="rfid" value="<?php echo htmlspecialchars($rfid); ?>" required>
                <label>Enter RFID</label>
            </div>
            <div class="input-field">
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
                <label>Enter Name</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" required>
                <label>Enter your password</label>
            </div>
            <div class="forget">
                <label for="remember">
                    <input type="checkbox" id="remember" name="remember">
                    <p>Remember me</p>
                </label>
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit" name="register">Register</button>
            <div class="register">
                <p>Already have an account? <a href="index.php">Login</a></p>
            </div>
        </form>
    </div>
    <script>
    $(document).ready(function() {
        $('#rfid').on('blur', function() { // Trigger on blur instead of keyup
            const rfidValue = $(this).val();

            if (rfidValue.length > 0) {
                // AJAX request to fetch name based on RFID
                $.ajax({
                    url: '', // Use the same file for the AJAX request
                    type: 'POST',
                    data: { rfid: rfidValue },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#name').val(response.name);
                        } else {
                            $('#name').val(""); // Clear if not found
                            alert('RFID not found.');
                        }
                    }
                });
            } else {
                $('#name').val(""); // Clear name if RFID input is empty
            }
        });
    });
    </script>
</body>

</html>
