<?php
session_start();
include('dbconfig/connect.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredCode = $_POST['code'];
    $resetCode = $_SESSION['reset_code'];

    if ($enteredCode === $resetCode) {
        $_SESSION['verify_code'] = true;
        header("Location: newpassword.php");
        exit();
    } else {
         $error = "Invalid verification code";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
</head>
<body>
    <h1 style="color:#c26192d0;text-align:center;margin-top:10%">Verify Code:</h1>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <form action="verifycode.php" method="POST">
        <input type="text" id="code" name="code" style="width: 40%;margin-left:27%;margin-top:5%" required><br>
        <button type="submit" style="background-color:#c26192d0;margin-left:47%;margin-top:5%;width:7%;height:15%">Verify</button>
    </form>
</body>
</html>
