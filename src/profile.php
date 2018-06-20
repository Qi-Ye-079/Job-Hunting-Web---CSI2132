<?php
session_start();
require_once "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" type="text/css" href="css/user_account.css">
</head>
<body>
<?php
$message = "";

if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['update'])) {
    if ($_SESSION['password'] !== $_POST['old_password']) {
        $GLOBALS['message'] = "* Old password incorrect. Please try again.";
    } else {
        $new_psw = !empty($_POST['new_password']) ? $_POST['new_password']: $_SESSION['password'];
        // If it's a student that logs in
        if (isset($_SESSION['userid'])) {
            $new_uname = !empty($_POST['new_username']) ? $_POST['new_username']: $_SESSION['username'];
            $new_level = !empty($_POST['new_level']) ? $_POST['new_level']: $_SESSION['level'];
            $new_program = !empty($_POST['new_program']) ? $_POST['new_program']: $_SESSION['program'];
            $GLOBALS['message'] = update("student",$_SESSION['userid'],$new_uname,$new_psw,$new_level,$new_program);
        }
        // else if it's a company
        elseif (isset($_SESSION['companyid'])) {
            $new_cname = !empty($_POST['new_cname']) ? $_POST['new_cname']: $_SESSION['cname'];
            $new_num_employees = !empty($_POST['new_num_employees']) ? $_POST['new_num_employees']: $_SESSION['numberofemployees'];
            $GLOBALS['message'] = update("company",$_SESSION['companyid'],$new_cname,$new_psw,$new_num_employees);
        }
    }

}

/*
 *  A student can update his username, password, level or program (email cannot be changed).
 *  Company can change its name, password and number of employees
 */
