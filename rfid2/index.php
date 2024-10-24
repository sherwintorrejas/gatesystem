<?php
require_once 'includes/dbcon.php'; // Include the database connection file

session_start(); // Start session to use session variables

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $name = $_POST['name']; // Using the name input from the form
    $password = $_POST['password'];

    // Fetch the stid and category based on the name
    $sql = "SELECT a.stid, a.category, r.did FROM admin a 
            JOIN registaff r ON a.stid = r.stid 
            JOIN information i ON r.did = i.did 
            WHERE i.name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stid = $row['stid'];
        $category = $row['category'];

        // Verify the password (you need to retrieve the hashed password from the database)
        $sql = "SELECT password FROM admin WHERE stid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $stid);
        $stmt->execute();
        $passwordResult = $stmt->get_result();

        if ($passwordResult->num_rows > 0) {
            $passwordRow = $passwordResult->fetch_assoc();
            // Verify the hashed password
            if (password_verify($password, $passwordRow['password'])) {
                // Store user info in session variables
                $_SESSION['name'] = $name;
                $_SESSION['stid'] = $stid;
                $_SESSION['category'] = $category;

                // Redirect based on user category
                switch ($category) {
                    case 'HR':
                        header("Location: HR/index.php");
                        break;
                    case 'REGISTRAR':
                        header("Location: Registrar/index.php");
                        break;
                    case 'TECHNICAL SUPPORT':
                        header("Location: Technical/index.php");
                        break;
                    default:
                        header("Location: index.php"); // Default redirect
                        break;
                }
                exit;
            } else {
                echo "<p>Incorrect password.</p>";
            }
        } else {
            echo "<p>User not found.</p>";
        }
    } else {
        echo "<p>Name not found.</p>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">

  <title>Homepage/Login</title>
  <link rel="stylesheet" href="logn.css">
</head>

<body>
  <div class="wrapper">
    <form action="" method="POST">
      <h2 align="center">ST. CECILIA'S COLLEGE-CEBU, INC.</h2>
      <div class="text-center">
        <img src="logo/logo.jpg" style="width:100px;height:100px">
        <br><br>
      </div>
      <h2>Login</h2>
      
      <div class="input-field">
        <input type="text" name="name" required>
        <label>Enter your Name</label>
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
      <button type="submit" name="login">Log In</button>
      <div class="register">
        <p>Don't have an account? <a href="register.php">Register</a></p>
      </div>
    </form>
  </div>
</body>

</html>