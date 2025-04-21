<?php 
    include("php/config.php");

    // Fetch student information from the database
    $query = "SELECT * FROM student WHERE studentnum = 202211815";
    $result = mysqli_query($con, $query);
    
    // Check if the query was successful
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the data as an associative array
        $row = mysqli_fetch_assoc($result);

        // Assign values to variables
        $studentnum = $row['studentnum'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $yearlvl = $row['yearlvl'];
        $email = $row['email'];
    } else {
        // Handle case where no results were found
        echo "No student found with that student number.";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Update Profile</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
        <p><a href="index.php">BSCS CHECKLIST OF COURSES</a></p>
        </div>

        <div class="right-links">
            <a href='#'><button class="btn">STUDENT INFORMATION</button></a>
            <a href="index.php"> <button class="btn1">BACK</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <header>STUDENT PROFILE</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="fullname"><b>Full Name</b></label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo $first_name . ' ' . $last_name; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="studentnum"><b>Student Number</b></label>
                    <input type="text" name="studentnum" id="studentnum" value="<?php echo $studentnum; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="yrlvl"><b>Year Level</b></label>
                    <input type="text" name="yrlvl" id="yrlvl" value="<?php echo $yearlvl; ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email"><b>Email Address</b></label>
                    <input type="text" name="email" id="email" value="<?php echo $email; ?>" autocomplete="off" required>
                </div>
                
            </form>
        </div>
      </div>
</body>
</html>
