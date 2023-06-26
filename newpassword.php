<?php
session_start();
include('dbconfig/connect.php');
if (empty($_SESSION['reset_email'])) {
    
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['verify_code']) || !$_SESSION['verify_code']) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
   if ($newPassword === $confirmPassword) {
        $email = $_SESSION['reset_email'];
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) > 0) {
            $query = "UPDATE user SET password = '$hashedPassword' WHERE email = '$email'";
            mysqli_query($con, $query);
            header("Location: login.php");
            exit();
        } else {
           
            $error = "Email does not exist";
        }
    } else {
        $error = "Passwords do not match";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Password</title>
</head>
<body>
    <h1 style="color:#c26192d0;text-align:center;margin-top:10%">Set New Password:</h1>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <form action="newpassword.php" method="POST">
        <label for="new_password" style="width: 40%;margin-left:27%;margin-top:5%">New Password:</label><br><br>
        <input type="password" id="new_password" style="width: 40%;margin-left:27%;margin-bottom:3%" name="new_password" required><br>
        <label for="confirm_password" style="width: 40%;margin-left:27%;margin-top:5%">Confirm Password:</label><br><br>
        <input type="password" id="confirm_password"  style="width: 40%;margin-left:27%;"name="confirm_password" required><br>
        <span id="password-match-status"></span><br>
        <button type="submit" style="background-color:#c26192d0;margin-left:47%;margin-top:5%;width:7%;height:15%">Submit</button>
    </form>
    <script>
 const passwordInput = document.getElementById('new_password');
 const confirmPasswordInput = document.getElementById('confirm_password');
 const passwordMatchStatus = document.getElementById('password-match-status');
 confirmPasswordInput.addEventListener('input', () => {
 const password = passwordInput.value;
 const confirmPassword = confirmPasswordInput.value;
 if (password === confirmPassword) {
   passwordMatchStatus.textContent = 'Passwords match.';
   passwordMatchStatus.style.color = 'green';
  } else {
   passwordMatchStatus.textContent = 'Passwords do not match.';
   passwordMatchStatus.style.color = 'red';
  }
  });
  passwordInput.addEventListener('input', () => {
  const password = passwordInput.value;
  const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
  if (passwordPattern.test(password)) {
   passwordInput.setCustomValidity('');
  } else {
   passwordInput.setCustomValidity('Password must be at least 8 characters long and contain both letters and numbers.');
  }
  });
</script>
</body>
</html>
