<?php
    session_start();
    require_once "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome!!</title>
    <link rel="stylesheet" type="text/css" href="css/sign_form.css">
</head>
<body style="top: 0;">

<?php
    // The error message when login
    $signup_error="";

    // Determine type of user (student or company)
    if ($_SERVER['REQUEST_METHOD']=="GET" && isset($_GET['signup_target'])) {
        $_SESSION['signup_first'] = $_GET['signup_target']=="signup_student"?"Email address":"Company's Working Email";
        $_SESSION['signup_second'] = $_GET['signup_target']=="signup_student"?"User name":"Company name";
    }

    // If trying to signup
    if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['signup_submit'])) {

        if ($_POST['ns_password'] !== $_POST['ns_confirm_password']) {
            // Check if password is confirmed
            $GLOBALS['signup_error'] = "* Password confirmation doesn't match.";
        } else {
            $program = empty($_POST['ns_program'])?"":$_POST['ns_program'];
            sign_up($_POST['ns_email'], $_POST['ns_uname'], $_POST['ns_password'], $_POST['ns_num'], $program);
            if (empty($signup_error)) {
                unset($_SESSION['signup_first']);
                unset($_SESSION['signup_second']);
                header("Location: search_result.php");
                exit();
            }
        }
    }

    /*
     * A new Student signs up; violation of unique key constraint will be handled by PHP script.
     * Possible error code: 23505 (violation of key constraint)
     */
    function sign_up($email,$uname,$password,$level,$program) {
        global $dbh;
        $q_email = $dbh -> quote($email);
        $q_uname = $dbh -> quote($uname);
        $q_psw = $dbh -> quote($password);

        try {
            // If this is a company signing in
            if (empty($program)) {
                $dbh->query("INSERT INTO company(cname,workemail,password,numberofemployees) 
                                       VALUES ($q_uname,$q_email,$q_psw,$level)");
            } else {
                $q_program = $dbh -> quote($program);
                $dbh -> query("INSERT INTO student(email,username,password,study_level,program) 
                                 VALUES ($q_email,$q_uname,$q_psw,$level,$q_program)");
            }
        } catch(PDOException $e) {
            // If violation of unique key constraint
            if ($e->errorInfo[0]==23505) {
                $GLOBALS['signup_error'] = "* A user with the same email/username already exists.";
            } else {
                die($e->errorInfo[2]);
            }
        }
    }
?>

<!-- The sign up form -->
<div class="signup">

    <!-- The sign up window -->
    <div class="signup-content" style="margin: 5% auto;">
        <h1 style="margin-top: 8%; font-family: 'Arial Black'; text-align: center;"><b>CREATE ACCOUNT</b></h1>

        <form id="signup_form" style="padding: 16px;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
            <input class="signup-input" type="email" name="ns_email" required
                   placeholder="<?php if (empty($_SESSION['signup_first'])) echo "Email address";
                                      else echo $_SESSION['signup_first']; ?>"
                   oninvalid="this.setCustomValidity('Please enter a correct email')"
                   oninput="this.setCustomValidity('')"><br>

            <input class="signup-input" type="text" name="ns_uname" required
                   placeholder="<?php if (empty($_SESSION['signup_second'])) echo "User name";
                                      else echo $_SESSION['signup_second']; ?>"
                   oninvalid="this.setCustomValidity('Please enter a valid name')"
                   oninput="this.setCustomValidity('')"><br>

            <input class="signup-input" type="password" name="ns_password" required
                   placeholder="Password"
                   oninvalid="this.setCustomValidity('Please enter your password')"
                   oninput="this.setCustomValidity('')"><br>

            <input class="signup-input" type="password" name="ns_confirm_password" required
                   placeholder="Confirm your password"
                   oninvalid="this.setCustomValidity('Please confirm your password')"
                   oninput="this.setCustomValidity('')"><br>

            <span class="error"><?php echo $signup_error; ?></span>

            <?php // If a new student tries to sign in
                   if (empty($_SESSION['signup_first']) || $_SESSION['signup_first']=="Email address") {  ?>
            <select class="select-list" name="ns_num" required
                    oninvalid="this.setCustomValidity('Please choose your current level of study')"
                    oninput="this.setCustomValidity('')">
                <option value="" disabled selected>Choose your level of study</option>
                <option value="1">1st year</option>
                <option value="2">2nd year</option>
                <option value="3">3rd year</option>
                <option value="4">4th year</option>
            </select><br>

            <select class="select-list" name="ns_program" required
                    oninvalid="this.setCustomValidity('Please choose your program of study')"
                    oninput="this.setCustomValidity('')">
                <option value="" disabled selected>Choose your program</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Computer Engineering">Computer Engineering</option>
                <option value="Software Engineering">Software Engineering</option>
                <option value="Electrical Engineering">Electrical Engineering</option>
            </select><br>

            <?php  // Else if a company wants to sign in
                   } else { ?>
            <input class="signup-input" type="number" name="ns_num" required
                   placeholder="Please enter your current number of employees"
                   oninvalid="this.setCustomValidity('Please enter a valid integer number')"
                   oninput="this.setCustomValidity('')"><br>
            <?php } ?>


            <p><input type="checkbox" required oninvalid="this.setCustomValidity('You must check this box to sign up')"
                   onchange="this.setCustomValidity('')">I agree with some agreements...</p></br>

            <input class="signup-submit" type="submit" name="signup_submit" value="SIGN UP!">
        </form>
    </div>
</div>
</body>
</html>