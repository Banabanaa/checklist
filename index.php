<?php
include("php/config.php");

$results_per_page = 10; // Number of results per page

// Default SQL query if no search is performed
$sql = "SELECT c.course_code, c.year, c.semester, c.course_title,
        COALESCE(d.grade, 'N/A') AS grade, d.remarks, 
        i.last_name
        FROM subject AS c 
        LEFT JOIN instructor AS i ON c.course_code = i.course_code 
        LEFT JOIN grade AS d ON c.course_code = d.course_code ";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    
    // search conditions
    if (!empty($search)) {
        // user search query is provided, search by grade or other fields
        $sql .= "WHERE d.grade = '$search' OR 
                  c.course_code = '$search' OR 
                  c.course_title = '$search' OR 
                  i.last_name = '$search' OR
                  c.year = '$search' OR 
                  c.semester = '$search' OR 
                  d.remarks = '$search'
                  ORDER BY c.course_code ASC";
    } else {
        // search in all fields
        $sql .= "WHERE d.grade LIKE '%$search%' OR 
        c.course_code LIKE '%$search%' OR 
        c.year LIKE '%$search%' OR 
        c.semester LIKE '%$search%' OR 
        c.course_title LIKE '%$search%' OR
        i.fullname LIKE '%$search%' ";
    }
}

// filter variables for year and semester
$year_filter = isset($_GET['year_filter']) ? $_GET['year_filter'] : "";
$semester_filter = isset($_GET['semester_filter']) ? $_GET['semester_filter'] : "";

// WHERE clause based on selected filters
$where_clause = "";
if (!empty($year_filter) && !empty($semester_filter)) {
    $where_clause = "WHERE c.year = '$year_filter' AND c.semester = '$semester_filter'";
} elseif (!empty($year_filter)) {
    $where_clause = "WHERE c.year = '$year_filter'";
} elseif (!empty($semester_filter)) {
    $where_clause = "WHERE c.semester = '$semester_filter'";
}

// Append the WHERE clause to the SQL query
$sql .= $where_clause;

// Determine the total number of pages available
$query_total = "SELECT COUNT(*) AS total FROM subject AS c 
                LEFT JOIN instructor AS i ON c.course_code = i.course_code 
                LEFT JOIN grade AS d ON c.course_code = d.course_code";

$result_total = mysqli_query($con, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_pages = ceil($row_total["total"] / $results_per_page);

// Determine the current page number and calculate the starting row for the SQL query
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages) {
    $current_page = $total_pages;
}
$start_from = ($current_page - 1) * $results_per_page;