function update($target,$id,$name,$password,$num,$program="") {
    global $dbh;

    $q_name = $dbh->quote($name);
    $q_password = $dbh->quote($password);
    $q_program = $dbh->quote($program);
    try {
        // If a student logs in
        if ($target==="student") {
            $result = $dbh->exec("UPDATE student
                         SET username=$q_name, password=$q_password, study_level=$num, program=$q_program
                         WHERE userid = $id");
        }
        // Else if company
        else {
            $result = $dbh->exec("UPDATE company
                         SET cname=$q_name, password=$q_password, numberofemployees=$num
                         WHERE companyid = $id");
        }
        if ($result == 0) return "* Update failed. Please check your entered information again.";
        else if ($target==="student") {
            $_SESSION['username'] = $name;
            $_SESSION['password'] = $password;
            $_SESSION['level'] = $num;
            $_SESSION['program'] = $program;
            return "* Student Update successful!";
        } else {
            $_SESSION['cname'] = $name;
            $_SESSION['password'] = $password;
            $_SESSION['numberofemployees'] = $num;
            return "* Company Update successful!";
        }
    } catch (PDOException $e) {
        // If the username already exists
        if ($e->errorInfo[0]==23505) {
            return "* User name already exists. ";
        } else {
            die($e->errorInfo[0]." ".$e->errorInfo[2]); // For debugging use
        }
    }

}
?>

<?php if (isset($_SESSION['userid'])) { ?>
    <!-- The upload resume form -->
    <div id="resume-job-upload">
        <div id="resume-job-upload-content">
            <form action="<?php echo htmlspecialchars('upload.php'); ?>" method="post" style="text-align: center">
                <label>Content of your resume</label>
                <textarea name="resume_content" class="description" style="height: 380px!important;" required></textarea>
                <input type="submit" class="resume-job-upload-submit" name="resume_submit" value="Upload">
            </form>
        </div>
    </div>

    <div id="top">
        <div  style="display: inline-block; height: 50px;text-align: center">
            <a href="search_result.php" style="margin: 15px 30px;">Click here back to search page</a>
        </div>

        <div style="float: right; margin: 15px 20px">
            <a id="upload-resume" onclick="showUpload()">Upload resume</a>
        </div>
        <div class="dropdown" style="float: right; margin: 15px 20px;">
            <img id="user-avatar" src="images/user.png">
            <div class="dropdown-content" style="width: 200px!important;">
                <span style="color: darkred;display: block; margin: 10px 15px;">Username: <?php echo $_SESSION['username'];?></span>
                <a href="profile.php" style="border-bottom: 1px ridge gainsboro;">Profile</a>
                <a href="uploaded_resumes.php">Uploaded Resumes</a>
                <a href="company_reviews.php">Reviews</a>
                <?php if ($_SESSION['usertype']=="M") { ?>
                    <a href="resume_requests.php">Review Requests</a>
                <?php } ?>
            </div>
        </div>

        <!-- The center part -->
        <div id="centre">
            <div id="personal-info">
                <h1 style="color:limegreen">YOU CAN UPDATE YOUR PROFILE HERE<br>(。・`ω´・)
                    <span style="color: red; font-size: 15px; float: right;vertical-align: middle"><?php echo $message;?></span>
                </h1>
                <form style="margin-top: 15px;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                    <label style="margin-bottom: 20px; display: block;color: darkred; font-size: larger">
                        <b>Your user id: <?php echo $_SESSION['userid'];?></b>
                    </label>

                    <label><b>Your Email address (not updatable)</b></label>
                    <input value="<?php echo $_SESSION['email']; ?>" disabled>

                    <label><b>Your new user name (Currently: <?php echo $_SESSION['username']; ?>)</b></label>
                    <input type="text" name="new_username">

                    <label><b>Your current password <span style="color: red">(required if you want to update any of your infomation)</span></b></label>
                    <input type="password" name="old_password" placeholder="Enter your current password here.." required>

                    <label><b>Your new password <span style="color: red">(Must be different from your old password)</span></b></label>
                    <input type="password" name="new_password" placeholder="Enter your new password here..">

                    <label><b>Your level of study (Currently: <?php echo $_SESSION['level']; ?>)</b></label>
                    <input type="number" name="new_level" placeholder="Update your level of study here..">

                    <label><b>Your new program (Currently: <?php echo $_SESSION['program']; ?>)</b></label>
                    <select class="select-list" name="new_program">
                        <option value="" disabled selected>Choose your new program</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Software Engineering">Software Engineering</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                    </select><br>
                    <input id="update-button" type="submit" name="update" value="UPDATE">
                </form>
            </div>
        </div>
    </div>
    <!-- Else if a company has logged in -->
<?php } else if (isset($_SESSION['companyid'])) { ?>
    <div id="resume-job-upload">
        <div id="resume-job-upload-content" style="height: 600px!important;">
            <form action="<?php echo htmlspecialchars('upload.php'); ?>" method="post" style="text-align: center">
                <input class="resume-job-upload-input" name="new_job_title" placeholder="Title of job" required>
                <select class="select-list" style="width: 80%; margin: 15px auto" name="new_target_program" required>
                    <option value="" disabled selected>Please choose a targeted program</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Computer Engineering">Computer Engineering</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Electrical Engineering">Electrical Engineering</option>
                </select>
                <input class="resume-job-upload-input" type="number" name="new_target_level" placeholder="Expected level of study" required>
                <input class="resume-job-upload-input" type="number" name="new_num_positions" placeholder="Number of positions available (optional)">
                <input class="resume-job-upload-input" type="date" name="new_closingdate" placeholder="Closing date of this job" required>
                <textarea class="description" placeholder="Description of job" name="new_job_description" required></textarea>
                <input type="submit" class="resume-job-upload-submit" name="job_submit" value="Upload">
            </form>
        </div>
    </div>

    <div id="top">
        <div  style="display: inline-block; height: 50px;text-align: center">
            <a href="search_result.php" style="margin: 15px 30px;">Click here back to search page</a>
        </div>

        <div style="float: right; margin: 15px 20px">
            <a id="upload-resume" onclick="showUpload()">Post a job</a>
        </div>
        <div class="dropdown" style="float: right; margin: 15px 20px;">
            <img id="user-avatar" src="images/company-avatar.png">
            <div class="dropdown-content" style="width: 200px!important;">
                <span style="color: darkred;display: block; margin: 10px 15px;">Company name: <?php echo $_SESSION['cname'];?></span>
                <a href="profile.php" style="border-bottom: 1px ridge gainsboro;">Company Profile</a>
                <a href="posted_jobs.php">Posted Jobs</a>
            </div>
        </div>

        <!-- The center part -->
        <div id="centre">
            <div id="personal-info">
                <h1 style="color:limegreen">YOU CAN UPDATE YOUR PROFILE HERE<br>(。・`ω´・)
                    <span style="color: red; font-size: 15px; float: right;vertical-align: middle"><?php echo $message;?></span>
                </h1>

                <form style="margin-top: 15px;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                    <label style="margin-bottom: 20px; display: block;color: darkred; font-size: larger">
                        <b>Your company id: <?php echo $_SESSION['companyid'];?></b>
                    </label>

                    <label><b>Working Email address (not updatable)</b></label>
                    <input value="<?php echo $_SESSION['workemail']; ?>" disabled>

                    <label><b>New company name</b></label>
                    <input type="text" name="new_cname" value="<?php echo $_SESSION['cname']; ?>">

                    <label><b>Current password <span style="color: red">(required if you want to update any of your infomation)</span></b></label>
                    <input type="password" name="old_password" placeholder="Enter current password here.." required>

                    <label><b>New password <span style="color: red">(Must be different from old password)</span></b></label>
                    <input type="password" name="new_password" placeholder="Enter new password here..">

                    <label><b>Current number of employees (Currently: <?php echo $_SESSION['numberofemployees']; ?>)</b></label>
                    <input type="number" name="new_num_employees" placeholder="Update number of employees here..">


                    <input id="update-button" type="submit" name="update" value="UPDATE">
                </form>
            </div>
        </div>
    </div>
<?php } ?>


<script>
    var modal = document.getElementById("resume-job-upload");
    window.onclick = function(event) {
        if (event.target === modal) modal.style.display = "none";
    }

    function showUpload() {
        document.getElementById("resume-job-upload").style.display = "block";
    }
</script>

</body>
</html>