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
?>
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
    <div id="centre">
        <div id="personal-info">
            <p style="color:limegreen">YOU CAN EDIT/DELETE YOUR REVIEWS HERE (Evaluations cannot be edited)(。・`ω´・)
                <span style="color: red; font-size: 15px; float: right;vertical-align: middle"><?php echo $message;?></span>
            </p>
            <?php
            global $dbh;
            $q_userid = $_SESSION['userid'];
            try {
                $result = $dbh->query("SELECT c.cname, r.reviewid, r.comments FROM review r NATURAL JOIN company c WHERE r.userid=$q_userid")->fetchAll(PDO::FETCH_ASSOC);
            }  catch(PDOException $e) {
                die($e->errorInfo[2]);
            }
            if (is_array($result)) {
                foreach ($result as $row) {
                    echo '<!-- Each table has an invisible form -->
                                  <form id="review-edit-'.$row['reviewid'].'" action="review_disposal.php" method="post" hidden>
                                        <input type="number" name="reviewid" value="'.$row['reviewid'].'" hidden>
                                  </form>
                                  
                                  <!-- The table itself -->
                                  <table style="width: 100%; margin-bottom: 15px; border: 2px solid">
                                    <tbody style="text-align: center;">
                                       <tr style="color: darkred;">
                                                    <td style="width:35%;">Company name</td>
                                                    <td style="width:50%;">Comments</td>
                                                    <td style="width:15%;">Action</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:35%;">'.$row['cname'].'</td>
                                                    <td style="width:50%;">
                                                        <textarea name="comments" form="review-edit-'.$row['reviewid'].'" style="display:inline-block;width:90%;" required>'.$row['comments'].'</textarea>
                                                    </td>
                                                    <td style="width:15%;">
                                                        <button type="submit" form="review-edit-'.$row['reviewid'].'" name="review-update-button">update</button><br>
                                                        <a href="review_disposal.php?action=delete&reviewid='.$row['reviewid'].'">Delete</a><br>
                                                    </td>
                                        </tr>         
                                     </tbody>          
                                  </table>';
                }
            } else {
                echo "<p style='color: black;'>Oops, you haven't uploaded any resume yet!</p>";
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