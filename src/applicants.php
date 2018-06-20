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
<!-- The job posting form -->
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
            <a href="applicants.php">Applicants</a>
        </div>
    </div>

    <!-- The center part -->
    <div id="centre" style="overflow: auto; width: 1200px;">
        <div id="personal-info">
            <h1>You Have Following Resumes from Applicants</h1>
            <?php
            global $dbh;
            $q_comid = $_SESSION['companyid'];
            try {
                $result = $dbh->query("SELECT a.userid, a.jobtitle, r.contents FROM applyfor a JOIN resume r ON a.userid = r.userid  
                                                 WHERE comid=$q_comid")->fetchAll(PDO::FETCH_ASSOC);
            }  catch(PDOException $e) {
                die($e->errorInfo[2]);
            }
            if (is_array($result)) {
                foreach ($result as $row) {
                    echo '<!-- Each table has an invisible form -->                                  
                                  <!-- The table itself -->
                                  <table style="width: 100%; margin-bottom: 15px; border: 2px solid">
                                    <tbody style="text-align: center;">
                                        <tr style="color: darkred;">
                                             <td style="width: 15%;">User ID</td>
                                             <td style="width: 35%;">Job Title</td>
                                             <td style="width: 50%;">Resume Contents</td>
                                        </tr>
                                        <tr>
                                             <td style="width:15%;">'.$row['userid'].'</td>
                                             <td style="width:35%;">'.$row['jobtitle'].'</td>
                                             <td style="width:50%;"><textarea style="inline-block; width:90%;">'.$row['contents'].'</textarea></td>        
                                        </tr>         
                                     </tbody>          
                                  </table>';
                }
            } else {
                echo "<p>Oops, you haven't posted any job yet!</p>";
            }
            ?>

        </div>
    </div>
</div>

<script>
    var modal = document.getElementById("resume-job-upload");
    window.onclick = function(event) {
        if (event.target === modal) modal.style.display = "none";
    }

    function showUpload() {
        document.getElementById("resume-job-upload").style.display = "block";
    }

    function showEdit() {

    }
</script>
</body>
</html>