<?php
include '../Includes/dbcon.php'; // Include the database connection script

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = $_POST['search'];

    // Perform database query
    $sql = "SELECT * FROM student_list WHERE student_id_number LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%$search%";
    $stmt->bind_param('s', $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Populate form fields
        $studentId = $row['student_id'];
        $studentName = $row['student_name'];
        $year = $row['year'];
        $course = $row['course'];
    } else {
        echo "No records found.";
    }

    $stmt->close();
}
?>
