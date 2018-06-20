<?php
    session_start();
    require_once "connection.php";

    // If update resume
    if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['resume-review-button'])) {
        review_resume($_POST['review_userid'], $_POST['review_resumeid'], $_POST['review_version'], $_POST['old_contents'], $_POST['resume-comments']);
    }
    header("Location: resume_requests.php");
    exit();

    /*
     * Update a resume's content
     */
    function review_resume($userid, $resumeid, $version, $contents,$comments) {
        global $dbh;

        $new_version = $version + 1;
        $q_contents = $dbh->quote($contents);
        $q_comments = $dbh->quote($comments);
        try {
            $dbh->query("INSERT INTO resume (userid,resumeid,version,contents,comments)
                                   VALUES($userid, $resumeid, $new_version, $q_contents, $q_comments)");
            $dbh->query("UPDATE resume SET moderatorid=NULL WHERE userid=$userid AND resumeid=$resumeid AND version=$version");
        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }
