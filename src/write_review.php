<?php
    session_start();
    require_once "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Write Review</title>
    <link rel="stylesheet" type="text/css" href="css/write_review.css">
</head>
<body>
<?php
    //href="write_review.php?comid='.$q_companyid.'&title='.$q_title.'"
    $companyid = $_REQUEST['comid'];
?>
<div id="centre">
    <div id="review-section">
        <h1>User name: <?php echo $_SESSION['username']; ?></h1>
        <h1>Reviewing company ID: <?php echo $companyid; ?></h1>

        <form id="review-form" action="submit_review.php" method="post">
            <div class="evaluation">
                <label class="evaluation-header"><b>Salary:</b></label>
                <spa>1</spa><input class="scroll-bar" name="salary" type="range" min="1" max="5" step="1"><spa>5</spa>
                <label class="evaluation-header"><b>Guidance:</b></label>
                <spa>1</spa><input class="scroll-bar" name="guidance" type="range" min="1" max="5" step="1"><spa>5</spa>
                <label class="evaluation-header"><b>Working Condition:</b></label>
                <spa>1</spa><input class="scroll-bar" name="we" type="range" min="1" max="5" step="1"><spa>5</spa>
                <label class="evaluation-header"><b>Culture:</b></label>
                <spa>1</spa><input class="scroll-bar" name="culture" type="range" min="1" max="5" step="1"><spa>5</spa>
                <label class="evaluation-header"><b>Schedule and Holiday:</b></label>
                <spa>1</spa><input class="scroll-bar" name="sandh" type="range" min="1" max="5" step="1"><spa>5</spa>
                <label class="evaluation-header"><b>Colleagues:</b></label>
                <spa>1</spa><input class="scroll-bar" name="colleagues" type="range" min="1" max="5" step="1"><spa>5</spa>
            </div>
            <div class="review">
                <h1>Please provide your valuable opinion about this company below:</h1>
                <textarea class="comment" name="comment" required></textarea>
                <input type="text" name="companyid" value="<?php echo $companyid; ?>" hidden>
            </div>
            <div style="clear: both; display: block; text-align: center">
                <input class="review-submit" type="submit" name="submit_review" value="SUBMIT REVIEW">
            </div>
        </form>

    </div>
</div>
</body>
</html>