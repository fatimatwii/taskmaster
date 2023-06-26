<?php session_start();
include('dbconfig/connect.php');
if (empty($_SESSION['user_info'])) {
header("Location: login.php");
exit();  }
$title = "My Profile";
$userrole=$_SESSION['user_info']['role'];
$userid=$_SESSION['user_info']['id'];
require_once "headerr.php";
$query="select color_scheme from user_settings where user_id='$userid'";
$result = mysqli_query($con, $query);
if ($result && mysqli_num_rows($result) > 0) {
$row = mysqli_fetch_assoc($result);
$cssClass = ($row['color_scheme'] === 'dark') ? 'dark-body' : 'light-body';
$cssbutton = ($row['color_scheme'] === 'dark') ? 'darkbutton' : 'lightbutton';
}
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'delete' )
{  $id = $_SESSION['user_info']['id'];
    // user settings for this user
   $sqlsettings="delete from user_settings where user_id='$id'";
   $result = mysqli_query($con,$sqlsettings);
   // delete user's tasks
   $sqldeletetasks="DELETE FROM task WHERE user_id = '$id';";
   $result = mysqli_query($con, $sqldeletetasks);
   // delete project for admin: tasks, members, managers employees
   if($userrole=='Community Admin'){
    // delete admin's community
    $sqlcommunity="delete from community where communityadmin_id='$id'";
    $communityresult = mysqli_query($con,$sqlcommunity);
    // delete users that the admin created , settings
    $sqlidusers = "SELECT id FROM user WHERE communityadmin_id = '$id'";
    $result = mysqli_query($con, $sqlidusers);
     if ($result) {
     if (mysqli_num_rows($result) > 0) {
     while ($row = mysqli_fetch_assoc($result)) {
     $memberid = $row['id'];
     // users's settings
     $sqlsettings = "DELETE FROM user_settings WHERE user_id = '$memberid'";
     $settingsResult = mysqli_query($con, $sqlsettings);
     if (!$settingsResult) {
     echo "Error deleting user settings: " . mysqli_error($con);
     }
     }
     }
     } else {
     echo "Error executing query: " . mysqli_error($con);
     }
     // user's
   $sqlusers = "DELETE FROM user WHERE communityadmin_id = '$id'";
   $result = mysqli_query($con, $sqlusers);
   // select project id
   $sqlid = "select id FROM project_list WHERE communityadmin_id= '$id'";
   $result = mysqli_query($con, $sqlid);
   $projectid='';
   if (mysqli_num_rows($result) > 0) {
   while ($row = mysqli_fetch_assoc($result)) {
   $projectId = $row['id'];
   // Delete project's members
   $sqlmembers = "DELETE FROM project_members WHERE project_id = '$projectId'";
   $resultMembers = mysqli_query($con, $sqlmembers); 
   // Delete project's tasks
   $sqldeletetasks = "DELETE FROM task WHERE project_id = '$projectId'";
   $resultTasks = mysqli_query($con, $sqldeletetasks); 
   // Delete project for this manager
   $sqldeleteprojects = "DELETE FROM project_list WHERE communityadmin_id = '$id'";
   $resultProjects = mysqli_query($con, $sqldeleteprojects); 
   }
   }}
   if(file_exists($_SESSION['user_info']['image'])){
   unlink($_SESSION['user_info']['image']);
   }
   // delete collaboration and chats
   $sqldeletecollaboration = "DELETE FROM collaboration WHERE project_manager_id = '$id'";
   $resultcollaboration = mysqli_query($con, $sqldeletecollaboration); 
   $sqldeletechats = "DELETE FROM chat WHERE sender_id = '$id'";
   $resultchats = mysqli_query($con, $sqldeletechats); 
   
   $query = "delete from user where id = '$id'";
   $result = mysqli_query($con,$query);
   session_destroy();
   header("Location: login.php");
   die;
  
}
elseif($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'edit' ){
   $image_added = false;
   if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
   $allowedTypes = array('image/jpeg', 'image/png', 'image/gif', 'image/jpg');
   $fileInfo = getimagesize($_FILES['image']['tmp_name']);
   $fileType = $fileInfo['mime'];
   if (in_array($fileType, $allowedTypes)) {
   $folder = "userimage/";
   if (!file_exists($folder)) {
    mkdir($folder, 0777, true);
   }
    
    $image = $folder . $_FILES['image']['name'];
    $image_extension = pathinfo($image, PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['image']['tmp_name'], $image);
    if (file_exists($_SESSION['user_info']['image'])) {
    unlink($_SESSION['user_info']['image']);
    }
    $image_added = true;
    }
   }
   $username = addslashes($_POST['username']);
   $email = addslashes($_POST['email']);
   $query = "SELECT * FROM user WHERE email='$email' and id <> '$userid'";
   $result = mysqli_query($con, $query);
   if(mysqli_num_rows($result) > 0) {
   header('Location: profile.php?error=email');
   exit();
   }
   $id = $userid;
   if ($image_added) {
       $query = "UPDATE user SET username = '$username', email = '$email', image = '$image' WHERE id = '$id' LIMIT 1";
   } else {
       $query = "UPDATE user SET username = '$username', email = '$email' WHERE id = '$id' LIMIT 1";
   }
   $result = mysqli_query($con, $query);
   // Rename the image 
    $new_image_name = $userid . '.' . $image_extension;
    $new_image_path = $folder . $new_image_name;
    rename($image, $new_image_path);
    $update_query = "UPDATE user SET image = '$new_image_path' WHERE id = '$userid'";
    mysqli_query($con, $update_query);
   $query = "SELECT * FROM user WHERE id = '$id' LIMIT 1";
   $result = mysqli_query($con, $query);
   if (mysqli_num_rows($result) > 0) {
   $_SESSION['user_info'] = mysqli_fetch_assoc($result);
   }
   if ($result) {
    header('Location: profile.php');
    exit();
  } else {
    header('Location: profile.php?error=registration');
    exit();
  }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
 <link rel="stylesheet" href="css/profilestyle.css">
 <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
 <title>Profile</title>
