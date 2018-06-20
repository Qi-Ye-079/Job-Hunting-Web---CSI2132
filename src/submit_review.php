<?php
    session_start();
    require_once "connection.php";

    // If the form is submitted
    if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['submit_review'])) {

        $salary = $_POST['salary'];
        $guidance = $_POST['guidance'];
        $we = $_POST['we'];
        $culture = $_POST['culture'];
        $sandh = $_POST['sandh'];
        $colleagues = $_POST['colleagues'];

        $userid = $_SESSION['userid'];
        $companyid = $_POST['companyid'];
        $comment = $_POST['comment'];

        postReview($salary,$guidance,$we,$culture,$sandh,$colleagues, $userid, $companyid, $comment);
        header("Location: search_result.php");
        exit();

    }

    /*
     * A student can post review associated with an evaluation for a company.
     * Evaluation ID will be from the last added record in Evaluation, because an insert into Review
     * always follows an insert into Evaluation in our design.
     */
    function postReview($s,$g,$we,$c,$sh,$col, $userid,$comid,$comment) {
        global $dbh;

        $q_comment = $dbh->quote($comment);
        try {
            // First insert new evaluation
            $dbh->query("INSERT INTO evaluation(salary, guidance, we, culture, SandH, Colleagues) 
                                 VALUES ($s,$g,$we,$c,$sh,$col)");

            // Get the last inserted evaluation id
            $last_evaid = $dbh->lastInsertId();

            // Then bind the evaluation id and insert the review
            $dbh->query("INSERT INTO review(UserID, companyid, EvaID, Comments)
                                 VALUES ($userid, $comid, $last_evaid, $q_comment)");
        }  catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }