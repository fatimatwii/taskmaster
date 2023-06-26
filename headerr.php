<?php
ob_start(); 
include('dbconfig/connect.php');
if (empty($_SESSION['user_info'])) {
header("Location: login.php");
exit();
}
$userid = $_SESSION['user_info']['id'];
$role = $_SESSION['user_info']['role'];
$headertype = '';
if ($role == 'user') {
$headertype = 'user';
} else {
$headertype = 'community';
}

$query = "select color_scheme from user_settings where user_id='$userid'";

$result = mysqli_query($con, $query);
if ($result && mysqli_num_rows($result) > 0) {
$row = mysqli_fetch_assoc($result);
$cssClass = ($row['color_scheme'] === 'dark') ? 'dark-navbar' : 'light-navbar';
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
<title><?php echo $title; ?></title>
<style>
.dark-navbar {
background-color: rgb(73, 73, 73);
}

.light-navbar {
background: linear-gradient(to left, #c9378e, #f5f4f4);
}

.navbar {
padding-top: 0% !important;
padding-bottom: 0% !important;
}

.navbar-brand {
font-family: Verdana, sans-serif;
font-size: 2rem !important;
font-weight: bolder;
padding-left: 2%;
color: #c9378e!important;
}

.nav-item {
padding: 0 10px;
}

.navbar-nav {
 margin-top: 0.5% !important;
}
.nav-link {
font-size: 1rem;
font-family: "Montserrat-Light";
}

.nav-link:hover {
color: #fa0075 !important;
}

button {
 background-color: #5e012c;
 border: #fa0075;
 padding: 1% 2%;
 margin-top: 1%;
 border-radius: 5px;
 margin-top: 2%;
 cursor: pointer;
  font-size: 1rem;
 }

button:hover {
background-color: #fa0075;
}

.icon-wrapper {
  display: flex;
 flex-direction: column;
  align-items: center;
  text-align: center;
}

.icon-title {
 margin-top: 5px;
}

.iconcolor {
color: #535353;
}
.iconcolor:hover{ transform: scale(1.2);
   box-shadow: 0 0 10px 5px white;
   transition: box-shadow 0.3s ease;
  }
  .icon-title:hover{transform: scale(1.2);
  }
  a:hover{
    text-decoration: none !important;
  }
</style>
<!-- Bootstrap CSS  font icon,bhoton hon mesh kel page-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body  >

<nav class="<?php echo $cssClass;?> navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href=""><?php echo $title; ?></a>
    <button style="background-color:#fa0075" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
      <?php if ($headertype=='user'){?>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link " href="dashboard.php">
          <div class="icon-wrapper"><i class="fas fa-home iconcolor"></i><p class="icon-title" style="font-size: 0.9rem; color:#535353 ;">Home</p></div></a></li>
        <li class="nav-item "><a class="nav-link" href="profile.php">
          <div class="icon-wrapper"><i class="fas fa-user iconcolor"></i><p class="icon-title" style="font-size: 0.9rem; color:#535353 ;">Manage Account</p></div></a></li>
          <li class="nav-item "><a class="nav-link" href="contact.php">
          <div class="icon-wrapper"><i class="fas fa-envelope iconcolor"></i><p class="icon-title" style="font-size: 0.9rem; color:#535353 ;">Contact Us</p></div></a></li>
          <li class="nav-item "><a class="nav-link" href="logout.php">
          <div class="icon-wrapper"><i class="fas fa-sign-out-alt iconcolor"></i>
          <p class="icon-title" style="font-size: 0.9rem; color: #535353;">Log Out</p>
        </div></a></li>
      </ul>
       <?php }
       else{?>
        <ul class="navbar-nav ms-auto">
        <li class="nav-item "><a class="nav-link" href="panel.php">
          <div class="icon-wrapper"><i class="fas fa-home iconcolor"></i><p class="icon-title" style="font-size: 0.9rem; color: #535353;">Home</p></div></a></li>
        <li class="nav-item "><a class="nav-link" href="profile.php">
          <div class="icon-wrapper"><i class="fas fa-user iconcolor"></i><p class="icon-title" style="font-size: 0.9rem; color: #535353;">Manage Account</p></div></a></li>
          <li class="nav-item "><a class="nav-link" href="contact.php">
          <div class="icon-wrapper"><i class="fas fa-envelope iconcolor"></i><p class="icon-title" style="font-size: 0.9rem; color:#535353 ;">Contact Us</p></div></a></li>
         
          <li class="nav-item ">
          <a class="nav-link" href="logout.php">
          <div class="icon-wrapper"><i class="fas fa-sign-out-alt  iconcolor"></i>
          <p class="icon-title" style="font-size: 0.9rem; color: #535353;">Log Out</p>
          </div>
         </a>
    </li>
      </ul>

      <?php }?>
    </div>
  </div>
</nav>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html> 