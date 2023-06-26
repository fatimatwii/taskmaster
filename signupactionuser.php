<?php
include('dbconfig/connect.php');
session_start();
$name = $_POST['username'];
$email = $_POST['email'];
$pass = $_POST['password'];
$hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
$image_name = $_FILES["image"]["name"];
$image_tmp = $_FILES["image"]["tmp_name"];
$uploads_dir = "userimage/";
$image_extension = pathinfo($image_name, PATHINFO_EXTENSION); 
$image_new_name = uniqid() . '.' . $image_extension; 
$image_path = $uploads_dir . $image_new_name;
move_uploaded_file($image_tmp, $image_path);
$confirmPass = $_POST['confirm-password'];

if ($pass !== $confirmPass) {
    header('Location: signup.php?error=password');
    exit();
}
$query = "SELECT * FROM user WHERE email='$email'";
$result = mysqli_query($con, $query);
if(mysqli_num_rows($result) > 0) {
  header('Location: signup.php?error=email');
  exit();
}
$query = "INSERT INTO user (username, email, password, image, role) VALUES ('$name', '$email', '$hashedPassword', '$image_path', 'user')";
$result = mysqli_query($con, $query);
$userId = mysqli_insert_id($con);
$new_image_name = $userId . '.' . $image_extension;
$new_image_path = $uploads_dir . $new_image_name;
rename($image_path, $new_image_path);
$update_query = "UPDATE user SET image = '$new_image_path' WHERE id = '$userId'";
mysqli_query($con, $update_query);

$settingsquery = "INSERT INTO user_settings (user_id, sort_order, color_scheme, email_notifications, sms_notifications) VALUES ('$userId', 'priority', 'light', 1, 0)";
mysqli_query($con, $settingsquery);

if ($result) {
    header('Location: login.php');
    exit();
  } else {
    header('Location: signup.php?error=registration');
    exit();
  }
?>
