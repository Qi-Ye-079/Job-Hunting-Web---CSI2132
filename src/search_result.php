<?php
    session_start();
    require_once "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search for your co-op jobs</title>
    <link rel="stylesheet" type="text/css" href="css/search_page.css">
</head>
<body>
    <?php

    // If a search is submited, get all the search results
    if ($_SERVER['REQUEST_METHOD']=="GET" && isset($_GET['submit_search'])) {
        $_SESSION['search_keyword'] = $_GET['search'];
        $_SESSION['filter']=$_GET['filter'];
    } else {
        $_SESSION['search_keyword']="";
        $_SESSION['filter']="";
    }
    $search_result = search_job($_SESSION['search_keyword'], $_SESSION['filter']);

    /*
     * Function search_job:
     *      Search for a job using keywords for job titles ,company names or targeted programs
     */
    function search_job($search, $filter_option) {
        global $dbh;

        if ($filter_option=="Job_DESC") $filter=" ORDER BY numberofapplicants DESC";
        elseif ($filter_option=="Job_ASC") $filter=" ORDER BY numberofapplicants ASC";
        elseif ($filter_option=="Com") $filter=" ORDER BY totalrating DESC";
        else $filter="";
        try {
            return $dbh->query("SELECT companyid,title,cname,closingdate,targetedLevel,targetedprogram, totalrating FROM job NATURAL JOIN company
                                    WHERE title LIKE '%$search%' OR 
                                          targetedProgram LIKE '%$search%' OR
                                          cname LIKE '%$search%' $filter") ->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die($e->errorInfo[2]);
        }
    }

    ?>

    <!-- The upload resume form -->
    <?php if (isset($_SESSION['userid'])) { ?>
        <!-- The upload resume form -->
        <div id="resume-job-upload" class="popup">
            <div class="popup-content">
                <form action="<?php echo htmlspecialchars('upload.php'); ?>" method="post" style="text-align: center">
                    <label>Content of your resume</label>
                    <textarea name="resume_content" class="description" style="height: 380px!important;" required></textarea>
                    <input type="submit" class="popup-submit" name="resume_submit" value="Upload">
                </form>
            </div>
        </div>

        <!-- Else the job posting form -->
    <?php } else if (isset($_SESSION['companyid'])) { ?>
        <div id="resume-job-upload" class="popup">
            <div class="popup-content" style="height: 600px!important;">
                <form action="<?php echo htmlspecialchars('upload.php'); ?>" method="post" style="text-align: center">
                    <input class="popup-input" name="new_job_title" placeholder="Title of job" required>
                    <select class="select-list" style="width: 80%; margin: 15px auto" name="new_target_program" required>
                        <option value="" disabled selected>Please choose a targeted program</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Software Engineering">Software Engineering</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                    </select>
                    <input class="popup-input" type="number" name="new_target_level" placeholder="Expected level of study" required>
                    <input class="popup-input" type="number" name="new_num_positions" placeholder="Number of positions available (optional)">
                    <input class="popup-input" type="date" name="new_closingdate" placeholder="Closing date of this job" required>
                    <textarea class="description" placeholder="Description of job" name="new_job_description" required></textarea>
                    <input type="submit" class="popup-submit" name="job_submit" value="Upload">
                </form>
            </div>
        </div>
    <?php } ?>

    <div id="top">
        <!-- The top search bar and filter -->
        <div id="top-search">
            <form style="display: inline" id="search-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get">
                <input type="search" name="search" placeholder="Job titles, company name or targeted program...">
                <input type="submit" name="submit_search" value="Search">
            </form>
            <button onclick="filter()" id="filter" type="button" title="Open filters for advanced search">
                <img src="images/adv-search.png" style="height: 35px;">
            </button>

            <!-- If no user or company has logged in -->
            <?php if (empty($_SESSION['userid']) && empty($_SESSION['companyid'])) { ?>
                <!-- Sign up and login button if no $_SESSION-->
                <div class="dropdown" style="float:right; margin: 15px 20px;">
                    <button id="signin-button">Login</button>
                    <div class="dropdown-content">
                        <a href="login.php?login_target=login_student" style="border-bottom: 1px ridge gainsboro;">For Students</a>
                        <a href="login.php?login_target=login_company">For Companies</a>
                    </div>
                </div>

                <div class="dropdown" style="float:right; margin: 15px 20px;">
                    <a id="signup" title="New user sign-up"">Sign up now!</a>
                    <div class="dropdown-content">
                        <a href="signup.php?signup_target=signup_student" style="border-bottom: 1px ridge gainsboro;">New Student</a>
                        <a href="signup.php?signup_target=signup_company">New Company (Employer)</a>
                    </div>
                </div>
            <!-- Else if a student has logged in -->
            <?php } else if (isset($_SESSION['userid'])) { ?>
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
                        <a style="color:red" id="logout" href="logout.php">Logout</a>
                    </div>
                </div>
                <!-- Else if a company has logged in -->
            <?php } else if (isset($_SESSION['companyid'])) { ?>
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
                        <a style="color:red" id="logout" href="logout.php">Logout</a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="filter-content">
            <span style="color: black; font-size: 15px; font-family: Verdana">Advanced Search:    </span>
            <select class="filter-select" form="search-form" name="filter">
                <option value="" selected>Select the order of results</option>
                <option value="Job_DESC">Popularity of Job in descending order</option>
                <option value="Job_ASC">Popularity of Job in ascending order</option>
                <option value="Com">Rating of Company in descending order</option>
            </select>
        </div>
    </div>

    <!-- The div for search results -->
    <div id="search-result">
        <div id="search-sum">
            <span class="summary-header">Jobs searched using keywords:
                <?php echo $_SESSION['search_keyword']; ?></span>

            <span class="summary-header" style="font-size: 15px; color: darkgray;">
                <?php echo count($search_result)." "; ?>results
            </span>
        </div>

        <div id="searched-jobs">
            <ul style="list-style: none">
                <!-- Test search result using PHP -->
                <?php foreach ($search_result as $row) { ?>
                <li href="#" onclick="getUserArray('<?php echo $row['companyid'];?>','<?php echo $row['title']; ?>')">
                    <div class="list-line">
                        <div class="line-left">
                            <span class="job-title"><b><?php echo $row['title']; ?></b></span>
                        </div>
                    </div>
                    <div class="list-line"> <!-- The second line -->
                        <div class="line-left">
                            <span class="company-and-program">
                                <?php echo $row['cname']."  "; ?><span style="color: darkred"><?php echo $row['totalrating']; ?></span>
                            </span>
                        </div>
                        <div class="line-right">
                            <span class="company-and-program" style="font-size: smaller; color: darkred;"><?php echo $row['targetedprogram']; ?></span>
                        </div>
                    </div>
                    <div class="list-line" style="height: 15px!important;"> <!-- The third line -->
                        <div class="line-left">
                            <span class="date"><?php echo "Open until: ".$row['closingdate']; ?> </span>
                        </div>
                        <div class="line-right">
                            <span class="date"><?php echo "Level of study: ".$row['targetedlevel']; ?></span>
                        </div>
                    </div>
                </li>
                <?php } ?>
            </ul>
        </div>

        <!-- The session to display job descriptions -->
        <div id="job-description">

        </div>
    </div>

    <script>
        // Function to toggle the display of filter
        function filter() {
            var filter_content = document.getElementById("filter-content");
            var search_div = document.getElementById("search-result");
            if (filter_content.style.display !== "block") {
                filter_content.style.display = "block";
                search_div.style.marginTop = "130px";
            } else {
                filter_content.style.display = "none";
                search_div.style.marginTop = "80px";
            }
        }

        // Show the content of job description
        function getUserArray(companyid,title) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState==4 && this.status==200) {
                    document.getElementById('job-description').innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "display_job.php?companyid="+companyid+"&title="+title, true);
            xmlhttp.send();
        }

        // Show the resume upload window
        function showUpload() {
            document.getElementById("resume-job-upload").style.display = "block";
        }

        function showApply(userid, companyid, title) {
            document.getElementById("apply-"+userid+"-"+companyid+"-"+title).style.display = "block";
        }

        function closeParent(id) {
            id.parentNode.parentNode.parentNode.style.display = "none";
        }
    </script>
    <script>
        var modal = document.getElementById("resume-job-upload");
        window.onclick = function(event) {
            if (event.target === modal) modal.style.display = "none";
        }
    </script>
</body>
</html>