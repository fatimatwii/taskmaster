<?php session_start();
include('dbconfig/connect.php');
if (empty($_SESSION['user_info'])) {
header("Location: login.php");
exit();  }
 $title = "Settings";
 $userid=$_SESSION['user_info']['id'];
 require_once "headerr.php";
 $query="select * from user_settings where user_id='$userid'";
 $result = mysqli_query($con, $query);
 if ($result && mysqli_num_rows($result) > 0) {
 $row = mysqli_fetch_assoc($result);
 $_SESSION['user_settings']=$row;
 $cssbutton = ($row['color_scheme'] === 'dark') ? 'darkbutton' : 'lightbutton';
 }

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'todopre'){

  $sort_order = $_POST['sorting-order'];
  $color_scheme = $_POST['theme'];
  if(empty($sort_order) && !empty($color_scheme))
  {
    $query = "UPDATE user_settings SET  color_scheme = '$color_scheme' WHERE user_id = '$userid'";
  } 
  elseif(!empty($sort_order) && empty($color_scheme))
  {
     $query = "UPDATE user_settings SET sort_order = '$sort_order' WHERE user_id = '$userid'";  
  }
  else{
    $query = "UPDATE user_settings SET sort_order = '$sort_order', color_scheme = '$color_scheme' WHERE user_id = '$userid'";
  }
  $result = mysqli_query($con, $query);
   header("Location: setting.php");
}
elseif($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'changepass'){
   $confirm_password = $_POST['confirm_password'];
   if(!empty($confirm_password))
 {
  $hashedPassword = password_hash($confirm_password, PASSWORD_DEFAULT);
  $query="update user set password='$hashedPassword' where id='$userid'";
  $result=mysqli_query($con,$query);
  header("Location: setting.php");
  die;
 }
 else{
  header("Location: setting.php");
 }
  
}
elseif($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'notification'){
  $email_notifications = isset($_POST['email-notifications']);
  $query="update user_settings SET email_notifications = '$email_notifications' WHERE user_id = $userid";
  $result=mysqli_query($con,$query);
  header("Location: setting.php");
  die;
}?>
<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="css/settingstyle.css">
    <title>Settings</title>
</head>
<body>
<?php if(!empty($_GET['action']) && $_GET['action'] == 'todopre'):?>
  <div class="settings-container">
    <h3>To-Do List Preferences</h3>
  <form method="post">
    <label for="sorting-order">Default Sorting Order:</label>
    <select  id="sorting-order" name="sorting-order" style="font-size: 1rem;" >
      <option value=""></option>
      <option value="due-date"<?php echo ($_SESSION['user_settings']['sort_order'] === 'due-date') ? ' selected' : ''; ?>>Due Date</option>
      <option value="priority"<?php echo ($_SESSION['user_settings']['sort_order'] === 'priority') ? ' selected' : ''; ?>>Priority</option>
    </select>
    <br>
    <label for="theme">Theme:</label>
    <select id="theme" name="theme" style="font-size: 1rem;">
    <option value=""></option>
    <option value="light"<?php echo ($_SESSION['user_settings']['color_scheme'] === 'light') ? ' selected' : ''; ?>>Light</option>
    <option value="dark"<?php echo ($_SESSION['user_settings']['color_scheme'] === 'dark') ? ' selected' : ''; ?>>Dark</option>
    </select>

    <br><input type="hidden" name="action" value="todopre">
  <button class="<?php echo $cssbutton; ?>" >Save Changes</button></div>
  </form> 
<?php elseif(!empty($_GET['action']) && $_GET['action'] == 'changepass'):?>
  <div class="settings-container">
  <h3>Change Password</h3>
  <form method="post">
  <br>
  <label for="new-password">New Password:</label>
  <input type="password" id="new-password" name="new-password">
  <br>
  <label for="confirm-password">Confirm Password:</label>
  <input type="password" id="confirm-password" name="confirm_password"><br>
  <span id="password-match-status"></span><br>
  <br>
  <input type="hidden" name="action" value="changepass">
  <button class="<?php echo $cssbutton; ?>" type="submit">Save Changes</button></form></div>

<?php elseif(!empty($_GET['action']) && $_GET['action'] == 'notification'):?>
  <div class="settings-container" style="background-color: white;margin-top: 5%;padding-bottom:5%">
  <h3>Notification Settings</h3>
  <form method="post">
  Email Notifications: <input type="checkbox" id="email-notifications" name="email-notifications"<?php echo ($_SESSION['user_settings']['email_notifications']) ? ' checked' : ''; ?>>
  <br><br>
  <br><input type="hidden" name="action" value="notification">
  <button class="<?php echo $cssbutton; ?>" type="submit">Save Changes</button> </form></div>

<?php else:?>
<div class="settings-container">
<div class="settings-section">
<a href="setting.php?action=todopre"><h2>To-Do List Preferences</h2></a>
<a href="setting.php?action=changepass"><h2>Change Password</h2></a>
<a href="setting.php?action=notification"><h2>Notification Settings</h2></a>
</div>
<a href="profile.php"><button type="button" class="<?php echo $cssbutton; ?>">Back to my Account</button></a>
</div>
<?php endif;?>

<script>
 const passwordInput = document.getElementById('new-password');
 const confirmPasswordInput = document.getElementById('confirm-password');
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