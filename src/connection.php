<?php
/**
 * Created by PhpStorm.
 * Author: Qi Ye, 7151574
 * Date: 2017/3/14
 * Time: 23:16
 * Description:
 *      This php handles the connection to database and is required for all pages
 */
    $host = 'web0.site.uottawa.ca';
    $port = '15432';
    $dbname = 'qye079';
    $username = 'qye079';
    $password = 'Residentevil1234';

    try {
        // Connect to database
        $dbh = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $dbh->query("SET search_path = 'CoopDatabase'");
        // Handle error by exception
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error: connection to database failed.</br>");
    }