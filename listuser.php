<?php
session_start();
include('dbconfig/connect.php');
if (empty($_SESSION['user_info'])) {
header("Location: login.php");
exit(); }
$userrole=$_SESSION['user_info']['role'];
$adminId=$_SESSION['user_info']['communityadmin_id'];
if($userrole=='Community Admin')
{$title = "User List";}
else{
  $title="Members Activity";
}
require "headerr.php";
// admin delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteuser') {
  $id = $_POST['user-id'];
  // delete image
  $sqlimg = "select image,role FROM user WHERE id = '$id'";
  $resultimg = mysqli_query($con, $sqlimg);
  $userimage='';$deleteduserrole='';
  if(mysqli_num_rows($resultimg) > 0) {
  $rowimg = mysqli_fetch_assoc($resultimg);
  $userimage = $rowimg['image'];
  $deleteduserrole=$rowimg['role'];
  }
  if(file_exists($userimage)){
  unlink($userimage);
  }
  // delete user settings
  $sqlsettings = "delete from user_settings where user_id='$id'";
  $result = mysqli_query($con, $sqlsettings);
  // 
  if (isset($_POST['deleteTasksProjects']) && $_POST['deleteTasksProjects'] === 'on') {
  if ($deleteduserrole == 'Project Manager') {
  $sqlimg = "SELECT id FROM project_list WHERE manager_id = '$id'";
  $result = mysqli_query($con, $sqlimg);
  if (mysqli_num_rows($result) > 0) {
  $rowid = mysqli_fetch_assoc($result);
  $projectid = $rowid['id'];
  // delete project's members
  $sqlmembers = "DELETE FROM project_members WHERE project_id = '$projectid'";
  $result = mysqli_query($con, $sqlmembers);
  // delete project's tasks
  $sqldeletetasks = "DELETE FROM task WHERE project_id = '$projectid'";
  $result = mysqli_query($con, $sqldeletetasks);
  // delete project for this manager
  $sqldeleteprojects = "DELETE FROM project_list WHERE manager_id = '$id'";
  $result = mysqli_query($con, $sqldeleteprojects);
  // delete collaboration and chats
  $sqldeletecollaboration = "DELETE FROM collaboration WHERE project_manager_id = '$id'";
  $resultcollaboration = mysqli_query($con, $sqldeletecollaboration); 
  }
  } 
  }
  $sqldeletechats = "DELETE FROM chat WHERE sender_id = '$id'";
   $resultchats = mysqli_query($con, $sqldeletechats); 
  $sqldeletetasks = "DELETE FROM task WHERE user_id = '$id';";
  $result = mysqli_query($con, $sqldeletetasks);
  // delete user
  $sql = "DELETE FROM user WHERE id = '$id'";
  $result = mysqli_query($con, $sql);
}

// admin edit user
elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'edituser') {
  $id = $_POST['user-id'];
  $sql = "SELECT image FROM user WHERE id='$id'";
  $result = mysqli_query($con, $sql);
  $userimage = '';
  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $userimage = $row['image'];
  }
  $image_added = false;
  $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');

  if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
    $fileInfo = getimagesize($_FILES['image']['tmp_name']);
    $fileType = $fileInfo['mime'];

    if (in_array($fileType, $allowedTypes)) {
      $folder = "userimage/";
      if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
      }

      $image = $folder . $_FILES['image']['name'];
      move_uploaded_file($_FILES['image']['tmp_name'], $image);

      if (file_exists($userimage)) {
        unlink($userimage);
      }

      $image_added = true;
    }
  }

  $username = addslashes($_POST['edit-name']);
  $email = addslashes($_POST['edit-email']);

  if ($image_added) {
    $query = "UPDATE user SET username = '$username', email = '$email', image = '$image' WHERE id = '$id' LIMIT 1";
  } else {
    $query = "UPDATE user SET username = '$username', email = '$email' WHERE id = '$id' LIMIT 1";
  }

  $result = mysqli_query($con, $query);

  $query = "SELECT * FROM user WHERE id = '$id' LIMIT 1";
  $result = mysqli_query($con, $query);
}
// manager delete task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deletetask') {
   $taskId = $_POST['task-id'];
   $sql = "DELETE FROM task WHERE task_id = '$taskId'";
   $result = mysqli_query($con, $sql);
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="css/listuserstyle.css">
<title>User List</title>
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
<?php if($userrole=='Project Manager'){?>
  <div class="container" style="margin-top: 1%;">
  <div class="row">
   <?php 
    $managerid = $_SESSION['user_info']['id'];
    $employeeList = array(); // Array to store employees
    $projectsql = "SELECT * FROM project_list WHERE manager_id='$managerid'"; // Retrieve ll manager
    $result = mysqli_query($con, $projectsql);
    if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
    $projectId = $row['id'];
    $query = "SELECT * FROM user INNER JOIN project_members ON user.id = project_members.user_id WHERE role='Employee' AND communityadmin_id='$adminId' AND project_members.project_id='$projectId'";  //  employees la kel project
    $employeeResult = mysqli_query($con, $query);
    if (mysqli_num_rows($employeeResult) > 0) {
    while ($employeeRow = mysqli_fetch_assoc($employeeResult)) {
    $id = $employeeRow['id'];
    if (!in_array($id, $employeeList)) { // Check if employeeis already added
    $employeeList[] = $id; // Add employee ID to the list
    $name = $employeeRow['username'];
    $taskCountQuery = "SELECT COUNT(*) AS task_count FROM task WHERE user_id='$id' AND project_id IN (SELECT id FROM project_list WHERE manager_id='$managerid')"; //  task counts la kel employee 
    $taskCountResult = mysqli_query($con, $taskCountQuery);
    if ($taskCountResult) {
      $taskCountRow = mysqli_fetch_assoc($taskCountResult);
      $taskCount = $taskCountRow['task_count'];
    }
    else{
      $taskCount='0';
    }
    $taskDoneQuery = "SELECT COUNT(*) AS task_count FROM task WHERE status='done' AND user_id='$id' AND project_id IN (SELECT id FROM project_list WHERE manager_id='$managerid')"; // done tasks
    $taskDoneResult = mysqli_query($con, $taskDoneQuery);
    if ($taskDoneResult) {
      $taskDoneRow = mysqli_fetch_assoc($taskDoneResult);
      $taskdone = $taskDoneRow['task_count'];
    }
    else{
      $taskdone='0';
    }
    if ($taskCount > 0) {
    $productivity = ($taskdone / $taskCount) * 100; // % productivity
    } else {
    $productivity = 0; 
    }
    ?>
    <div class="col-md-4" style="margin-top:3%;">
    <div class="card project-card" onclick="toggleDetails(this)">
      <div class="card-body">
      <h5 class="card-title" style="text-align: center;"><?php echo $name; ?></h5>
      </div>
      <div class="card-footer project-details">
      <p>Tasks assigned: <?php echo $taskCount?></p>
      <p>Tasks done: <?php echo $taskdone?></p>
      <p><?php echo "Productivity: " . round($productivity, 2) . "%";?></p>
      </div>
    </div>
    </div>
      <?php
                  }
               }
            }
         }
      
   ?>
   </div>
</div>

<table style="margin-top: 3%;">
   <thead>
      <tr>
         <th>Username</th>
         <th>Project Name</th>
         <th>Task Name</th>
         <th>Due Date</th>
         <th>Task Status</th>
         <?php if ($userrole == 'Project Manager') { ?>
         <th style="width:20%">Action</th>
         <?php } ?>
      </tr>
   </thead>
   <tbody>
      <?php
         $taskQuery = "SELECT u.username, p.name AS project_name, t.task_title, t.task_id,t.status,t.due_date
                       FROM user u
                       INNER JOIN project_members pm ON u.id = pm.user_id
                       INNER JOIN project_list p ON pm.project_id = p.id
                       LEFT JOIN task t ON u.id = t.user_id AND p.id = t.project_id
                       WHERE p.manager_id='$managerid'
                       GROUP BY u.username, p.name, t.task_title, t.status";
         $taskResult = mysqli_query($con, $taskQuery);
         if (mysqli_num_rows($taskResult) > 0) {
            while ($taskRow = mysqli_fetch_assoc($taskResult)) {
               $taskid=$taskRow['task_id'];
               $username = $taskRow['username'];
               $projectName = $taskRow['project_name'];
               $taskName = $taskRow['task_title'];
               $taskduedate = $taskRow['due_date'];
               $taskStatus = $taskRow['status'];
      ?>
      <tr>
         <td><?php echo $username; ?></td>
         <td><?php echo $projectName; ?></td>
         <td><?php echo $taskName; ?></td>
         <td><?php echo $taskduedate; ?></td>
         <td><?php echo $taskStatus; ?></td>
         <?php if ($userrole == 'Project Manager') {
          echo "<td style='width:90%'
                <button onclick='deletetask(event,\"$taskid\")' type='button' style='margin-right:1.5%; border-radius: 50%; background-color: #f5f4f4 !important ;'>
                  <i class='fas fa-trash-alt statusclick' style='color:red;'></i>
                </button>
              </td>";
        }?>
      </tr>
      <?php
         }
      } 
      ?>
   </tbody>
</table>
<!-- delete task-->
<div class="modal" id="delete-task-form" style="margin-top:15%;width:30%;height:30%;margin-left:35%">
  <div class="modal-content">
    <h3 style="text-align:center;margin-top:5%;margin-bottom:10%">Delete Task?</h3>
    <form method="post">
      <input type="hidden" name="task-id" value="" id="delete-task-id-input">
      <input type="hidden" name="action" value="deletetask">
      <button class="statusclick" id="confirm-delete-btn" style="margin-left: 30%; background-color: red;border-radius:10px;margin-bottom:4%">Delete</button>
      <button onclick="closedelete()" class="statusclick" type="button" style="margin-left:5%;border-radius:10px;background-color: #f855a6d0">Cancel</button>
    </form>
  </div>
</div>
<script>
  currentprojectId='';
  function deletetask(event, id) {
    event.stopPropagation();
    currentprojectId = id;
    var projectid = document.getElementById("delete-task-id-input");
    projectid.value = currentprojectId;
    var modal = document.getElementById("delete-task-form");
    modal.style.display = "block";
  }
  function closedelete() {
    var modal = document.getElementById("delete-task-form");
    modal.style.display = "none";
  }
  </script>
<!-- project admin-->
<?php } else echo "no users";  } else{ ?>
  <a href="newuser.php" tyle="text-decoration: none;margin-left:43%;"><button style="margin-top:1.5%;margin-left:43%"> + add new user </button></a>
  <div class="container" style="margin-top:1%;">
  <div class="row">
   <?php
   $defimg='userimage/blank-profile-picture-973460_1280 (1).png';
    $adminID = $_SESSION['user_info']['id'];
    $query = "SELECT * FROM user WHERE communityadmin_id = $adminID";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $userid = $row['id'];
        $name = $row['username'];
        $email = $row['email'];
        $role = $row['role'];
        $img = $row['image'];
        $imageSrc = !empty($img) ? $img : $defimg;
        if (empty($img)) {
            $imageSrc = $defimg;
        }
        
     ?>
     <div class="col-md-4" style="margin-top: 3%;">
   <div class="card project-card">
   <div class="card-body">
   <h5 class="card-title" style="text-align: center;"><?php echo  $name; ?></h5>
   <img src="<?php echo $imageSrc; ?>" style="height: 30%;width:40%;margin-top: 3%;border-radius:50%;margin-left:25%"><hr>
   <p class="card-text">Email: <?php echo  $email; ?></p>
   <p class="card-text">Role: <?php echo  $role; ?></p>
   <button style="border-radius: 50%; background-color: #f855a6d0; margin-left: 5%; margin-top: 3%" onclick="editUser('<?php echo $userid;?>', '<?php echo $name;?>', '<?php echo $email;?>', '<?php echo $role?>', '<?php echo $imageSrc;?>')">
 <i class="fas fa-edit" style="color: white;"></i></button>        
   <button style="border-radius: 50%; background-color: red;margin-left:5%;margin-top:3%" onclick="deleteUser(<?php echo $userid;?>)">
   <i class='fas fa-trash-alt' style='color: white;'></i></button>
  </div></div></div>
      <?php  
      }
    } 
   else 
   echo "no users";
    mysqli_close($con);
    ?>


  <!-- delete user--> 
<div class="modal"  id="delete-user-form" style="margin-top:15%;width:30%;height:40%;margin-left:35%">
 <div class="modal-content">
 <p style="margin-left:35%;margin-top:10%;">Delete User?</p>
 <form  method="post">
 <label for="deleteTasksProjects" style="margin-left:15%;margin-top:10%;">Delete associated projects or tasks?</label>
  <input type="checkbox" id="deleteTasksProjects" name="deleteTasksProjects"><br><br>
  <input type="hidden" name="user-id" value="" id="delete-user-id-input">
  <input type="hidden" name="action" value="deleteuser">
  <button id="confirm-delete-btn" style="margin-left: 22%;background-color: red">Delete</button>
  <button type="button" style="margin-left: 10%;" onclick="closeModal('delete-user-form')">Cancel</button>
 </form></div>
</div>
<!--edit user-->
<div class="modal" id="edit-user-form" style="width: 50%;height:70%;margin-top:7%">
 <div class="modal-content" style="margin-top:7%;padding-left:30%">
 <form method="post"  enctype="multipart/form-data">
 <input type="text" name="edit-name" id="edit-name" placeholder="Edit username" style="margin-top: 3%;" required><br>
 <input type="text" name="edit-email" id="edit-email" placeholder="Edit user email" style="margin-top: 3%;" required><br>
 <img id="edit-img" src="" name="edit-img" style="height:30%;width:30%;margin-top: 3%;margin-bottom:5%"><br>
 Profile Picture: <input type="file" name="image" accept="image/*"><br>
 <input type="hidden" name="user-id" value="" id="edit-user-id-input">
 <input type="hidden" name="action" value="edituser">
 <button type="submit" style="margin-left: 8%;margin-top:7%">Save</button>
 <button type="button" onclick="closeModal('edit-user-form')" style="margin-left: 10%;margin-top:7%">Cancel</button>
 </form>
</div>


<?php }?>


<script>
 
function toggleDetails(card) {
  card.classList.toggle('expanded');
}

  var currentUserId = '';
function deleteUser(userid){
  
  currentUserId = userid;
  var modal = document.getElementById("delete-user-form");
  
    modal.style.display = "block";
    var deleteuserIdInput = document.getElementById("delete-user-id-input");
    deleteuserIdInput.value =  currentUserId;
}
function editUser(userid, name, email, role, img) {
    currentUserId = userid;
    var username = document.getElementById("edit-name");
    var emailElement = document.getElementById("edit-email");
    var imgElement = document.getElementById("edit-img");
    var edituserIdInput = document.getElementById("edit-user-id-input");
    edituserIdInput.value =  currentUserId;
    username.value = name;
    emailElement.value = email;
    imgElement.src = img;

    var modal = document.getElementById("edit-user-form");
    modal.style.display = "block";
   }

function closeModal(formName) {
  var modal = document.getElementById(formName);
  modal.style.display = "none";
}
</script>
</body>
</html>
