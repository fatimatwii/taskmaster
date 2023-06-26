
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   
    <link rel="stylesheet" href="css/indexstyle.css">
    <title>Home page</title>

</head>
<body>
<!--header sentence-->
  <h3 class="header">"What you get by achieving your goals is not as important 
    as what you become by achieving your goals."- Zig Ziglar</h3> 
<div class="row" style="margin-left: 5%;">
  <!--title section-->
  <div class="col-lg-6 flex1">
   <!--welcome--> 
      <div class="big-heading">
        <h1 >Welcome to TASK MASTER</h1>
         <p>A simple and easy-to-use task manager that helps you stay organized.</p>
      </div>
      <!--tasks image-->
      <img class="title-image" src="images/1686514920646[294].png">
  </div>
<!--forms-->
 <div class="col-lg-6" style="WIDTH:40%">
  <!---log in-->
       <form action="loginaction.php" method="POST" class="log">
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="log-btn"><button type="submit" class="btn btn-dark " >Log In</button></div>
        <p class="text">
          <a href="javascript:void(0);" onclick="openForgotPassword()">Forget password?</a><br>
          <a href="signup.php">Don't have an account?</a>
        </p>
        
      </form>
   
  </div><!--end forms-->
  
</div>

<div class="task-details-window"  id="forget_password" style="display: none;">
<h6 style="color:#c26192d0;">Forget Password, enter your email to send code:</h6>
  <form method="POST" action="reset_password.php">
   <label for="email">Email:</label>
   <input type="email" id="resetemail" name="email" style="width: 90%;" required><br>
  <button type="submit" style="background-color:#c26192d0;margin-left:30%;margin-top:5%">Submit</button>
  </form>
</div>

<script>
function openForgotPassword() {
  var window = document.getElementById("forget_password");
  window.style.display = "block";
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
 </body>
  </html>
