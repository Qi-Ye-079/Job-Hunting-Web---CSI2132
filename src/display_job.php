<?php
    session_start();
    require_once "connection.php";

    try {
        $q_userid = empty($_SESSION['userid']) ? "":$_SESSION['userid'];
        $q_companyid = $_REQUEST['companyid'];
        $q_title = $dbh->quote($_REQUEST['title']);
        $result = $dbh->query("SELECT title, cname, totalrating, targetedlevel, targetedprogram, jobdescription 
                             FROM job NATURAL JOIN company
                             WHERE companyid=$q_companyid AND title=$q_title")->fetch(PDO::FETCH_ASSOC);

        // Find if the user has review on this company
        $review_result = $dbh->query("SELECT e.*, r.comments, r.userid, r.reviewid FROM review r JOIN evaluation e ON r.evaid = e.evaluationid
                             WHERE r.companyid=$q_companyid")->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die($e->errorInfo[2]);
    }

    // If query executed, check result
    if (is_array($result)) {
        // The "Apply now" button will be activated only when a student logs in
        if (isset($_SESSION['userid'])) {
            $apply_button = '<div class="line-right">
                                <button class="button-in-cominfo" type="button" 
                                        onclick="showApply('.$q_userid.','.$q_companyid.','.$q_title.')">Apply now!</button>
                             </div>';
            $review_button = '<div class="line-right">
                                <a class="button-in-cominfo" type="button" 
                                        href="write_review.php?comid='.$q_companyid.'"
                                        style="background-color: dodgerblue; border: hidden;">Write a Review</a>
                             </div>';
        } else {
            $apply_button = '<div class="line-right">
                                <button class="button-in-cominfo" type="button" disabled>Apply now!</button>
                             </div>';
            $review_button = '<div class="line-right">
                                <button class="button-in-cominfo" type="button" disabled
                                        style="background-color: dodgerblue; border: hidden;">Write a Review</button>
                             </div>';
        }

        // Response with HTML
        echo '                
        <!-- the apply-for job form -->
        <div id="apply-'.$q_userid.'-'.$q_companyid.'-'.$_REQUEST['title'].'" class="popup" 
             style="float: none!important;">
            <div class="popup-content">
                <form id="apply-form-'.$q_userid.'-'.$q_companyid.'-'.$_REQUEST['title'].'" 
                    action="apply_job.php" method="post" style="text-align: center">
                    <label>Type your resume id to apply</label>
                    <input type="number" name="userid" value="'.$q_userid.'" hidden>
                    <input type="number" name="companyid" value="'.$q_companyid.'" hidden>
                    <input type="text" name="title" value="'.$_REQUEST['title'].'" hidden>
                    <input class="popup-input" type="number" name="resumeid" placeholder="Choose a resume by id" required>
                    <input class="popup-input" type="number" name="version" placeholder="And the resume\'s version" required>
                    <input type="submit" class="popup-submit" name="apply_job" value="APPLY">
                    <button type="button" class="popup-submit" onclick="closeParent(this)">Close</button>
                </form>
            </div>
        </div>
         
        <div id="com-info" class="info" style="height: 100px;border-top: hidden; overflow: hidden;">
                <div id="com-avatar">
                    <img src="images/company-avatar.png" style="height: 70px">
                </div>
                <div class="list-line">
                    <div class="line-left">
                        <span class="job-title" style="font-size: larger; color: black;"><b>'.$result['title'].'</b></span>
                    </div>'.
                    $apply_button.'
                </div>
                <div class="list-line"> <!-- The second line -->
                    <div class="line-left">
                        <span class="company-and-program" style="color: black">
                            '.$result['cname'].' - <span style="color: gray">average rating: '.$result['totalrating'].'</span>
                        </span>
                    </div>
                </div>
                <div class="list-line"> <!-- The third line -->
                    <div class="line-left">
                        <span class="company-and-program" style="color: darkred">
                            Desired level and program: '.$result['targetedlevel'].'th-year '.$result['targetedprogram'].'
                        </span>
                    </div>'.
                    $review_button.'
                </div>
            </div>
            <div id="job-info" class="info" style="height: 595px;margin-top: 5px;">
                '.$result['jobdescription'].'
            </div>
            <div>
        </div>';

        if (is_array($review_result)) {
            foreach ($review_result as $row) {
                $upvote_link = (empty($_SESSION['userid']) || $_SESSION['userid']==$row['userid'])?'':'<a href="upvote.php?reviewid='.$row['reviewid'].'" style="text-decoration: none">Upvote</a>';
                echo '
            <div id="com-info" class="info" style="height: 300px;overflow: hidden;">
                <div class="list-line">
                    <div class="line-left">
                        <span style="display: inline-block; color: darkred; font-size: larger">
                            Review from user ID: '.$row['userid'].'
                        </span>
                    </div>
                    <div class="line-right">
                        '.$upvote_link.'
                    </div>
                </div>
                <div class="list-line" style="border-top: 2px solid dodgerblue; padding-top: 15px">
                    <div class="line-left" style="font-size: 20px;color: coral">Salary: '.$row['salary'].'</div>
                    <div class="line-right" style="font-size: 20px;color: coral">Guidance: '.$row['guidance'].'</div>
                </div>
                <div class="list-line">
                    <div class="line-left" style="font-size: 20px;color: coral">Working Condition: '.$row['we'].'</div>
                    <div class="line-right" style="font-size: 20px;color: coral">Culture:  '.$row['culture'].'</div>
                </div>
                <div class="list-line">
                    <div class="line-left" style="font-size: 20px;color: coral">Schedule and Holiday: '.$row['sandh'].'</div>
                    <div class="line-right" style="font-size: 20px;color: coral">Colleagues: '.$row['colleagues'].'</div>
                </div>
                <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid dodgerblue; height: 100px; overflow: auto" >
                    '.$row['comments'].'
                </div>
            </div>';
            }
        }

    } else {
        echo "nothing";
    }