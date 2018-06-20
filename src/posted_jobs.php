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
            <h1>You can edit/delete a posted job</h1>
            <?php
            global $dbh;
            $q_comid = ($_SESSION['companyid']);
            try {
                $result = $dbh->query("SELECT * FROM job WHERE companyid=$q_comid")->fetchAll(PDO::FETCH_ASSOC);
            }  catch(PDOException $e) {
                die($e->errorInfo[2]);
            }
            if (is_array($result)) {
                foreach ($result as $row) {
                    $input_title = '<input form="job-edit-'.$row['title'].'" type="text" style="inline-block; width:90%;" name="title_edit" value="'.$row['title'].'" required>';
                    $input_description = '<textarea  form="job-edit-'.$row['title'].'" style="inline-block; width:90%;" name="description_edit" required>'.$row['jobdescription'].'</textarea>';
                    $input_program = '<select  form="job-edit-'.$row['title'].'" style="width: 90%; height: 40px;" name="program_edit" required>
                                        <option value="">Update targeted program</option>
                                        <option value="Computer Science">Computer Science</option>
                                        <option value="Computer Engineering">Computer Engineering</option>
                                        <option value="Software Engineering">Software Engineering</option>
                                        <option value="Electrical Engineering">Electrical Engineering</option>
                                      </select>';
                    $input_level = '<select  form="job-edit-'.$row['title'].'" style="width: 90%; height: 40px;" name="level_edit" required>
                                        <option value="">Update targeted level</option>
                                        <option value="1">1st year</option>
                                        <option value="2">2nd year</option>
                                        <option value="3">3rd year</option>
                                        <option value="4">4th year</option>
                                    </select>';
                    $input_num_pos = '<input  form="job-edit-'.$row['title'].'" type="number" style="height:40px;width: 80%;" name="num_pos_edit" value="'.$row['numberofpositions'].'" required>';
                    $input_closing_date = '<input  form="job-edit-'.$row['title'].'" type="date" style="display: inline; width: 100%;" name="closing_date_edit" required>';
                    echo '<!-- Each table has an invisible form -->
                                  <form id="job-edit-'.$row['title'].'" action="job_post_disposal.php" method="post" hidden>
                                    <input type="text" name="old_title" value="'.$row['title'].'" hidden>
                                  </form>
                                  
                                  <!-- The table itself -->
                                  <table style="width: 100%; margin-bottom: 15px; border: 2px solid">
                                    <tbody style="text-align: center;">
                                       <tr style="color: darkred;">
                                                    <td style="width: 21%;">Title</td>
                                                    <td style="width: 25%;">Description</td>
                                                    <td style="width: 20%;">Targeted Program: '.$row['targetedprogram'].'</td>
                                                    <td style="width: 7%;">Targeted Level: '.$row['targetedlevel'].'</td>
                                                    <td style="width: 7%;">No. positions</td>
                                                    <td style="width: 10%;">Closing Date: '.$row['closingdate'].'</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:21%;">'.$input_title.'</td>
                                                    <td style="width:25%;">'.$input_description.'</td>
                                                    <td style="width:20%;">'.$input_program.'</td>
                                                    <td style="width:7%;">'.$input_level.'</td>
                                                    <td style="width:7%;">'.$input_num_pos.'</td>
                                                    <td style="width:10%;">'.$input_closing_date.'</td>
                                                    <td>
                                                        <button type="submit" form="job-edit-'.$row['title'].'" name="job-update-button">update</button><br>
                                                        <a href="job_post_disposal.php?action=delete&title='.$row['title'].'">Delete</a><br>
                                                    </td>
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