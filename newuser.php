<?php
session_start();
include('dbconfig/connect.php');
if (empty($_SESSION['user_info'])) {
header("Location: login.php");
exit();  }
$title = "New User";
require "headerr.php";?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
body {
  background-color: #f5f4f4;
}
form{
  text-align: center;
  margin-top: 3%;
}
label{
  margin-right: 5%;
  margin-top: 2%;
}
input{
 margin-top: 2%;
}
button {
  margin-top: 1%;
  background-color: #f855a6d0;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1rem;
}
button:hover {
background-color: #fa0075;
}
</style>
<title>New User</title>
</head>
<body>
<form method="post" action="newuseraction.php" enctype="multipart/form-data">
  <div class="division">
    <div class="chooseimage">
      <label for="image">Choose a profile picture:</label>
      <input type="file" id="image" name="image" accept="image/*" required>
    </div>

    <div class="info">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required><br>
      
      <label for="email" style="margin-right: 8%;">Email:</label>
      <input type="email" id="email" name="email"  required><br>
      
      <label for="password" style="margin-right: 6%;">Password:</label>
      <input type="password" id="password" name="password" required><br>
      
      <label for="confirm-password" style="margin-right: 2%;">Confirm Password:</label>
      <input type="password" id="confirm-password" name="confirm-password" required><br>
      <span id="password-match-status"></span><br>
      <label for="user_role" style="margin-top: 1%;">User Role:</label>
      <select name="user_role" style="margin-top: 1%;" required>
       
       <option>Project Manager</option>
       <option>Employee</option>
       </select>
    </div>
  </div>
  <div style="margin-top: 5%;"><button type="submit">Save</button>
  <a href="listuser.php" style="text-decoration: none;"><button type="button">Cancel</button></a>
 
</form>

<script>const passwordInput = document.getElementById('password');
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