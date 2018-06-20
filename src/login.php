<?php
    session_start();
    require_once "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome back!</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body style="top: 0;">

<?php
    // The error message when login
    $loginerr="";

    // If go into this page directly, login in as student by default
    $_SESSION['first_input']="Username or Email";

    // Determine type of user (student or company)
    if ($_SERVER['REQUEST_METHOD']=="GET" && isset($_GET['login_target'])) {
        $_SESSION['first_input'] = $_GET['login_target']=="login_student"?"Username or Email":"Working Email";
    }

    // If trying to login
    if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['login_submit'])) {
        login($_POST['login_first'], $_POST['login_psw']);
        if (empty($loginerr)) {
            // Head back to previous page...
            unset($_SESSION['first_input']);
            header("location: search_result.php");
        }
    }

    /*
     *  Function to login
     */
    function login($uname_email, $password) {
        global $dbh;

        // Properly escape the strings to query
        $u_email = $dbh->quote($uname_email);
        $psw = $dbh->quote($password);
        try {
            // First check if such a student exist
            $fetch = ($dbh->query("SELECT * FROM student 
                                   WHERE (email=$u_email OR username=$u_email) AND password=$psw"))->fetch(PDO::FETCH_ASSOC);
            if (is_array($fetch)) {
                // Make the session here..
                $_SESSION['userid'] = $fetch['userid'];
                $_SESSION['email'] = $fetch['email'];
                $_SESSION['username'] = $fetch['username'];
                $_SESSION['password'] = $fetch['password'];
                $_SESSION['level'] = $fetch['study_level'];
                $_SESSION['program'] = $fetch['program'];
                $_SESSION['usertype'] = $fetch['usertype'];

                return;
            }

            // If not student, check if such company exist
            $fetch = ($dbh->query("SELECT * FROM company 
                                   WHERE workemail=$u_email AND password=$psw"))->fetch(PDO::FETCH_ASSOC);
            if (is_array($fetch)) {
                // Make the session here..
                $_SESSION['companyid'] = $fetch['companyid'];
                $_SESSION['cname'] = $fetch['cname'];
                $_SESSION['workemail'] = $fetch['workemail'];
                $_SESSION['password'] = $fetch['password'];
                $_SESSION['numberofemployees'] = $fetch['numberofemployees'];
                $_SESSION['totalrating'] = $fetch['totalrating'];
                $_SESSION['numberofreviewwer'] = $fetch['numberofreviewwer'];

                return;
            }

            // If no pair of email/username and password is found
            $GLOBALS['loginerr'] = "* Username or password is incorrect. Please try again.";
        } catch (PDOException $e) {
            die("Error occurred while quering database.");
        }
    }
?>

<div style="position: absolute; top: 0; z-index: -1;">
    <?php if (empty($_SESSION['first_input']) || $_SESSION['first_input']=='Username or Email') { ?>
    <img class="background-image" src="images/jobs.jpeg">
    <?php } else { ?>
    <img class="background-image" src="images/hiring.jpeg">
    <?php } ?>
</div>

<div id="login">

    <!-- The login form window -->
    <div id="login-content">
        <h1 style="text-align: center; font-size: 40px; color: black;">WELCOME
            <span style="color: limegreen;">BACK</span>!</h1>

        <form style="padding: 16px;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <label><b><?php echo $_SESSION['first_input']; ?></b></label><br>
            <input class="login-input" type="text" name="login_first" required
                   oninput="this.setCustomValidity('')"><br>

            <label><b>Password</b></label><br>
            <input class="login-input" type="password" name="login_psw" required
                   oninvalid="this.setCustomValidity('Enter Your password')"
                   oninput="this.setCustomValidity('')"><br>

            <span class="error"><?php echo $loginerr; ?></span>

            <input class="login-submit" type="submit" name="login_submit" value="Login">
        </form>
    </div>
</div>
</body>
</html>