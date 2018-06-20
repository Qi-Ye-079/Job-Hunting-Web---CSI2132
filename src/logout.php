<?php
/**
 * This page simplys handles log out
 */
    session_start();
    session_destroy();
    unset($_GET['logout']);
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();