<?php
session_start();
include('dbconfig/connect.php');

$email = $_POST["email"];
$password = $_POST["password"];

$query = "SELECT * FROM user WHERE email='$email'";
$result = mysqli_query($con, $query);

if(mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  $hashedPassword = $row['password'];

  if(password_verify($password, $hashedPassword)) {
    $_SESSION['user_info'] = $row;

    $role = $row['role'];
    if($role == 'user') {
      header('location: dashboard.php');
    }
    else {
      header('location: panel.php');
    }
  }
  else {
    header("location: login.php?flag=1");
  }
}
else {
  header("location: login.php?flag=1");
}
?>
