<?php

    session_start();
    require_once "connection.php";
    //upvote.php?reviewid='.$row['reviewid'].'

    $this_userid = $_SESSION['userid'];
    $reviewid = $_REQUEST['reviewid'];

    global $dbh;
    try {
        $dbh->query("INSERT INTO upvote(userid, reviewid) VALUES($this_userid, $reviewid)");
        header("Location: search_result.php");
    } catch (PDOException $e) {
        die($e->errorInfo[2]);
    }