</head>
<body class="<?php echo $cssClass; ?>">
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
<div>
<?php if(!empty($_GET['action']) && $_GET['action'] == 'edit'):?>
 <div class="container">
 <form method="post"  enctype="multipart/form-data">
    <img src="<?php echo $_SESSION['user_info']['image']?>"><br><br>
    Profile Picture: <input type="file" name="image" accept="image/*"><br>
         <input value="<?php echo $_SESSION['user_info']['username']?>" type="text" name="username" placeholder="Username" required><br>
         <input value="<?php echo $_SESSION['user_info']['email']?>" type="email" name="email" placeholder="Email" required><br>
         <input type="hidden" name="action" value="edit">
         <button class="<?php echo $cssbutton; ?>">Save</button>
         <a href="profile.php"><button type="button" class="<?php echo $cssbutton; ?>">Cancel</button></a>
 </form>
 </div>
<?php elseif(!empty($_GET['action']) && $_GET['action'] == 'delete'):?>
    <div class="container">
    <h2 class="username">Are you sure you want to delete your profile?!</h2>
   <form method="post">
        <img src="<?php echo $_SESSION['user_info']['image']?>">
        <div class="username"><?php echo $_SESSION['user_info']['username']?></div>
        <div class="useremail"><?php echo $_SESSION['user_info']['email']?></div>
        <input type="hidden" name="action" value="delete">
        <button class="<?php echo $cssbutton; ?>">Delete</button>
       <a href="profile.php"><button type="button" class="<?php echo $cssbutton; ?>">Cancel</button></a>
        
    </form>
 </div>
<?php else:?>
 <div class="container">
     <td><img src="<?php echo $_SESSION['user_info']['image']?>"></td>
         <br><br>
    <div class="username">
        <td ><?php echo $_SESSION['user_info']['username']?></td><br> 
    </div>
    <div class="useremail">
         <td ><?php echo $_SESSION['user_info']['email']?></td>
    </div>
           <br><br>

    <a href="profile.php?action=edit"><button class="<?php echo $cssbutton; ?>">Edit profile</button></a>
    <?php if ($userrole == 'Community Admin' || $userrole == 'user'){?>
    <a href="profile.php?action=delete"><button class="<?php echo $cssbutton; ?>">Delete profile</button></a><br><br>
     <?php }?>
    <div class="profile-settings" >
     <a href="setting.php" style="text-decoration: none;color:#b60155;">
     <i class="fas fa-cogs fa-lg" style="color:#b60155;"></i>
        Settings</a>
    </div>
 </div>
 </div>

 <?php endif;?> 
  
</body>
</html>