<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/signupstyle.css">

    <title>Sign Up</title>
</head>
<body>
<?php
if(isset($_GET['error'])) {
    $error = $_GET['error'];
    if($error == 'email') {
      echo '<p class="error-message" style="font-weight: bold;font-size: 1rem;margin-top:5%;color:green">This email is already registered. Please use a different email.</p>';
    }
    elseif($error == 'registration') {
      echo '<p class="error-message" style="font-weight: bold;font-size: 1rem;margin-top:5%;color:red">Error occurred during user registration. Please try again.</p>';
    }
}
?>

<?php if(!empty($_GET['action']) && $_GET['action'] == 'user'):?>      
<form method="post" action="signupactionuser.php" enctype="multipart/form-data">
  <div class="division">
    <div class="chooseimage">
      <label for="image">Choose a profile picture:</label>
      <input type="file" id="image" name="image" accept="image/*"><br>
      <img id="image-preview" alt="Image Preview" style="max-width: 200px; display: none;margin-left:20%"><br>
    </div>

    <div class="info">
      <label for="username">Username:</label><br>
      <input type="text" id="username" name="username" required><br>
      
      <label for="email">Email:</label><br>
      <input type="email" id="email" name="email" required><br>
      
      <label for="password">Password:</label><br>
      <input type="password" id="password" name="password" required><br>
      
      <label for="confirm-password">Confirm Password:</label><br>
      <input type="password" id="confirm-password" name="confirm-password" required><br>
      <span id="password-match-status"></span><br>
    </div>
  </div>
  <div style="margin-top: 5%;"><button type="submit">Sign Up</button>
  <p>Already have an account? <a href="login.php">Log In</a></p></div>
</form>

<?php elseif(!empty($_GET['action']) && $_GET['action'] == 'communitycoordinator'):?>
  <form method="post" action="signupactioncommunitycoordinator.php" enctype="multipart/form-data">
  <div class="division">
    <div class="chooseimage">
      <label for="image">Choose a profile picture:</label>
      <input type="file" id="image" name="image" accept="image/*"><br>
      <img id="image-preview" alt="Image Preview" style="max-width: 200px; display: none;margin-left:20%"><br>

      
    <label for="community">Name your community:</label><br>
    <input type="text" id="community" name="community" required><br>
    
  </div>

    <div class="info">
      <label for="username">Username:</label><br>
      <input type="text" id="username" name="username" required><br>
      
      <label for="email">Email:</label><br>
      <input type="email" id="email" name="email" required><br>
      
      <label for="password">Password:</label><br>
      <input type="password" id="password" name="password" required><br>
      
      <label for="confirm-password">Confirm Password:</label><br>
      <input type="password" id="confirm-password" name="confirm-password" required><br>
      <span id="password-match-status"></span><br>
    </div>
  </div>
  <div style="margin-top: 5%;"><button type="submit">Sign Up</button>
  <p>Already have an account? <a href="login.php">Log In</a></p></div>
</form>
<?php else:?>
 <div style="text-align: center;margin-top:20%">
  <h1>SIGN UP US:</h1>
  <a href="signup.php?action=user" ><button type="submit" style="font-weight: bold;font-size: 1rem;">USER</button></a>
  <a href="signup.php?action=communitycoordinator" ><button type="submit" style="font-weight: bold;font-size: 1rem;width:25%">COMMUNITY COORDINATOR</button></a></div>
  <p>Already have an account? <a href="login.php">Log In</a></p></div>
  <?php endif;?>
<script>
 const passwordInput = document.getElementById('password');
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
document.getElementById('image').addEventListener('change', function(event) {
  var input = event.target;
  if (input.files && input.files[0]) {
  var reader = new FileReader();
  reader.onload = function(e) {
  var imageElement = document.getElementById('image-preview');
  imageElement.src = e.target.result; imageElement.style.display = 'block';
  };
  reader.readAsDataURL(input.files[0]);
 }
});



</script>

</body>
</html>

