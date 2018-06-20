<?php

    session_start();
    require_once "connection.php";

    /*
     *  This page simply handles the resume uploading
     */

    // If a resume is uploaded
    if ($_SERVER['REQUEST_METHOD']=="POST") {
        if (isset($_POST['resume_submit'])) {
            uploadResume($_SESSION['userid'], $_POST['resume_content']);
        } elseif (isset($_POST['job_submit'])) {
            postJob($_SESSION['companyid'],
                    $_POST['new_job_title'],
                    $_POST['new_job_description'],
                    $_POST['new_target_program'],
                    $_POST['new_target_level'],
                    $_POST['new_num_positions'],
                    $_POST['new_closingdate']);
        }
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    /*
     * A student can upload a resume; This UserID will be acquired from PHP session, and
     * contents will be text input from User
     */
    function uploadResume($userid, $content) {
        global $dbh;

        $q_content = $dbh->quote($content);
        try {
            $dbh->query("INSERT INTO resume(userid, contents) VALUES ($userid, $q_content)");
        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }

    /*
     *  A company can post a new job
     */
    function postJob($comid,$title,$description,$targetProgram,$targetLevel,$numberOfPosition,$closingdate) {
        global $dbh;

        $q_title = $dbh->quote($title);
        $q_description = $dbh->quote($description);
        $q_targetedProgram = $dbh->quote($targetProgram);
        $q_closingdate = $dbh->quote($closingdate);
        try {
            $dbh->query("INSERT INTO Job(CompanyId, title, jobdescription, targetedProgram, targetedLevel, numberOfPositions, ClosingDate) 
                                  VALUES($comid,$q_title,$q_description,$q_targetedProgram,$targetLevel,$numberOfPosition,$q_closingdate);");
        } catch(PDOException $e) {
            die($e->errorInfo[2]);
        }
    }