// Fetch the results for the current page
$sql .= " LIMIT $start_from, $results_per_page";
$result = mysqli_query($con, $sql);
$num_results = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Home</title>
</head>
<body>
    <div class="nav">
        <div class="logo" style="display: flex; align-items: center;">
        <img src="cvsu logo.png" alt="CVSU" width="60px" height="60px"><p>BSCS CHECKLIST OF COURSES</p><img src="acs logo.jpg" alt="ACS" width="60px" height="60px">
        </div>

        <div class="right-links">
            <a href='edit.php?Id=$res_id'><button class="btn">STUDENT INFORMATION</button></a>
        </div>
    </div>

    <br>
    
    <div style="text-align: center; display: flex; flex-direction: row; justify-content: center; align-items: center;">
        <form method="GET" action="" style="margin-left: 10px;">
            <input type="text" name="search" id="search" placeholder="Enter keywords" style="width: 900px; padding: 8px; font-size: 16px; margin-right:8px;">
            <button type="submit" class="btn2">SEARCH</button>    
        </form>
    </div>

    <div style="text-align: center; display: flex; flex-direction: row; justify-content: center; align-items: center; padding-top:10px;">
        <form method="GET" action="" style="margin-left: 10px;">
            <select name="year_filter" id="year_filter">
                <option value="">All Year</option>
                <option value="First Year">First Year</option>
                <option value="Second Year">Second Year</option>
                <option value="Third Year">Third Year</option>
                <option value="Mid Year">Mid Year</option>
                <option value="Fourth Year">Fourth Year</option>
            </select>
            <select name="semester_filter" id="semester_filter">
                <option value="">All Semesters</option>
                <option value="First Semester">First Semester</option>
                <option value="Mid Year">Mid Year</option>
                <option value="Second Semester">Second Semester</option>
            </select>
            <button type="submit" style="padding-left:5px; padding-right:5px;">LOAD</button>    
        </form>
    </div>
    <div style="text-align: center; display: flex; flex-direction: row; justify-content: center; align-items: center; padding-top:10px;">
    <div style="margin-left: 10px;">
            <?php
                // Compute average grade from the database
                $average_query = "SELECT AVG(grade) AS average_grade FROM grade WHERE grade != 'N/A' AND grade != 'S'";
                $average_result = mysqli_query($con, $average_query);
                $average_row = mysqli_fetch_assoc($average_result);
                $average_grade = $average_row['average_grade'];
            ?>
            <input value="  Average Grade: <?php echo round($average_grade, 2); ?>  " readonly>
        </div>
        <div style="margin-left: 10px;">
            <?php
                // Compute average grade from the database
                $min_query = "SELECT MAX(CAST(grade AS DECIMAL(4,2))) AS min_grade FROM grade WHERE grade REGEXP '^[0-9]+(\.[0-9]{1,2})?$';";
                $min_result = mysqli_query($con, $min_query);
                $min_row = mysqli_fetch_assoc($min_result);
                $min_grade = $min_row['min_grade'];
            ?>
            <input value="  Min Grade: <?php echo round($min_grade, 2); ?>  " readonly>
        </div>
        <div style="margin-left: 10px;">
            <?php
                // Compute average grade from the database
                $max_query = "SELECT MIN(CAST(grade AS DECIMAL(4,2))) AS max_grade FROM grade WHERE grade REGEXP '^[0-9]+(\.[0-9]{1,2})?$';";
                $max_result = mysqli_query($con, $max_query);
                $max_row = mysqli_fetch_assoc($max_result);
                $max_grade = $max_row['max_grade'];
            ?>
            <input value="  Max Grade: <?php echo round($max_grade, 2); ?>.00  " readonly>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table table-hover text-center table-custom">
            <thead>
                <tr class="<?php echo $highlightClass; ?>">
                    <th scope="col">COURSE CODE</th>
                    <th scope="col">YEAR</th>
                    <th scope="col">SEMESTER</th>
                    <th scope="col">COURSE TITLE</th>
                    <th scope="col">GRADE</th>
                    <th scope="col">REMARKS</th>
                    <th scope="col">INSTRUCTOR</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are any search results
                if ($num_results > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo $row["course_code"] ?></td>
                            <td><?php echo $row["year"] ?></td>
                            <td><?php echo $row["semester"] ?></td>
                            <td><?php echo $row["course_title"] ?></td>
                            <td><?php echo $row["grade"] ?></td>
                            <td><?php echo $row["remarks"] ?></td>
                            <td><?php echo $row["last_name"] ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    // Display a message if there are no search results
                    echo "<tr><td colspan='9'>No results found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
    <?php
    if ($total_pages > 1) {
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $current_page) {
                echo '<span class="current-page">'.$i.'</span>';
            } else {
                // include the search query, year filter, and semester filter to the pagination links
                $pagination_link = "?page=$i";
                if (!empty($_GET['search'])) {
                    $pagination_link .= "&search=" . urlencode($_GET['search']);
                }
                if (!empty($year_filter)) {
                    $pagination_link .= "&year_filter=" . urlencode($year_filter);
                }
                if (!empty($semester_filter)) {
                    $pagination_link .= "&semester_filter=" . urlencode($semester_filter);
                }
                echo '<a href="' . $pagination_link . '" class="page-link">'.$i.'</a>';
            }
        }
    }
    ?>
</div>
    <br>
</body>
</html>