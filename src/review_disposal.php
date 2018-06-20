<?php
    session_start();
    require_once "connection.php";

    // If update resume
    if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['review-update-button'])) {
        edit_review($_POST['reviewid'], $_POST['comments']);
    } elseif ($_SERVER['REQUEST_METHOD']=="GET" && isset($_GET['action'])) {
        // if delete a review
        if ($_GET['action']==="delete") {
            delete_review($_GET['reviewid']);
        }
    }
    header("Location: company_reviews.php");
    exit();

    /*
     * Update a review's content for a company
     */
    function edit_review($reviewid, $comments) {
        global $dbh;

        $q_comments = $dbh->quote($comments);
        try {
            $dbh->query("UPDATE review SET comments = $q_comments WHERE reviewid=$reviewid");
        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }

    /*
     * A student can make a reviwe request for one of his/her resumes to a moderator,
     * who is randomly assigned
     */
    function delete_review($reviewid) {
        global $dbh;

        try {
            $dbh->query("DELETE FROM review WHERE reviewid=$reviewid");
        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }
    }