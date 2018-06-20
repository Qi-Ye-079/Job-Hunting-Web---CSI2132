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
            <a href="company_reviews.php">Company Reviews</a>
            <?php if ($_SESSION['usertype']=="M") { ?>
                <a href="resume_requests.php">Review Requests</a>
            <?php } ?>
        </div>
    </div>

    <!-- The center part -->
    <div id="centre" style="overflow: auto; width: 1000px!important;">
        <div id="personal-info">
            <h1>You have the following Review Requests</h1>
            <?php
            global $dbh;
            $q_userid = $_SESSION['userid'];
            try {
                $result = $dbh->query("SELECT * FROM resume WHERE moderatorid=$q_userid")->fetchAll(PDO::FETCH_ASSOC);
            }  catch(PDOException $e) {
                die($e->errorInfo[2]);
            }
            if (is_array($result)) {
                foreach ($result as $row) {
                    echo '<!-- Each table has an invisible form -->
                                  <form id="rr-'.$row['resumeid'].'-'.$row['userid'].'-'.$row['version'].'" action="resume_review_disposal.php" method="post" hidden>
                                        <input type="text" name="review_userid" value="'.$row['userid'].'" hidden>
                                        <input type="text" name="review_resumeid" value="'.$row['resumeid'].'" hidden>
                                        <input type="text" name="review_version" value="'.$row['version'].'" hidden>
                                  </form>
                                  
                                  <!-- The table itself -->
                                  <table style="width: 100%; margin-bottom: 15px; border: 2px solid">
                                    <tbody style="text-align: center;">
                                       <tr style="color: darkred;">
                                                    <td style="width: 10%;">User ID</td>
                                                    <td style="width: 10%;">resume ID</td>
                                                    <td style="width: 10%;">version</td>
                                                    <td style="width: 30%;">Content</td>
                                                    <td style="width: 25%;">Comments</td>
                                                    <td style="width: 15%;">Actions</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:10%;">'.$row['userid'].'</td>
                                                    <td style="width:10%;">'.$row['resumeid'].'</td>
                                                    <td style="width:10%;">'.$row['version'].'</td>
                                                    <td style="width:30%;"><textarea form="rr-'.$row['resumeid'].'-'.$row['userid'].'-'.$row['version'].'" style="display:inline-block;width:90%;" name="old_contents">'.$row['contents'].'</textarea></td>
                                                    <td style="width:30%;"><textarea form="rr-'.$row['resumeid'].'-'.$row['userid'].'-'.$row['version'].'" style="display:inline-block;width:90%;" name="resume-comments" required></textarea></td>
                                                    <td style="width:15%;">
                                                        <button type="submit" form="rr-'.$row['resumeid'].'-'.$row['userid'].'-'.$row['version'].'" name="resume-review-button">Submit</button><br>
                                                    </td>
                                        </tr>         
                                     </tbody>          
                                  </table>';
                }
            } else {
                echo "<p>Oops, you haven't uploaded any resume yet!</p>";
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
</script>
</body>
</html>