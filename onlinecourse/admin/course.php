<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['submit'])) {
        $coursecode = $_POST['coursecode'];
        $coursename = $_POST['coursename'];
        $courseunit = $_POST['courseunit'];
        $level = $_POST['level'];
        $program = isset($_POST['isCore']) ? null : $_POST['program'];
        $isCore = isset($_POST['isCore']) ? 1 : 0;

        // Check if the course with the same code and level already exists
        $checkQuery = mysqli_query($con, "SELECT * FROM course WHERE courseCode='$coursecode' AND level_id='$level'");
        $existingRecord = mysqli_fetch_array($checkQuery);

        if ($existingRecord) {
            echo '<script>alert("Error: Course with the same code and level already exists.")</script>';
        } else {
            // Insert the new course
            $insertQuery = mysqli_query($con, "INSERT INTO course(courseCode, courseName, courseUnit, level_id, programme_id, isCore) VALUES ('$coursecode', '$coursename', '$courseunit', '$level', '$program', '$isCore')");

            if ($insertQuery) {
                echo '<script>alert("Course added successfully!")</script>';
            } else {
                echo '<script>alert("Error: Unable to add the course.")</script>';
            }
        }
    }

    if (isset($_GET['del'])) {
        $courseId = $_GET['id'];
        mysqli_query($con, "DELETE FROM course WHERE id = '$courseId'");
        echo '<script>alert("Course deleted!!")</script>';
    }
    ?>

    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <title>Admin | Course</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
        <link href="../assets/css/font-awesome.css" rel="stylesheet" />
        <link href="../assets/css/style.css" rel="stylesheet" />
        <link href="../assets/css/course.css" rel="stylesheet" />
        <link href="../assets/css/admin_side_nav.css" rel="stylesheet" />

    </head>

    <body>
   
    <div class="wrapper">
        <!-- Sidebar Holder -->
        <?php if ($_SESSION['alogin'] != "") {
      include('includes/menubar.php');
    }
    ?>

        <!-- Page Content Holder -->
        <div id="content">

            <nav class="navbar navbar-expand-lg ">
                <div class="container-fluid ">

                    <button type="button" id="sidebarCollapse" class="navbar-btn">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ">
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                    </div>
                </div>
            </nav>

            <div >
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <font color="green" align="center">
                                <?php echo htmlentities($_SESSION['msg']); ?>
                                <?php echo htmlentities($_SESSION['msg'] = ""); ?>
                            </font>

                            <div class="panel-body card ">
                                <form name="programme" method="post">
                                    <div class="form-group mb-2">
                                        <label for="coursecode">Course Code </label>
                                        <input type="text" class="form-control" id="coursecode" name="coursecode"
                                            placeholder="Course Code" required />
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="coursename">Course Title </label>
                                        <input type="text" class="form-control" id="coursename" name="coursename"
                                            placeholder="Course Name" required />
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="isCore">Is Core Course?</label>
                                        <input type="checkbox" id="isCore" name="isCore" value="1">
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="courseunit">Credit Hours</label>
                                        <input type="text" class="form-control" id="courseunit" name="courseunit"
                                            placeholder="Course Unit" required />
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="level">Select Level</label>
                                        <select class="form-control" id="level" name="level" required>
                                            <?php
                                            $levels = mysqli_query($con, "SELECT * FROM level");
                                            while ($row = mysqli_fetch_assoc($levels)) {
                                                echo "<option value='" . $row['id'] . "'>" . $row['level'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="program">Select Program</label>
                                        <select class="form-control" id="program" name="program" required <?php echo isset($_POST['isCore']) ? 'disabled' : ''; ?>>
                                            <?php
                                            $programs = mysqli_query($con, "SELECT * FROM programme");
                                            while ($row = mysqli_fetch_assoc($programs)) {
                                                echo "<option value='" . $row['id'] . "'>" . $row['program'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-default mt-3">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <font color="red" align="center">
                    <?php echo htmlentities($_SESSION['delmsg']); ?>
                    <?php echo htmlentities($_SESSION['delmsg'] = ""); ?>
                </font>
                <div class="col-md-12">
                    <?php


                    // Fetch distinct levels from the database
                    $levelQuery = $con->query("SELECT DISTINCT level_id FROM course");

                    while ($levelRow = $levelQuery->fetch_assoc()) {
                        $levelId = $levelRow['level_id'];

                        // Fetch distinct programs for this level
                        $programQuery = $con->prepare("SELECT DISTINCT programme_id, program FROM course 
                                    JOIN programme ON course.programme_id = programme.id
                                    WHERE level_id = ?");
                        $programQuery->bind_param("i", $levelId);
                        $programQuery->execute();

                        $programResult = $programQuery->get_result();

                        if ($programResult) {
                            while ($programRow = $programResult->fetch_assoc()) {
                                $programId = $programRow['programme_id'];

                                // Fetch courses for this level and program, including core courses
                                $stmt = $con->prepare("SELECT * FROM course WHERE (level_id = ? AND programme_id = ?) OR (level_id = ? AND isCore = 1)");
                                $stmt->bind_param("iii", $levelId, $programId, $levelId);
                                $stmt->execute();

                                $result = $stmt->get_result();

                                if ($result) {
                                    // Display your data here
                                    ?>
                                    <div class="panel panel-default card">
                                        <div class="table-heading">Level
                                            <?php echo $levelId . '00'; ?> --
                                            <?php echo $programRow['program']; ?>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive table-bordered">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Course Code</th>
                                                            <th>Course Title</th>
                                                            <th>Creation Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $cnt = 1;
                                                        while ($row = $result->fetch_assoc()) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo $cnt; ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo htmlentities($row['courseCode']); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo htmlentities($row['courseName']); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo htmlentities($row['creationDate']); ?>
                                                                </td>
                                                                <td>
                                                                    <a href="edit-course.php?id=<?php echo $row['id'] ?>">
                                                                        <button class="btn btn-primary"><i class="fa fa-edit"></i> Edit</button>
                                                                    </a>
                                                                    <a href="course.php?id=<?php echo $row['id'] ?>&del=delete"
                                                                        onClick="return confirm('Are you sure you want to delete?')">
                                                                        <button class="btn">Delete</button>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            $cnt++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $result->free_result();
                                } else {
                                    // Handle query error
                                    echo "Error: " . $con->error;
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>

            
         
        </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $(this).toggleClass('active');
            });
        });
    </script>
        <script>
            // Function to enable/disable program selection based on isCore checkbox
            function toggleProgramSelection() {
                var isCoreCheckbox = document.getElementById('isCore');
                var programSelect = document.getElementById('program');

                programSelect.disabled = isCoreCheckbox.checked;
            }

            document.getElementById('isCore').addEventListener('change', toggleProgramSelection);
            toggleProgramSelection();
        </script>
    </body>

    </html>
    <?php
}
?>