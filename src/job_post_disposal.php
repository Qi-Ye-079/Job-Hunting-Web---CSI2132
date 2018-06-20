<?php
    session_start();
    require_once "connection.php";

    // If update a job
    if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['job-update-button'])) {
        update_job($_SESSION['companyid'],
                   $_POST['old_title'],
                   $_POST['title_edit'],
                   $_POST['description_edit'],
                   $_POST['program_edit'],
                   $_POST['level_edit'],
                   $_POST['num_pos_edit'],
                   $_POST['closing_date_edit']);
    }
    // If delete a job
    elseif ($_SERVER['REQUEST_METHOD']=="GET" && isset($_GET['action'])) {
        delete_job($_SESSION['companyid'], $_GET['title']);
    }
    header("Location: posted_jobs.php");
    exit();

    /*
     * Update a resume's content
     */
    function update_job($comid, $old_title, $title, $description, $program, $level, $numPos, $closeDate) {
        global $dbh;

        $q_title = $dbh->quote($title);
        $q_oldtitle = $dbh->quote($old_title);
        $q_description = $dbh->quote($description);
        $q_program = $dbh->quote($program);
        $q_clsoingDate = $dbh->quote($closeDate);
        try {
            $dbh->query("UPDATE job 
                                   SET title=$q_title, jobdescription=$q_description, targetedprogram=$q_program,
                                       targetedlevel=$level, numberofpositions=$numPos, closingdate=$q_clsoingDate
                                   WHERE companyid = $comid AND title = $q_oldtitle");
        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }

    /*
     *  A company can also delete a posted job
     */
    function delete_job($comid,$title) {
        global $dbh;

        $q_title = $dbh->quote($title);
        try {
            $dbh->query("DELETE FROM Job WHERE CompanyId = $comid AND Title = $q_title;");
        } catch(PDOException $e) {
            die($e->errorInfo[2]);
        }
    }
