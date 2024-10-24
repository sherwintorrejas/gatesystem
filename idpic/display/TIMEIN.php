<?php
// Include the database connection file
include '..\..\rfid2\Includes\dbcon.php'; // Adjust the path as necessary

// SQL query to fetch today's "IN" entries with student and staff details
$sql = "
SELECT 
    dl.lid,
    dl.datetime, 
    CASE 
        WHEN dl.sid IS NOT NULL THEN s.sid
        ELSE NULL 
    END AS sid,
    CASE 
        WHEN dl.stid IS NOT NULL THEN st.stid
        ELSE NULL 
    END AS stid,
    s.image AS student_image,
    st.image AS staff_image,
    i.name,
    i.department
FROM 
    dailylogs AS dl
LEFT JOIN 
    registudent AS s ON dl.sid = s.sid
LEFT JOIN 
    registaff AS st ON dl.stid = st.stid
LEFT JOIN 
    information AS i ON (s.did = i.did OR st.did = i.did)
WHERE 
    dl.category = 'IN' AND DATE(dl.datetime) = CURDATE()
ORDER BY 
    dl.datetime DESC 
LIMIT 3";

// Fetch the latest "IN" entries with student and staff details
$result = $conn->query($sql);

// Prepare an array to store entries
$latestEntries = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $latestEntries[] = $row;
    }
} else {
    $latestEntries = []; // No entries found
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIMEIN</title>
    <link rel="stylesheet" href="gg.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .bg-cover {
            background-image: url('img/scc school.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        body,
        html {
            height: 100%;
            font-family: Arial, sans-serif;
            margin: 0;
            background-image: url('display/logo.jpg');


            /* Replace with your image path */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .center-line {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-left: 2px solid black;
            height: 100%;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            background-color: rgba(240, 240, 240, 0.9);
            /* Semi-transparent background */
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            background-color: #9b0a1e;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .logo {
            width: 60%;
        }

        .time-date-container {
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            background-color: #f0f0f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .time,
        .date {
            font-size: 50px;
            font-weight: bold;
        }

        .date {
            text-align: right;
        }

        .student-info {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            padding: 20px 0;
            background-color: rgba(255, 255, 255, 0.9);
            background: url("logo/s1.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            gap: 1px;
            height: calc(100vh - 120px);
        }



        .student-cards {
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            background-color: #f9f9f9;
            width: 600px;
        }

        .student-cards1 {
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            background-color: #f9f9f9;
            width: 700px;
            height: 800px;
        }

        .student-cards2 {
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            background-color: #f9f9f9;
            width: 500px;
        }

        .student-card1,
        .student-card2 {
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            background-color: #f9f9f9;
            width: 500px;
            height: 500px;
        }

        .student-card1 {
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            background-color: #f9f9f9;
            margin-top: 155px;
            margin-left: 50px;
            width: 300px;
        }

        .pic {
            margin: 20px auto;
            height: 400px;
            width: 400px;
            background-color: #9b0a1e;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            box-shadow: 5px 5px 10px #c5c5c5, -5px -5px 10px #fbfbfb;
        }

        .pic1 {
            margin: 20px auto;
            height: 250px;
            width: 250px;
            background-color: #9b0a1e;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            box-shadow: 5px 5px 10px #c5c5c5, -5px -5px 10px #fbfbfb;
        }

        .pic2 {
            margin: 20px auto;
            height: 150px;
            width: 150px;
            background-color: #9b0a1e;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            box-shadow: 5px 5px 10px #c5c5c5, -5px -5px 10px #fbfbfb;
        }

        .details {
            margin: 20px auto;
            height: 190px;
            background-color: #c5c5c5;
            border-radius: 3%;
            text-align: center;
        }

        .details1 {
            margin: 20px auto;
            height: 300px;
            background-color: #c5c5c5;
            border-radius: 3%;
            text-align: center;
        }

        .details2 {
            margin: 20px auto;
            height: 190px;
            background-color: #c5c5c5;
            border-radius: 3%;
            text-align: center;
        }

        h3 {
            display: block;
            font-size: 3em;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
            unicode-bidi: isolate;
        }

        h2 {
            display: block;
            font-size: 2.5em;
            margin-block-start: 0.83em;
            margin-block-end: 0.83em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
            unicode-bidi: isolate;
        }

        p {
            display: block;
            font-size: 3em;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            unicode-bidi: isolate;
            font-weight: bold;
        }

        .p1 {
            margin-top: 1vw;
            margin-bottom: 0.5vw;
            font-size: 2.5vw;
            font-weight: bold;
        }

        .p2 {
            margin-top: 1vw;
            margin-bottom: 0.5vw;
            font-size: 1.5vw;
            font-weight: bold;
        }

        .p3 {
            margin-top: 1vw;
            margin-bottom: 0.5vw;
            font-size: 1.5vw;
            font-weight: bold;
        }

        p4 {
            display: block;
            font-size: 2.5em;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            unicode-bidi: isolate;
            font-weight: bold;
        }

        .pp1,
        .pp2,
        .pp3 {
            margin-top: 6px;
            margin-bottom: 4px;
            font-size: medium;
            font-weight: bold;
        }

        .pp3 {
            text-transform: uppercase;
            opacity: 70%;
        }
        .dep{
                display: block;
                font-size: 2em;
                margin-block-start: 1em;
                margin-block-end: 1em;
                margin-inline-start: 0px;
                margin-inline-end: 0px;
                unicode-bidi: isolate;
                font-weight: bold;
            }
        .tim{
            display: block;
            font-size: 1.5em;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            unicode-bidi: isolate;
            font-weight: bold;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            height: 50px;
            width: 100%;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            height: 60px;
            width: 100%;
        }
    </style>
    <script>
        function updateTimeDate() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const date = now.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            document.querySelector('.time').textContent = `TIME: ${time}`;
            document.querySelector('.date').textContent = `DATE: ${date}`;
        }

        setInterval(updateTimeDate, 1000);
        window.onload = function() {
            updateTimeDate();
        };
        function refreshEntries() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_entries.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                // Update the student-info section with new entries
                document.querySelector('.student-info').innerHTML = this.responseText;
            }
        };
        xhr.send();
    }

    // Refresh the entries every 5 seconds
    setInterval(refreshEntries, 100);
    </script>
</head>

<body>
    
    <div class="refresh">
    <div class="container">
        <header class="header">
            <img src="bgs1.png" alt="School Logo" class="logo">
        </header>

        <div class="time-date-container">
            <div class="time">TIME: </div>
            <div class="time">TIME IN </div>
            <div class="date">DATE: </div>
        </div>

        <div class="student-info">
            
            <div class="third latest IN">
                <div class="student-cards2">
                    <div class="pic">
                    <?php 
                        // Check if student ID exists and display the student's image
                        if (!empty($latestEntries[2]['sid'])) {
                            echo '<img src="../' . basename($latestEntries[2]['student_image']) . '" alt="Student Image" class="pic" style="margin-top: 5px;">';
                        }
                        // Check if staff ID exists and display the staff's image
                        elseif (!empty($latestEntries[2]['stid'])) {
                            echo '<img src="../' . basename($latestEntries[2]['staff_image']) . '" alt="Staff Image" class="pic" style="margin-top: 5px;">';
                        } else {
                            echo '<p></p>';
                        }
                        ?>
                    </div>
                    <div class="details2">
                        <h2><?php echo isset($latestEntries[2]) && $latestEntries[2]['sid'] ? $latestEntries[2]['name'] : (isset($latestEntries[2]['stid']) ? $latestEntries[2]['name'] : 'NAME'); ?></h2>
                        <p class="dep"><?php echo isset($latestEntries[2]) ? $latestEntries[2]['department'] : 'Department'; ?></p>
                        <p class="tim"><?php echo isset($latestEntries[2]) ? date('h:i:s A', strtotime($latestEntries[2]['datetime'])) : ''; ?></p>
                    </div>
                </div>
            </div>
            <div class="Latest IN">
                <div class="student-cards1">
                <div class="pic">
                <?php 
                // Check if student ID exists and display the student's image
                if (!empty($latestEntries[0]['sid'])) {
                    echo '<img src="../' . basename($latestEntries[0]['student_image']) . '" alt="Student Image" class="pic" style="margin-top: 5px;">';
                }
                // Check if staff ID exists and display the staff's image
                elseif (!empty($latestEntries[0]['stid'])) {
                    echo '<img src="../' . basename($latestEntries[0]['staff_image']) . '" alt="Staff Image" class="pic" style="margin-top: 5px;">';
                } else {
                    echo '<p></p>';
                }
                ?>
            </div>
            <div class="details1">
                <h3><?php echo isset($latestEntries[0]) && $latestEntries[0]['sid'] ? $latestEntries[0]['name'] : (isset($latestEntries[0]['stid']) ? $latestEntries[0]['name'] : 'NAME'); ?></h3>
                <p><?php echo isset($latestEntries[0]) ? $latestEntries[0]['department'] : 'Department'; ?></p>
                <p><?php echo isset($latestEntries[0]) ? date('h:i:s A', strtotime($latestEntries[0]['datetime'])) : ''; ?></p>
            </div>
                </div>
            </div>

            <div class="second Latest IN">
                <div class="student-cards2">
                <div class="pic">
                <?php 
                // Check if student ID exists and display the student's image
                if (!empty($latestEntries[1]['sid'])) {
                    echo '<img src="../' . basename($latestEntries[1]['student_image']) . '" alt="Student Image" class="pic" style="margin-top: 5px;" >';
                }
                // Check if staff ID exists and display the staff's image
                elseif (!empty($latestEntries[1]['stid'])) {
                    echo '<img src="../' . basename($latestEntries[1]['staff_image']) . '" alt="Staff Image" class="pic" style="margin-top: 5px;">';
                } else {

                    
                    echo '<p></p>';
                }
                ?>
            </div>
            <div class="details">
                <h2><?php echo isset($latestEntries[1]) && $latestEntries[1]['sid'] ? $latestEntries[1]['name'] : (isset($latestEntries[1]['stid']) ? $latestEntries[1]['name'] : 'NAME'); ?></h2>
                <p class="dep"><?php echo isset($latestEntries[1]) ? $latestEntries[1]['department'] : 'Department'; ?></p>
                <p class="tim"><?php echo isset($latestEntries[1]) ? date('h:i:s A', strtotime($latestEntries[1]['datetime'])) : ''; ?></p>
            </div>
                </div>
            </div>
        </div>
        
        <footer>
            <div class="split">© 2024 All Rights Reserved By TEAM CYBER SENTINELS</div>
        </footer>
    </div>
    </div>
</body>
</html>
