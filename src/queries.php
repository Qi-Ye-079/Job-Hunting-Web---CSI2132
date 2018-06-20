<?php
/**
 * Created by PhpStorm.
 * User: Yaeqa
 * Date: 2017/3/29
 * Time: 23:23
 */
    require_once "connection.php";

    $loginerr=$signuperr=$updateerr="";

    // If a request is made: process the request
    if ($_SERVER['REQUEST_METHOD']=="POST") {

        // Check if a student or company tries to log in
        if (isset($_POST['login_submit'])) {
            // When a student logs in
            if (isset($_POST['login_student']) && isset($_POST['login_psw'])) {
                studentLogin($_POST['login_student'], $_POST['login_psw']);
            }
            // When a company logs in
            else if (isset($_POST['login_company']) && isset($_POST['login_psw'])) {
                //echo "Company working email: ".$_POST['login_company']." Now it works";
                // Call function for company to login...
            }
        }
        // If a new student or company tries to sign up
        else if (isset($_POST['signup_submit'])) {
            // A new student signs up
            if (isset($_POST['ns_email']) &&
                isset($_POST['ns_uname']) &&
                isset($_POST['ns_password']) &&
                isset($_POST['ns_confirm_password']) &&
                isset($_POST['ns_level']) &&
                isset($_POST['ns_program'])) {
                //echo "Form received. Insert new user into database.";
                newStudentSignUp($_POST['ns_email'],$_POST['ns_uname'],$_POST['ns_confirm_password'],$_POST['ns_level'],$_POST['ns_program']);
            }
        }

        $_POST = array(); // Always clear $_POST array at the end of request
    }


    //================================== Belows are the queries (in functions) =====================================
    /*
     *  An exsiting student logs in.
     */
    function studentLogin($uname_email, $password) {
        global $dbh;

        // Properly escape the strings to query
        $u_email = $dbh->quote($uname_email);
        $psw = $dbh->quote($password);
        try {
            // First check if this user exist
            $fetch = ($dbh->query("SELECT * FROM student 
                                   WHERE (email=$u_email OR username=$u_email) AND password=$psw"))->fetch(PDO::FETCH_ASSOC);
            if (is_array($fetch)) {
                // Make the session here..
                $_SESSION['userid'] = $fetch['userid'];
                $_SESSION['email'] = $fetch['email'];
                $_SESSION['username'] = $fetch['username'];
                $_SESSION['password'] = $fetch['password'];
                $_SESSION['level'] = $fetch['study_level'];
                $_SESSION['program'] = $fetch['program'];
                $_SESSION['usertype'] = $fetch['usertype'];

                echo "User: ". $fetch['email']." has logged in.". "</br>";
                return;
            }

            // If no pair of email/username and password is found
            $GLOBALS['loginerr'] = "* Username or password is incorrect. Please try again.";
        } catch (PDOException $e) {
            echo "Error code: ". $e->errorInfo[0]. " and error message: ". $e->errorInfo[2]. "</br>";
        }
    }

    /*
     * A new Student signs up; violation of unique key constraint will be handled by PHP script.
     * Possible error code: 23505 (violation of key constraint)
     */
    function newStudentSignUp($email,$uname,$password,$level,$program) {
        global $dbh;
        $q_email = $dbh -> quote($email);
        $q_uname = $dbh -> quote($uname);
        $q_psw = $dbh -> quote($password);
        $q_level = (int)$level;
        $q_program = $dbh -> quote($program);

        try {
            $dbh -> query("INSERT INTO student(email,username,password,study_level,program) 
                                 VALUES ($q_email,$q_uname,$q_psw,$q_level,$q_program)");
        } catch(PDOException $e) {
            // If violation of unique key constraint
            if ($e->errorInfo[0]==23505) {
                $GLOBALS['signuperr'] = "* A user with the same email/username already exists.";
            } else {
                echo $e->errorInfo[2]. "</br>"; // For debugging use
            }
            die();
        }
    }

    /*
     *  A student can update his username, password, level or program (email cannot be changed)
     */
    function updateStudent($userid,$uname,$password,$level,$program) {
        global $dbh;
        $q_userid = $dbh->quote($userid);
        $q_uname = $dbh->quote($uname);
        $q_psw = $dbh->quote($password);
        $q_level = $dbh->quote($level);
        $q_program = $dbh->quote($program);

        try {
            $dbh->query("UPDATE student
                         SET username=$q_uname, password=$q_psw, study_level=$q_level, program=$q_program
                         WHERE userid = $q_userid");
        } catch (PDOException $e) {
            // If the username already exists
            if ($e->errorInfo[0]==23505) {
                $GLOBALS['updateerr'] = "* User name already exists. ";
            } else {
                echo $e->errorInfo[2]."</br>"; // For debugging use
            }
            die();
        }
    }

        /*
         * A student can upload a resume; This UserID will be acquired from PHP session, and
         * contents will be text input from User
         */
    function uploadResume($userid, $content) {
        global $dbh;

        $q_userid = $dbh->quote($userid);
        $q_content = $dbh->quote($content);

        try {
            $dbh->query("INSERT INTO resume(userid, contents) VALUES ($q_userid, $q_content)");
        } catch (PDOException $e) {
            echo $e->errorInfo[2]."</br>";
            die();
        }
    }

    /*
     * A student can apply for a job that has the same level as student with an uploaded resume.
     * All the values are acquired from $_SESSION
     */
    function applyJob($userid,$comid,$jobtitle,$resumeid,$resumever) {
        global $dbh;

        $q_userid = $dbh->quote($userid);
        $q_comid = $dbh->quote($comid);
        $q_jobtitle = $dbh->quote($jobtitle);
        $q_resumeid = $dbh->quote($resumeid);
        $q_resumever = $dbh->quote($resumever);

        try {
            $dbh->query("INSERT INTO applyfor(UserID, comid, jobtitle, resumeid, resumever)
                         VALUES ($q_userid,$q_comid,$q_jobtitle,$q_resumeid,$q_resumever)");
        } catch (PDOException $e) {
            echo $e->errorInfo[2]."</br>";
            die();
        }
    }

    /*
     * A student can post review associated with an evaluation for a company.
     * Evaluation ID will be from the last added record in Evaluation, because an insert into Review
     * always follows an insert into Evaluation in our design.
     */
    function postReview($s,$g,$we,$c,$sh,$col, $userid,$comid,$comment) {
        global $dbh;

        // Convert all values to int
        $salary = (int)$s;
        $guidance = (int)$g;
        $workingEnvironment = (int)$we;
        $culture = (int)$c;
        $SandH = (int)$sh;
        $colleagues = (int)$col;

        $q_userid = $dbh->quote($userid);
        $q_comid = $dbh->quote($comid);
        $q_comment = $dbh->quote($comment);

        try {
            // First insert new evaluation
            $dbh->query("INSERT INTO evaluation(salary, guidance, we, culture, SandH, Colleagues) 
                         VALUES ($salary,$guidance,$workingEnvironment,$culture,$SandH,$colleagues)");

            // Get the last inserted evaluation id
            $last_evaid = (int)($dbh->lastInsertId());

            // Then bind the evaluation id and insert the review
            $dbh->query("INSERT INTO review(UserID, companyid, EvaID, Comments)
                         VALUES ($q_userid, $q_comid, $last_evaid, $q_comment)");
        }  catch (PDOException $e) {
            echo "Error code: ". $e->errorInfo[0]. ", ". $e->errorInfo[2]."</br>";
            die();
        }
    }

    /*
     * A student can upvote a Review (only once)
     */
    function upvote($userid, $reviewid) {
        global $dbh;

        $i_userid = (int)$userid;
        $i_reviewid = (int)$reviewid;
        try {
            $dbh->query("INSERT INTO upvote(userid, reviewid) VALUES($i_userid, $i_reviewid)");
        } catch (PDOException $e) {
            echo $e->errorInfo[2] . "</br>";
            die();
        }
    }

    /*
     * A student can edit his review for a Company (within 30 days of posting)
     */
    function edit_review($userid, $comid, $comment) {
        global $dbh;

        $i_userid = (int)$userid;
        $i_comid = (int)$comid;
        $q_comment = $dbh->quote($comment);
        try {
            $dbh->query("UPDATE review
                         SET comments = $q_comment
                         WHERE userId = $i_userid and companyId = $i_comid");
        } catch (PDOException $e) {
            echo $e->errorInfo[2]."</br>";
            die();
        }
    }

    /*
     * A student can delete his review for a Company at any time
     * Note: the evaluation cannot be deleted nor updated.
     */
    function delete_review($userid, $comid) {
        global $dbh;

        $i_userid = (int)$userid;
        $i_comid = (int)comid;
        try {
            $dbh->query("DELETE FROM review WHERE UserId = $i_userid AND companyid = $i_comid;");
        } catch (PDOException $e) {
            die();
        }
    }

    /*
     *  A moderator can make comments on a resume that has requested review
     */
    function comment_resume($userid, $resumeid, $resumever, $comments) {
        global $dbh;

        $q_comments = $dbh->quote("$comments");
        try {
            $dbh->query("UPDATE Resume
                         SET comments = $q_comments
                         WHERE userid = $userid AND resumeid = $resumeid AND version = $resumever;");
        } catch(PDOException $e) {
            echo $e->errorInfo[2]."</br>";
            die();
        }
    }

    /*
     *  A company can post a new job
     */
    function post_job($comid,$title,$description,$targetProgram,$targetLevel,$numberOfPosition) {
        global $dbh;

        $q_title = $dbh->quote($title);
        $q_description = $dbh->quote($description);
        $q_targetedProgram = $dbh->quote($targetProgram);
        try {
            $dbh->query("INSERT INTO Job(CompanyId, title, description, targetedProgram, targetedLevel, numberOfPositions, ClosingDate); 
                              VALUES($comid,$q_title,$q_description,$q_targetedProgram,$targetLevel,$numberOfPosition);");
        } catch(PDOException $e) {
            echo $e->errorInfo[2]."</br>";
            die();
        }
    }

    /*
     *  Search for a job using keywords for job titles ,company names or targeted programs
     */
    function search_job($search, $filter_option) {
        global $dbh;

        if ($filter_option=="Job_DESC") $filter=" ORDER BY numberofapplicants DESC";
        elseif ($filter_option=="Job_ASC") $filter=" ORDER BY numberofapplicants ASC";
        elseif ($filter_option=="Com") $filter=" ORDER BY totalrating DESC";
        else $filter="";
        try {
            return $dbh->query("SELECT j.* FROM job j INNER JOIN company c ON j.companyid = c.companyid
                                    WHERE j.title LIKE '%$search%' OR 
                                          j.targetedProgram LIKE '%$search%' OR
                                          c.cname LIKE '%$search%' $filter");
        } catch(PDOException $e) {
            die($e->errorInfo[2]);
        }
    }








