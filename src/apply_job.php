<?php

    session_start();
    require_once "connection.php";

    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['apply_job'])) {

        global $dbh;

        try {
            $q_userid = $_POST['userid'];
            $q_companyid = $_POST['companyid'];
            $q_title = $dbh->quote($_POST['title']);
            $q_resumeid = $_POST['resumeid'];
            $q_version = $_POST['version'];

            $result = $dbh->query("INSERT INTO applyfor(userid, comid, jobtitle, resumeid, resumever)
                                                    VALUES ($q_userid,$q_companyid,$q_title,$q_resumeid,$q_version)");
            header("Location: search_result.php");
            exit();

        } catch (PDOException $e) {
            die($e->errorInfo[2]);
        }

    }