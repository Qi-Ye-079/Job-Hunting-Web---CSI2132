<?php
    session_start();
    require_once "connection.php";

    // If update resume
    if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['resume-update-button'])) {
        edit_resume($_SESSION['userid'], $_POST['edit_resumeid'], $_POST['edit_version'], $_POST['edited-resume']);
    } elseif ($_SERVER['REQUEST_METHOD']=="GET" && isset($_GET['action'])) {
        // if delete a resume
        if ($_GET['action']==="delete") {
            delete_resume($_SESSION['userid'], $_GET['resumeid'], $_GET['version']);
        }
        // Else if make a request review
        elseif ($_GET['action']==='reviewrequest') {
            resume_review_request($_SESSION['userid'], $_GET['resumeid'], $_GET['version']);
        }
    }
    header("Location: uploaded_resumes.php");
    exit();

    /*
     * Update a resume's content
     */
    function edit_resume($userid, $resumeid, $version, $content) {
        global $dbh;

        $q_content = $dbh->quote($content);
        try {
            $dbh->query("UPDATE resume SET contents = $q_content
                                   WHERE userid = $userid AND resumeid = $resumeid AND version = $version");
        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }


    /*
     * Delete a resume
     */
    function delete_resume($userid, $resumeid, $version) {
        global $dbh;

        try {
            $dbh->query("DELETE FROM resume
                                 WHERE userid = $userid AND resumeid = $resumeid AND version = $version");
        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }

    /*
     * A student can make a reviwe request for one of his/her resumes to a moderator,
     * who is randomly assigned
     */
    function resume_review_request($userid, $resumeid, $version) {
        global $dbh;

        try {
            // Randomly select a moderator and get his/her id
            $fetch = ($dbh->query("SELECT userid FROM student WHERE usertype='M' ORDER BY random() LIMIT 1"))->fetch(PDO::FETCH_ASSOC);
            $moderatorid = $fetch['userid'];

            // Update the resume's moderator id
            $dbh->query("UPDATE resume
                             SET ModeratorID = $moderatorid
                             WHERE userId = $userid AND resumeid = $resumeid AND version = $version");
        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }
