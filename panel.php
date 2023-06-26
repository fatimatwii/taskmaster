<?php
require("vendor/autoload.php");
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
session_start();
include('dbconfig/connect.php');
if (empty($_SESSION['user_info'])) {
header("Location: login.php");
exit();  }
$userrole=$_SESSION['user_info']['role'];
$userid=$_SESSION['user_info']['id'];
$title='Home Page';
require "headerr.php";
require "sidebar.php";
//delete project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
  $id = $_POST["project-id"];
  $sql = "DELETE FROM project_list WHERE id = '$id'";
  $result = mysqli_query($con, $sql);
  // delete project members
  $sqlmembers = "DELETE FROM project_members WHERE project_id = '$id'";
  $result = mysqli_query($con, $sqlmembers);
  // delete project tasks
  $sqldeletetasks="DELETE FROM task WHERE project_id = '$id'";
  $result = mysqli_query($con, $sqldeletetasks);
  // delete project collaboration
  $sqlcollaboration="DELETE FROM collaboration WHERE project_id = '$id'";
  $result = mysqli_query($con, $sqlcollaboration);
  header("Location: panel.php");
  die;
}
//update project
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "edit") {
  $projectId = $_GET['id'];
  $name = $_POST['projectname'];
  $startDate = $_POST["start_date"];
  $endDate = $_POST["end_date"];
  $status = $_POST["edit-status"];
  $description = $_POST["edit-description"];
  $managerId = $_POST["edit-manager"];
  // update project
  $updateQuery = "UPDATE project_list SET name='$name', manager_id='$managerId', start_date='$startDate', end_date='$endDate', status='$status', description='$description' WHERE id='$projectId'";
  $result = mysqli_query($con, $updateQuery);
  $selectedUserIds = $_POST['selectedmemberids'];
  $userIds = explode(',', $selectedUserIds);
  // delete all members, add old and new if
  $deleteQuery = "DELETE FROM project_members WHERE project_id='$projectId'";
  mysqli_query($con, $deleteQuery);
  foreach ($userIds as $userId) {
  $insertQuery = "INSERT INTO project_members (project_id, user_id) VALUES ('$projectId', '$userId')";
   mysqli_query($con, $insertQuery);
  }
  header("Location: panel.php");
  die;
}
// add task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addtask') {
  $id = $_POST["project-id"];
  $tasktitle=$_POST["taskname"];
  $duedate=$_POST["due-date"];
  $descriptionname=$_POST["descriptionname"];
  $selectedUserId = $_POST['user_id'];
  $membernamesql="select username,email from user where id='$selectedUserId'";
  $memberresult = mysqli_query($con, $membernamesql);
  $membername='';
  $memberemail='';
  if ($memberresult && mysqli_num_rows($memberresult) > 0) {
  $row = mysqli_fetch_assoc($memberresult);
  $membername=$row['username'];
  $memberemail=$row['email'];
  } 
  $sqlnotifications="select email_notifications from user_settings where user_id='$selectedUserId'";
  $notificationsresult = mysqli_query($con, $sqlnotifications);
  if ($row = mysqli_fetch_assoc($notificationsresult)) {
  $emailNotifications = $row['email_notifications'];
  if ($emailNotifications == 1) {
  $sql = "insert into task(user_id,project_id,task_title,task_description,due_date,status)values('$selectedUserId','$id','$tasktitle','$descriptionname','$duedate','todo')";
  $result = mysqli_query($con, $sql);
    // l sendinblue api ma byzbat nazlo github, service provider's api,
 // $apiKey = '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';
//  $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
//  $apiInstance = new TransactionalEmailsApi(null, $config);
//  $emailParams = [
//  'sender' => ['email' => 'taskmasterapp01@gmail.com'],
//  'to' => [['email' => $memberemail]],
//  'subject' => 'TASK MASTER:New Task Assigned',
//  'htmlContent' => '
//  <p>Dear '.$membername.',</p>
//  <p>I hope this email finds you well. Your manager has assigned new task for you. Below are the details of the tasks:</p>
//  <p><strong>Task Title:</strong>'. $tasktitle.'</p>
//  <p><strong>Assigned By:</strong>'.$_SESSION['user_info']['username'].'</p>
//  <p><strong>Deadline:</strong>'.$duedate.'</p>
//  <p><strong>Description:</strong>'.$descriptionname.'</p>
//  <p>Please review the task and make sure to understand the requirements and deadlines. If you have any questions or need clarifications, don\'t hesitate to reach out to your manager for further assistance.</p>
//  <p>We trust in your abilities to successfully complete these task. Your contributions are valuable to the team, and we appreciate your dedication.</p>
//  <p>Thank you for your attention to this matter.</p>
//  <p>Best regards,</p>
//  <p>'.$_SESSION['user_info']['username'].'</p>
//  <p>[Manager]</p>'
//  ];
//  $response = $apiInstance->sendTransacEmail($emailParams);}}
  header("Location: panel.php?action=viewproject&id=$id");
  die;
}
// add collaboration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addcollaboration') {
  $id = $_POST["projectt-id"];
  $date = $_POST["date"];
  $starthour=$_POST["start-hour"];
  $endhour=$_POST["end-hour"];
  $projectname=$_POST["projectt-name"];
  $sql = "insert into collaboration(project_id,projectname,date,start_time,end_time,project_manager_id)values('$id','$projectname','$date','$starthour','$endhour','$userid')";
  $result = mysqli_query($con, $sql);
  
  header("Location: panel.php?action=viewproject&id=$id");
  die;
}
// delete task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deletetask') {
  $taskId = $_POST['task-id'];
  $sql = "DELETE FROM task WHERE task_id = '$taskId'";
  $result = mysqli_query($con, $sql);
}
// edit task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edittask') {
  $taskId = $_POST['task-id'];
  $editTitle = $_POST['edit-title'];
  $editDescription = $_POST['edit-description'];
  $editdate=$_POST['edit-due-date'];
  $sql="UPDATE task SET task_title='$editTitle', task_description='$editDescription', due_date='$editdate' WHERE task_id='$taskId'";
    mysqli_query($con,$sql);
}
// set task done
if($_SERVER['REQUEST_METHOD'] == "POST"  && $_POST['action'] == 'setdone'){
  $taskId = $_POST['task-id'];
  $sql="UPDATE task SET status='done' WHERE task_id='$taskId'";
  mysqli_query($con,$sql);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="css/adminpanelstyle.css">
</head>

<body>
<!-- view project/ ma3 l tasks wl members/  eza manager add edit delete tasks / eza employee beshof my tasks wset done-->
<?php if(!empty($_GET['action']) && $_GET['action'] == 'viewproject' && !empty($_GET['id'])): $id = $_GET['id'];
 $sqlproject="SELECT * FROM project_list WHERE id = $id";
 $result = mysqli_query($con,$sqlproject);
 if ($result && mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  $name=$row['name'];
  $start_date=$row['start_date'];
  $end_date=$row['end_date'];
  $status=$row['status'];
  $adminid=$row['communityadmin_id'];
  $description=$row['description'];
  $manager_id = $row['manager_id'];
  $managernamee = '';
  $sql = "SELECT username FROM user WHERE id = $manager_id";
  $resultman = mysqli_query($con, $sql);
  if (mysqli_num_rows($resultman) > 0) {
  $rowman = mysqli_fetch_assoc($resultman);
  $managernamee = $rowman['username'];
  }
  $member_ids = array();
  $member_names = array();
  $member_query = "SELECT project_members.project_id , user.username ,user.id FROM project_members INNER JOIN user ON project_members.user_id = user.id WHERE project_members.project_id =$id";
  $member_result = mysqli_query($con, $member_query);
  if (mysqli_num_rows($member_result) > 0) {
  while ($member_row = mysqli_fetch_assoc($member_result)) {
  $member_ids[] = $member_row['id'];
  $member_names[] = $member_row['username'];
  }
  }
 else{
  $member_ids[] = ' ';
  $member_names[] = ' ';
   }
 }?>
                 
 <div class="containerview">
  <div class="up">
  <div class="column">
    <h5><strong>Project Name:</strong></h5>
    <p> <?php echo $name;?></p>
    <h6><strong>Description:</strong></h6>
    <p><?php echo $description;?></p>
   </div>
  <div class="column">
    <h6><strong>Start Date:</strong></h6>
    <p><?php echo $start_date;?></p>
    <h6><strong>End Date:</strong></h6>
    <p><?php echo $end_date;?></p>
    <h6><strong>Status</strong></h6>
    <p><?php echo $status;?></p>
    <h6><strong>Project Manager:</strong></h6>
    <p><?php echo $managernamee;?></p>
    </div>
  </div>
  <div class="down">
    <div class="left">
      <h6 style="margin-top:3%;margin-left:3%"><strong>Team Member/s:</strong></h6><hr>
      <?php
        $member_query = "SELECT project_members.user_id, user.username FROM project_members INNER JOIN user ON project_members.user_id = user.id WHERE project_members.project_id ='$id'";
        $member_result = mysqli_query($con, $member_query);
        if (mysqli_num_rows($member_result) > 0) {
          while ($member_row = mysqli_fetch_assoc($member_result)) {
            $memberId = $member_row['user_id'];
            $memberName = $member_row['username'];
            echo '<p style="margin-left:3%" value="' . $memberId . '">' . $memberName .'</p>';
          }
        }
        ?>
    </div>
   <div class="right">
   <h6 style="margin-left:3%;margin-top:1%"><?php if ($userrole == 'Employee') { echo  "<strong>My tasks</strong>"; } else { echo "<strong>Task List</strong>";} 
  if ($userrole == 'Project Manager') { 
    echo "<a style='margin-left:10%;text-decoration:none;color:black;font-size:1rem'>Add task:<button onclick='addtask(\"$id\"," . json_encode($member_names) . "," . json_encode($member_ids) . ")' class='statusclick' style='border-radius: 50%; background-color: #f855a6d0;margin-left:1%;margin-top:3%'><i class='fas fa-tasks fa-lg' style='color: white;'></i></button></a>";
    echo "<a style='margin-left:30%;text-decoration:none;color:black;font-size:1rem'>Assign Collaborations:<button onclick='addcollaboration(event,\"$id\",\"$name\")' class='statusclick' style='border-radius: 50%; background-color: #f855a6d0;margin-left:1%;margin-top:3%'><i class='fas fa-comments fa-lg' style='color: white;'></i></button></a>";
  } ?>
  </h6><hr>
   <table class="task-table">
  <thead>
    <tr>
      <th>Task</th>
      <th>Description</th>
      <th>Due Date</th>
      <th>Status</th>
      <?php if ($userrole == 'Project Manager') { ?>
      <th style="width:20%">Action</th>
      <?php } ?>
      <?php if ($userrole == 'Employee') { ?>
      <th style="width:20%">Set Done</th>
      <?php } ?>
    </tr>
  </thead>
  <tbody>
    <?php
    if($userrole == 'Employee'){
    $query = "SELECT task_id,task_title AS task_name,task_description,due_date, status AS task_status FROM task WHERE project_id = '$id' and user_id='$userid'";
    }else {
    $query = "SELECT task_id,task_title AS task_name,task_description,due_date, status AS task_status FROM task WHERE project_id = '$id'";
    }
    $taskresult = mysqli_query($con, $query);
    if (mysqli_num_rows($taskresult) > 0) {
      foreach ($taskresult as $row) {
        $taskid = $row['task_id'];
        $tasktitle = $row['task_name'];
        $taskdescription = $row['task_description'];
        $taskdate = $row['due_date'];
        $status = $row['task_status'];
        if (strtotime($taskdate) < strtotime(date('Y-m-d H:i:s'))) {
          $titleclass = 'late';
        } else {
          $titleclass = '';
        }
        $liClass = ($status == 'done') ? 'line-through' : '';
        echo "<tr>";
        echo "<td class='$liClass $titleclass'>$tasktitle</td>";
        echo "<td class='$liClass $titleclass'>$taskdescription</td>";
        echo "<td class='$liClass $titleclass'>$taskdate</td>";
        echo "<td class='$liClass $titleclass'>$status</td>";
        if ($userrole == 'Project Manager') {
          echo "<td style='width:90%'
                <button onclick='setdone(event,\"$taskid\")' type='button' style='margin-right:1.5%; margin-left:3%;border-radius: 50%;width: 87%;'>
                  <i class='fas fa-check-circle fa-lg statusclick' style='color:#f855a6d0;'></i>
                </button>
                <button onclick='edittask(event,\"$taskid\",\"$tasktitle\", \"$taskdescription\", \"$taskdate\")' type='button' style='margin-right:1.5%; border-radius: 50%; background-color: #f5f4f4 !important;'>
                  <i class='fas fa-eye statusclick' style='color:#ed89a9;'></i>
                </button>
                <button onclick='deletetask(event,\"$taskid\")' type='button' style='margin-right:1.5%; border-radius: 50%; background-color: #f5f4f4 !important ;'>
                  <i class='fas fa-trash-alt statusclick' style='color:red;'></i>
                </button>
              </td>";
        }
        if ($userrole == 'Employee') {
          echo "<td style='width:90%'
                <button onclick='setdone(event,\"$taskid\")' type='button' style='margin-right:1.5%; margin-left:3%;border-radius: 50%;width: 87%;'>
                  <i class='fas fa-check-circle fa-lg statusclick' style='color:#f855a6d0;'></i>
                </button>
              </td>";
        }
        echo "</tr>";
      }
    }
    ?>
  </tbody>
 </table>

 <!--add task-->
 <div class="modal" id="add-task-form" style="margin-top: 5%;width: 65%;height: 80%;margin-left: 15%">
  <div class="modal-content">
    <h3 style="text-align: center; margin-top: 5%">New Task</h3>
    <form method="post">
      <div style="display: flex; flex-direction: row;">
        <div style="flex: 1; margin-right: 5%;">
          <label for="taskname" style="margin-left: 20%;">Task:</label><br>
          <input type="text" name="taskname" required style="margin-left: 20%; width: 70%"><br>
          <label for="due-date" style="margin-left: 20%;">Due Date:</label><br>
          <input class="form-group" style="margin-left: 20%; width: 70%" type="datetime-local" id="due-date" name="due-date" required><br>
        </div>
        <div style="flex: 1;">
          <label for="user_id">Assign To:</label><br>
          <select id="userSelect" name="user_id" style="width: 70%">
          </select>
        </div>
      </div>
      <label for="descriptionname" style="margin-left: 10%;">Description:</label><br>
      <textarea name="descriptionname" cols="100" rows="8" style="margin-left: 10%;"></textarea><br>
      <input type="hidden" name="project-id" value="" id="project-id">
      <input type="hidden" name="action" value="addtask">
      <button class="statusclick" id="add-task-btn" style="background-color: #f855a6d0; border-radius: 10px; margin-top: 10px; margin-left: 40%;margin-bottom:4%">Save</button>
      <button onclick="closeModal('add-task-form')"; type="button" class="statusclick" style="border-radius: 10px; background-color: #f855a6d0; margin-left: 10px;">Cancel</button>
    </form>
  </div>   
 </div> 
 <!-- add-collaboration-form-->
 <div class="modal" id="add-collaboration-form" style="margin-top:10%;width:50%;height:70%;margin-left:25%">
  <div class="modal-content">
    <h6 style="text-align:center;margin-top:5%;margin-bottom:5%">The 'Assign Collaboration' feature allows you, as a manager,<br> to connect with your team members and initiate real-time communication</h6>
    <form method="post" style="margin-left: 35%;">
    <label for="date" style="margin-bottom: 4%;">Date:</label>
    <input type="date" id="date" name="date" required style="margin-bottom: 4%;margin-left:10%"><br>
    <label for="start-hour" style="margin-bottom: 4%;">Start Hour:</label>
    <input type="time" id="start-hour" name="start-hour" required style="margin-bottom: 4%;margin-left: 2%"><br>
    <label for="end-hour" >End Hour:</label>
    <input type="time" id="end-hour" name="end-hour" required style="margin-left: 3%;"><br>
    <input type="hidden" name="projectt-name" value="" id="projectt-name">
    <input type="hidden" name="projectt-id" value="" id="projectt-id">
    <input type="hidden" name="action" value="addcollaboration">
    <button class="statusclick" id="save-btn" style=" background-color: red;border-radius:10px;margin-top:4%;margin-bottom:4%">Save</button>
    <button onclick="closeModal('add-collaboration-form')"; class="statusclick" type="button" style="margin-left:5%;border-radius:10px;background-color: #f855a6d0">Cancel</button>
    </form>
  </div>
 </div>
 <!-- delete task-->
 <div class="modal" id="delete-task-form" style="margin-top:15%;width:30%;height:30%;margin-left:35%">
  <div class="modal-content">
    <h3 style="text-align:center;margin-top:5%;margin-bottom:10%">Delete Task?</h3>
    <form method="post">
      <input type="hidden" name="task-id" value="" id="delete-task-id-input">
      <input type="hidden" name="action" value="deletetask">
      <button class="statusclick" id="confirm-delete-btn" style="margin-left: 30%; background-color: red;border-radius:10px;margin-bottom:4%">Delete</button>
      <button onclick="closeModal('delete-task-form')"; class="statusclick" type="button" style="margin-left:5%;border-radius:10px;background-color: #f855a6d0">Cancel</button>
    </form>
  </div>
 </div>

 <!-- done task-->
 <div class="modal" id="done-task-form" style="margin-top:15%;width:30%;height:30%;margin-left:35%">
  <div class="modal-content">
    <h3 style="text-align:center;margin-top:5%;margin-bottom:10%">Done Task?</h3>
    <form method="post">
      <input type="hidden" name="task-id" value="" id="done-task-id">
      <input type="hidden" name="action" value="setdone">
      <button class="statusclick" id="confirm-done-btn" style="margin-left: 30%; background-color: #f855a6d0;border-radius:10px;margin-bottom:4%">Done</button>
      <button onclick="closeModal('done-task-form');" class="statusclick" type="button" style="margin-left:5%;border-radius:10px;background-color: #f855a6d0">Cancel</button>
    </form>
  </div>
 </div>

 <!-- edit task-->
 <div class="modal" id="edit-task-form" style="margin-top:10%;width:50%;height:70%;margin-left:25%">
  <div class="modal-content">
    <form method="POST">
      <input type="text" name="edit-title" id="edit-title" placeholder="Edit task title" style="margin-top:5%;margin-left:30%;margin-bottom:3%;" required><br>
      <input type="datetime" name="edit-due-date" id="edit-due-date" style="margin-left:20%;margin-bottom:3%" required><br>
      <textarea name="edit-description" id="edit-description" cols="50" rows="8" style="margin-left:20%;margin-bottom:3%" placeholder="Edit task description"></textarea><br>
      <input type="hidden" name="task-id" value="" id="edit-task-id-input">
      <input type="hidden" name="action" value="edittask">
      <button class="statusclick" type="submit" style="margin-left: 40%;border-radius:10px;background-color: #f855a6d0;margin-bottom:4%">Save</button>
      <button onclick="closeModal('edit-task-form');" class="statusclick" type="button" style="margin-left:5%;border-radius:10px;background-color: #f855a6d0">Cancel</button>
    </form>
  </div>
 </div>

 <script>
  currentprojectId='';
 function addtask(id, memberNames, memberIds) {
  currentprojectId = id;
  var projectid = document.getElementById("project-id");
  projectid.value = currentprojectId;
  var userSelect = document.getElementById("userSelect");
  userSelect.innerHTML = ""; 
  for (var i = 0; i < memberNames.length; i++) {
  var option = document.createElement("option");
  option.value = memberIds[i];
  option.textContent = memberNames[i];
  userSelect.appendChild(option);
  }
  var modal = document.getElementById("add-task-form");
  modal.style.display = "block";
 } 
 currentprojectname='';
 function addcollaboration(event, id,name){
  event.stopPropagation();
  currentprojectId = id;
  currentprojectname=name;
  var projectid = document.getElementById("projectt-id");
  projectid.value = currentprojectId; 
  var projectname = document.getElementById("projectt-name");
  projectname.value=currentprojectname;
  var modal = document.getElementById("add-collaboration-form");
  modal.style.display = "block";
}
  function deletetask(event, id) {
  event.stopPropagation();
  currentprojectId = id;
  var projectid = document.getElementById("delete-task-id-input");
  projectid.value = currentprojectId;
  var modal = document.getElementById("delete-task-form");
  modal.style.display = "block";
  }

  function edittask(event, id, tasktitle, taskdescription, taskdate) {
  event.stopPropagation();
  currentprojectId = id;
  var projectid = document.getElementById("edit-task-id-input");
  projectid.value = currentprojectId;
  var taskname = document.getElementById("edit-title");
  var description = document.getElementById("edit-description");
  var date = document.getElementById("edit-due-date");
  taskname.value = tasktitle;
  description.value = taskdescription;
  date.value = taskdate;
  var modal = document.getElementById("edit-task-form");
  modal.style.display = "block";
  }

  function setdone(event, id) {
  event.stopPropagation();
  currentprojectId = id;
  var projectid = document.getElementById("done-task-id");
  projectid.value = currentprojectId;
  var modal = document.getElementById("done-task-form");
  modal.style.display = "block";
  }

  function closeModal(formName) {
  var modal = document.getElementById(formName);
  modal.style.display = "none";
}
</script>


<!--ll admin ye3mal edit project w members-->
<?php elseif(!empty($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])):
  $id = $_GET['id'];
  $sqlproject="SELECT * FROM project_list WHERE id = $id";
  $result = mysqli_query($con,$sqlproject);
  if ($result && mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  $name=$row['name'];
  $start_date=$row['start_date'];
  $end_date=$row['end_date'];
  $status=$row['status'];
  $adminid=$row['communityadmin_id'];
  $description=$row['description'];
  $manager_id = $row['manager_id'];
  $managernamee = '';
  $sql = "SELECT username FROM user WHERE id = $manager_id";
  $resultman = mysqli_query($con, $sql);
  if (mysqli_num_rows($resultman) > 0) {
  $rowman = mysqli_fetch_assoc($resultman);
  $managernamee = $rowman['username'];
  }
 }
 
 ?>
 <form method="post" enctype="multipart/form-data" class="edit-form">
  <label for="projectname" style="margin-top: 3%;"><strong>Project Name:</strong></label>
  <input value="<?php echo $name ?>" type="text" name="projectname" placeholder="Project name" required>
  <div class="form-container">
    <div class="form-column">
      <!-- Start Date -->
      <div class="form-row" style="margin-left: 30%;">
        <label for="start_date"><strong>Start Date:</strong></label>
        <input type="date" style="margin-left:3%;width:50%" id="start_date" name="start_date" class="edit_start_date" value="<?php echo isset($start_date) ? date("Y-m-d", strtotime($start_date)) : '' ?>">
      </div>

      <!-- End Date -->
      <div class="form-row" style="margin-left: 30%;">
        <label for="end_date"><strong>End Date:</strong></label>
        <input type="date" style="margin-left:3%;width:50%" id="end_date" name="end_date" class="edit_end_date" value="<?php echo isset($end_date) ? date("Y-m-d", strtotime($end_date)) : '' ?>">
      </div>

      <!-- Status -->
      <div class="form-row" style="margin-left: 30%;">
        <label for="edit-status"><strong>Status:</strong></label>
        <?php
        echo '<select name="edit-status" id="edit-status" class="edit-status" style="margin-left:8%;width:50%">';
        echo '<option' . ($status === "pending" ? ' selected' : '') . '>pending</option>';
        echo '<option' . ($status === "On-Hold" ? ' selected' : '') . '>On-Hold</option>';
        echo '<option' . ($status === "Done" ? ' selected' : '') . '>Done</option>';
        echo '</select>';
        ?>
      </div>
    </div>

    <div class="form-column">
      <!-- Manager -->
      <div class="form-row">
        <label for="edit-manager"><strong>Manager:</strong></label><br>
        <?php
        $manquery = "SELECT * FROM user WHERE role='Project Manager' AND communityadmin_id='$adminid'";
        $manresult = mysqli_query($con, $manquery);
        echo '<select name="edit-manager" id="edit-manager" class="edit-manager" style="margin-bottom: 1%; width:50%;margin-left:1%">';
        while ($row = mysqli_fetch_assoc($manresult)) {
          $managerName = $row['username'];
          $selected = ($managerName === $managernamee) ? 'selected' : '';
          echo '<option ' . $selected . ' value="' . $row['id'] . '">' . $managerName . '</option>';
        }
        echo '</select>';
        ?>
      </div>

      <!-- Members -->
      <div class="form-row">
        <label for="edit-members"><strong>Members:</strong></label><br>
        <div id="members-container">
          <?php
          $member_query = "SELECT project_members.user_id, user.username FROM project_members INNER JOIN user ON project_members.user_id = user.id WHERE project_members.project_id ='$id'";
          $member_result = mysqli_query($con, $member_query);
          if (mysqli_num_rows($member_result) > 0) {
            while ($member_row = mysqli_fetch_assoc($member_result)) {
              $memberId = $member_row['user_id'];
              $memberName = $member_row['username'];
              echo '<p value="' . $memberId . '">' . $memberName . ' <span class="cancel-member" onclick="removeMember(this)">&times;</span></p>';
            }
          }
          ?>
        </div><br>
        <select name="edit-category" id="edit-category" onchange="addMember()" style=" width: 50%">
          <?php
          $member_query = "SELECT * FROM user WHERE role='Employee' AND communityadmin_id='$adminid'";
          $member_result = mysqli_query($con, $member_query);
          if (mysqli_num_rows($member_result) > 0) {
            while ($member_row = mysqli_fetch_assoc($member_result)) {
              $memberId = $member_row['id'];
              $memberName = $member_row['username'];
              echo '<option></option>';
              echo '<option value="' . $memberId . '">' . $memberName . '</option>';
            }
          }
          ?>
        </select>
      </div>
    </div>
  </div>
  <label for="edit-description" style="margin-right: 45%;"><strong>Description:</strong></label>
  <textarea name="edit-description" style="width:75%;margin-right: 10%;" cols="10" rows="5" id="edit-description" class="edit-description" placeholder="Edit project description"><?php echo $description ?></textarea>
  <br>
  <input type="hidden" id="selectedmemberids" name="selectedmemberids" value="">
  <input type="hidden" name="action" value="edit">
  <button type="submit" style="margin-left: 0%; width: 10%">Save</button>
  <a href="panel.php"><button style="margin-left: 10%; width: 10%">Cancel</button></a>
 </form>

 <script>
  document.addEventListener("DOMContentLoaded", function() {
    initializeSelectedMembers();
  });

  function initializeSelectedMembers() {
    var membersContainer = document.getElementById("members-container");
    var memberElements = membersContainer.getElementsByTagName("p");
    var selectedMemberIds = [];
  
    for (var i = 0; i < memberElements.length; i++) {
      var memberId = memberElements[i].getAttribute("value");
      selectedMemberIds.push(memberId);
    }
  
    var selectedMembersInput = document.getElementById("selectedmemberids");
    selectedMembersInput.value = selectedMemberIds.join(",");
  }

  function addMember() {
    var selectElement = document.getElementById("edit-category");
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var memberId = selectedOption.value;
    var memberName = selectedOption.text;

    var membersContainer = document.getElementById("members-container");
    var memberElement = document.createElement("p");
    memberElement.setAttribute("value", memberId);
    memberElement.textContent = memberName + " ";
  
    var cancelSpan = document.createElement("span");
    cancelSpan.className = "cancel-member";
    cancelSpan.textContent = "\u00D7";
    cancelSpan.onclick = function() {
      removeMember(this);
    };
  
    memberElement.appendChild(cancelSpan);
    membersContainer.appendChild(memberElement);
  
    updateSelectedMembers();
  }

  function updateSelectedMembers() {
    var membersContainer = document.getElementById("members-container");
    var memberElements = membersContainer.getElementsByTagName("p");
    var selectedMemberIds = [];
  
    for (var i = 0; i < memberElements.length; i++) {
      var memberId = memberElements[i].getAttribute("value");
      selectedMemberIds.push(memberId);
    }
  
    var selectedMembersInput = document.getElementById("selectedmemberids");
    selectedMembersInput.value = selectedMemberIds.join(",");
  }

  function removeMember(spanElement) {
    var memberElement = spanElement.parentNode;
    var membersContainer = memberElement.parentNode;
    membersContainer.removeChild(memberElement);
  
    updateSelectedMembers();
  }
 </script>

<!-- l page 3ade -->
<?php else:?> 
  <?php if ($userrole=='Community Admin'){?>
  <p style="margin-top:2%;text-align: center;font-family: Verdana, sans-serif;font-size: 1.5rem ;font-weight: bolder;color: #c9378e">Welcome <?php echo $_SESSION['user_info']['username'];?></p>
  <p style="text-align: center;font-family: Verdana, sans-serif">"We are grateful for your tireless work in maintaining and fostering this community.<br> Your passion is instrumental in creating a vibrant and engaging space for everyone."</p>
  <?php }
  elseif($userrole=='Project Manager'){ ?>
  <p style="margin-top:2%;text-align: center;font-family: Verdana, sans-serif;font-size: 1.5rem ;font-weight: bolder;color: #c9378e">Welcome <?php echo $_SESSION['user_info']['username'];?></p>
  <p style="text-align: center;font-family: Verdana, sans-serif">"Great job on managing the team's performance! <br>Your dedication and leadership skills are truly inspiring."</p>
  <?php }
  else {?>
  <p style="margin-top:2%;text-align: center;font-family: Verdana, sans-serif;font-size: 1.5rem ;font-weight: bolder;color: #c9378e">Welcome <?php echo $_SESSION['user_info']['username'];?></p>
  <p style="text-align: center;font-family: Verdana, sans-serif">"Remember that your contributions are valuable and essential to the team's success."</p>
  <?php }?>
  <br>
  <hr>
  <?php if ($userrole == 'Community Admin') { ?>
  <a href="newproject.php" style="text-decoration: none;margin-left:5%;font-size:1rem"><button style="background-color: #f855a6d0;border-radius: 5%;"> + add new project </button></a>
  <?php } ?>
 <div class="container" style="margin-top: 1%;">
  <div class="row">
    <?php
    $adminid = $_SESSION['user_info']['id'];
    if ($userrole == 'Community Admin') {
      $query = "SELECT * FROM project_list WHERE communityadmin_id = '$adminid' ORDER BY date_created DESC";
    } elseif($userrole=='Project Manager') {
      $query = "SELECT * FROM project_list WHERE manager_id = '$adminid' ORDER BY date_created DESC";
    }else{
      $query = "SELECT * FROM project_list WHERE id IN(select project_id from project_members where user_id='$userid') ORDER BY date_created DESC";
    }
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
      while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $name = $row['name'];
        $description = $row['description'];
        $status = $row['status'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
        $manager_id = $row['manager_id'];
        // managername
        $managername = '';
        $sql = "SELECT username FROM user WHERE id = $manager_id";
        $resultman = mysqli_query($con, $sql);
        if (mysqli_num_rows($resultman) > 0) {
          $rowman = mysqli_fetch_assoc($resultman);
          $managername = $rowman['username'];
        } else {
          $managername = ' ';
        }
        ?>
        <div class="col-md-4" style="margin-top: 3%;">
          <div class="card project-card" onclick="toggleDetails(this)">
            <div class="card-body">
              <h5 class="card-title"><?php echo $name; ?>
              <a href="panel.php?action=viewproject&id=<?php echo $row['id']; ?>"><button  class='statusclick' type='submit' style='margin-left:55%; border-radius: 50%; background-color: #f5f4f4;;'><i class='fas fa-eye' style='color:white;'></i></button></a>
              </h5><hr>
              <p class="card-text">Status:<?php echo $status; ?></p>
            </div>

            <div class="card-footer project-details">
              <p>Start Date: <?php echo $start_date; ?></p>
              <p>End Date: <?php echo $end_date; ?></p>
              <p>Project Manager: <?php echo $managername; ?></p>
              <?php if ($userrole == 'Community Admin') { ?>
                <a href="panel.php?action=edit&id=<?php echo $id; ?>" style="margin-left:30%"><button style="border-radius: 50%; background-color: #f855a6d0;margin-left:5%;margin-top:3%"><i class="fas fa-edit" style="color: white;"></i></button></a>
                <button style="border-radius: 50%; background-color: red !important;margin-left:5%;margin-top:3%" onclick="deleteproject(<?php echo $id; ?>)"><i class='fas fa-trash-alt' style='color: white;'></i></button>
              <?php } ?>
            </div>

          </div>
        </div>
    <?php
      }

    }
    ?>
  </div>
 </div>
<!--admin delete project-->
 <div class="modal" id="delete-project-form" style="margin-top:15%;width:30%;height:40%;margin-left:35%">
 <div class="modal-content">
  <h3 style="text-align:center;margin-top:5%;margin-bottom:10%">Delete Project?</h3>
  <form method="post">
  <input type="hidden" name="project-id" value="" id="delete-project-id-input">
  <input type="hidden" name="action" value="delete">
  <button id="confirm-delete-btn" style="margin-left: 30%; background-color: red !important;border-radius:10px;margin-bottom:4%;">Delete</button>
  <a href="panel.php"><button type="button" style="margin-left:5%;border-radius:10px">Cancel</button></a>
  </form> 
 </div>   
</div>  
  

<script>
function toggleDetails(card) {
  card.classList.toggle('expanded');
}
currentprojectId='';
function deleteproject(id){
  currentprojectId=id;
  var projectid = document.getElementById("delete-project-id-input");
  projectid.value = currentprojectId;
  var modal = document.getElementById("delete-project-form");
  modal.style.display = "block";
}
function closedelete(){
  var modal = document.getElementById("delete-project-form");
  modal.style.display = "none";
}
</script>
<?php endif;?>
</body>
</html>
