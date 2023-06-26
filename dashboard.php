<?php session_start();
if (empty($_SESSION['user_info'])) {
  header("Location: login.php");
exit();  }
$title = "My To-Do List";
 $userid=$_SESSION['user_info']['id'];
 require_once "headerr.php";
 include('dbconfig/connect.php');
// theme
  $query="select color_scheme from user_settings where user_id='$userid'";
  $result = mysqli_query($con, $query);
  $cssClass = 'light-body'; 
  if ($result && mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  $cssClass = ($row['color_scheme'] === 'dark') ? 'dark-body' : 'light-body';
}
// sorting 
 $query="select sort_order from user_settings where user_id='$userid'";
 $result = mysqli_query($con, $query);
 $sorting = 'due_date'; 
 if ($result && mysqli_num_rows($result) > 0) {
 $row = mysqli_fetch_assoc($result);
 $sorting  = ($row['sort_order'] === 'priority') ? 'priority' : 'due_date';
}
//add new task php
if($_SERVER['REQUEST_METHOD'] == "POST"  && $_POST['action'] == 'addtask'){
  $title = $_POST['title'];
  $description = $_POST['description'];
  $dueDate = $_POST['due-date'];
  $category = $_POST['category'];
  $priority = $_POST['priority'];
  $categorysql="select id from category where name='$category'";
  $result=mysqli_query($con,$categorysql);
  $categoryid= null;;
  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $categoryid=$row['id'];
    $sql = "INSERT INTO task (user_id,task_title, task_description, due_date,category_id ,priority,status) VALUES ('$userid','$title', '$description', '$dueDate','$categoryid','$priority','todo')";
    mysqli_query($con,$sql);
    header('location:dashboard.php');
   }
   else{
    $sql = "INSERT INTO task (user_id,task_title, task_description, due_date,priority,status) VALUES ('$userid','$title', '$description', '$dueDate','$priority','todo')";
    mysqli_query($con,$sql);
    header('location:dashboard.php');
   }
  
}
// set task done
if($_SERVER['REQUEST_METHOD'] == "POST"  && $_POST['action'] == 'setdone'){
  $taskId = $_POST['task-id'];
  $sql="UPDATE task SET status='done' WHERE task_id='$taskId'";
  mysqli_query($con,$sql);
}// set task TODO
if($_SERVER['REQUEST_METHOD'] == "POST"  && $_POST['action'] == 'settodo'){
  $taskId = $_POST['task-id'];
  $sql="UPDATE task SET status='todo' WHERE task_id='$taskId'";
  mysqli_query($con,$sql);
}
// set task arch
if($_SERVER['REQUEST_METHOD'] == "POST"  && $_POST['action'] == 'setarc'){
  $taskId = $_POST['task-id'];
  $sql="UPDATE task SET status='ARCHIVED' WHERE task_id='$taskId'";
  mysqli_query($con,$sql);
}
// add new category php
if($_SERVER['REQUEST_METHOD'] == "POST"  && $_POST['action'] == 'addcategory'){
  $newcategory = $_POST['newcategory'];
  $sqlcat="insert into category (name) value ('$newcategory')";
   mysqli_query($con,$sqlcat);
}
// delete task php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deletetask') {
  $taskId = $_POST['task-id'] ?? '';
  $sql = "DELETE FROM task WHERE task_id = '$taskId'";
  $result = mysqli_query($con, $sql);
 if ($result) {
   header('Location: dashboard.php');
   exit;
  } else {
   echo 'Failed to delete the task.';
  }
}
// edit task php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edittask') {
  $taskId = $_POST['task-id'];
  $editTitle = $_POST['edit-title'];
  $editDescription = $_POST['edit-description'];
  $editPriority=$_POST['edit-priority'];
  $editDueDate = $_POST['edit-due-date'];
  $editcategory=$_POST['edit-category'];
  $newcategorysql="select id from category where name='$editcategory'";
  $result=mysqli_query($con,$newcategorysql);
  $newcategoryid= null;;
  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $newcategoryid=$row['id'];
    $sql="UPDATE task SET task_title='$editTitle', task_description='$editDescription', due_date='$editDueDate', category_id='$newcategoryid',priority='$editPriority' WHERE task_id='$taskId'";
    mysqli_query($con,$sql);
    header('location:dashboard.php');
   }
   else{
    $sql="UPDATE task SET task_title='$editTitle', task_description='$editDescription', due_date='$editDueDate',priority='$editPriority' WHERE task_id='$taskId'";
    mysqli_query($con,$sql);
    header('location:dashboard.php');
   }
 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="css/dashboardstyle.css">
    <title>Dashboard</title>
</head>
<body  class="<?php echo $cssClass; ?>">

<!-- add task window-->
<div id="add-task-modal" class="modal" style="display: none;">
    
  <span class="close">&times;</span>
  <form method="POST">
  <input class="form-group" type="text" id="title" name="title" placeholder="Input new task here" required>
  <textarea class="form-group" id="description" name="description" placeholder="Describe your task"></textarea>
  <input class="form-group" type="datetime-local" id="due-date" name="due-date" required>
  <select class="form-group" id="category" name="category">
  <option value="create-new">No category</option>
  <?php $query = "SELECT name FROM category";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
      
      while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
      }
    }?>
      <option value="create-new">+ Create New</option>
    </select>
   <label for="priority">Priority:</label><br>
  <select class="form-group" id="priority" name="priority" required >
    <option value="high">High</option>
    <option value="medium">Medium</option>
    <option value="high">Low</option>
  </select>
  <input type="hidden" name="action" value="addtask">
  <button class="add-task-btn" type="submit"><i class="fas fa-paper-plane"></i></button>
  </form>
</div>

<!-- add new category-->
<div id="new-category" class="modal" style="display: none;">
 <span class="close">&times;</span>
  <form method="POST">
 <input class="form-group" id="newcategory" name="newcategory" placeholder="Input here."></input>
 <input type="hidden" name="action" value="addcategory">
    <button type="submit">Save</button>
 <button type="button" onclick="cancelnewcat()">Cancel</button></form>
</div>

<!-- task details-->
<div id="task-details" class="task-details-window" style="display: none;">
  <span class="close-details">&times;</span>
  <h3 class="task-title"></h3>
  <div class="status" style="text-align:center;margin-top:4%;margin-bottom:3%;">
  <form method="post" style="display: inline-block;width:30%;">
  <input type="hidden" name="task-id" value="" id="edit-task-id-input-todo">
  <input type="hidden" name="action" value="settodo"> 
  <button class="statusclick" type="submit" style="margin-right:1.5%; border-radius: 50%; background-color: white;"><i class="fas fa-tasks fa-lg" style="color:#ed89a9;"></i>    TODO</button>
  </form>
  <form method="post" style="display: inline-block;width:30%">
  <input type="hidden" name="task-id" value="" id="edit-task-id-input-done">
  <input type="hidden" name="action" value="setdone">
  <button class="statusclick" type="submit" style="margin-right:1.5%; border-radius: 50%; background-color: white;"><i class="fas fa-check-circle fa-lg" style="color:#ed89a9;"></i>    SET DONE</button>
  </form>
  <form method="post" style="display: inline-block;width:30%;">
  <input type="hidden" name="task-id" value="" id="edit-task-id-input-arc">
  <input type="hidden" name="action" value="setarc"> 
  <button class="statusclick" type="submit" style="margin-right:1.5%; border-radius: 50%; background-color: white;"><i class="fas fa-file-archive fa-lg" style="color:#ed89a9"></i>    ARCHIVE</button>
  </form>
 </div>
  <p class="task-description"></p>
  <p class="task-due-date"></p>
  <p class="task-category"></p>
  <p class="task-priority"></p>
  <a id="edit-task-btn" style="text-decoration: none;">
    <button  class="statusclick" style="border-radius: 53%;background-color:#b60155 ;">
    <i class="fas fa-edit" style="color: white;"></i>
    </button>
  </a>
  <a id="delete-Task-Btn" style="text-decoration: none;">
  <button  class="statusclick" style="border-radius: 50%; margin-left: 80%; background-color: #b60155;">
    <i class="fas fa-trash-alt" style="color: white;"></i>
  </button>
 </a>
</div>

<!-- delete task--> 
<div class="delete-task-form task-details-window"  id="delete-task-form"style="display: none;">
 <p>Delete Task?</p>
  <form  method="post">
  <h3 class="task-title"></h3>
  <input type="hidden" name="task-id" value="" id="delete-task-id-input">
  <input type="hidden" name="action" value="deletetask">
  <button id="confirm-delete-btn">Delete</button>
  <button type="button" onclick="canceldelete()">Cancel</button>
 </form>
</div>

<!-- edit task-->
<div id="edit-task-form" class="edit-task-form task-details-window" style="display: none;">
  <form method="POST">
    <input type="text" name="edit-title" id="edit-title" placeholder="Edit task title" required>
    <textarea name="edit-description" id="edit-description" placeholder="Edit task description"></textarea>
    <input type="datetime" name="edit-due-date" id="edit-due-date" required>
    <select name="edit-category" id="edit-category">
    
    <?php $query = "SELECT name FROM category";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
      
      while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
      }
    }?>
      
    </select>
    <select name="edit-priority" id="edit-priority">
    <option value="low">Low</option>
    <option value="medium">Medium</option>
     <option value="high">High</option>
    </select>
    <input type="hidden" name="task-id" value="" id="edit-task-id-input">
    <input type="hidden" name="action" value="edittask">
    <button type="submit">Save</button>
    <button type="button" onclick="cancelEdit()">Cancel</button>
  </form>
</div>
<!-- done task-->
<div id="done-task-form" class="task-details-window" style="display: none;">
 <span class="close-done close">&times;</span>
  <p style="text-align: center;">Congratulations!<br> You should be proud of your accomplishment<br>for completing the task successfully.<br>
   Well done!</p>
   <a id="delete-DoneTask-Btn" style="text-decoration: none;">
  <button style="border-radius: 50%; margin-left: 80%; background-color: #b60155;">
    <i class="fas fa-trash-alt" style="color: white;"></i>
  </button>
 </a>
</div>

<!-- delete done-->
<div class=" task-details-window"  id="delete-Donetask-form"style="display: none;">
 <p>Delete Task?</p>
  <form  method="post">
  <h3 class="task-title"></h3>
  <input type="hidden" name="task-id" value="" id="delete-Donetask-id-input">
  <input type="hidden" name="action" value="deletetask">
  <button id="confirm-delete-btn">Delete</button>
  <button type="button" onclick="canceldeletedone()">Cancel</button>
 </form>
</div>

<!--l page feha l tasks ma3 onclick la kel task-->
<div class="containerr"> 
   <div class="boxes">
   
    <div class="sorting-options">
  <?php if ($sorting === 'priority') {  ?>
      <p>LOW                              ←   PRIORITY   →                             HIGH</p>
    <?php } else {?>
      <p>                                 ←   DUE DATE   ←                                 </p>
    <?php }?>
     
    </div>
  <?php $todotaskTitles = '';
    $donetaskTitles = '';
    $archivedtaskTitles = '';
    $tododate= '';
    $query = "SELECT * FROM task WHERE user_id='$userid' and project_id=0 ORDER BY "; // l tasks la kel youm not project 
    if ($sorting === 'priority') {
    $query .= "FIELD(priority, 'Low', 'Medium', 'High') DESC";
     } else {
     $query .= "due_date DESC";
     }
   $result = mysqli_query($con, $query);
   if (mysqli_num_rows($result) > 0):
   foreach ($result as $row) {
    $status = $row['status'];
    $id=$row['task_id'];
    $title = $row['task_title'];
    $description=$row['task_description'];
    $date=$row['due_date'];
    $category=$row['category_id'];
    $status=$row['status'];
    $categorygroupsql="select name from category where id='$category'";
    $result = mysqli_query($con, $categorygroupsql);
    $categorygroup= null;;
    if ($result && mysqli_num_rows($result) > 0) {
      $row1 = mysqli_fetch_assoc($result);
      $categorygroup=$row1['name'];
     }
    $priority=$row['priority'];
    if ($status == 'todo') {
      if (strtotime($date) < strtotime(date('Y-m-d H:i:s'))) {
        $todotaskTitles .= "<li id='$id' title='$title' data-description='$description' data-date='$date' data-category='$categorygroup' data-priority='$priority' status='$status' onclick='openTaskDetails($id)'><h6>$title</h6><p style='color:red;'>$date</p></li>";
      } else {
        $todotaskTitles .= "<li id='$id' title='$title' data-description='$description' data-date='$date' data-category='$categorygroup' data-priority='$priority' status='$status' onclick='openTaskDetails($id)'>$title</li>";
      }
     
    }
   elseif ($status == 'done') {
     $donetaskTitles .="<li id='$id' title='$title' data-description='$description' data-date='$date' data-category='$categorygroup' data-priority='$priority' status='$status' onclick='opendone($id)'>$title</li>";
    } 
    else {
      $archivedtaskTitles .= "<li id='$id' title='$title' data-description='$description' data-date='$date' data-category='$categorygroup' data-priority='$priority' status='$status' onclick='openTaskDetails($id)'>$title</li>";
    }
   }
  endif;?>
  <!--todo-->
  <div class="container-fluid">
  <div class="row">
  <div class="col-sm-12 col-md-4" style="padding: 0;">
  <div class="box">
  <label class="boxeslabel">TO-DO&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/1680985828958kk.png" class="icon"></label>
  <div class="boxestask" style="margin-left: 0%;">
    <ul id="task-detail" class="task-list">
      <?php echo $todotaskTitles; ?>
    </ul>
  </div>
  </div>
  </div>
  <!--DONE-->
  <div class="col-sm-12 col-md-4" style="padding: 0;">
  <div class="box">
  <label class="boxeslabel">DONE&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/1680985816311pp.png" class="icon"></label>
  <div class="boxestask">
    <ul id="task-detail" class="task-list"><?php echo $donetaskTitles; ?></ul>
  </div>
  </div>
  </div>
  <!--ARCHIVED-->
  <div class="col-sm-12 col-md-4" style="padding: 0;">
  <div class="box">
  <label class="boxeslabel">ARCHIVED&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/1680985803078aa.png" class="icon"></label>
  <div class="boxestask" style="margin-right: 0%;">
    <ul  id="task-detail" class="task-list"><?php echo $archivedtaskTitles; ?></ul>
  </div>
  </div>
  </div></div>
  <button id="add-task-btn" class="newtask"><a style="color: white;text-decoration:none"><i class="fas fa-plus"></i></a></button>
  
   
</div>    

<script>  
//add new task lama click add task btn display l window , 3al eventlistener, close other window, 
   document.addEventListener('DOMContentLoaded', function() {
   document.getElementById("add-task-btn").addEventListener("click", function() {
   document.getElementById("add-task-modal").style.display = "block";
   document.getElementById("edit-task-form").style.display = "none";
   document.getElementById("delete-task-form").style.display = "none";
   document.getElementById("task-details").style.display = "none";});
   document.getElementsByClassName("close")[0].addEventListener("click", function() { // close click
   document.getElementById("add-task-modal").style.display = "none";
   document.getElementById("new-category").style.display = "none";
});})
// boxes height
   window.addEventListener('DOMContentLoaded', () => {
   const boxestasks = document.querySelectorAll('.boxestask');
   boxestasks.forEach(boxestask => {
    const taskList = boxestask.querySelector('.task-list');
    const height = taskList.offsetHeight + 10;
    boxestask.style.height = `${height}px`;
  });
});
//done task
function opendone(id){
  currentTaskId = id;
  // fathet window
  var taskDetails = document.getElementById("done-task-form");
  taskDetails.style.display = "block";
  document.getElementById("add-task-modal").style.display = "none";
  // close
  var closeDetailsBtn = document.getElementsByClassName("close-done")[0];
   closeDetailsBtn.addEventListener("click", function() {
    taskDetails.style.display = "none";})
}
// open task details
function openTaskDetails(id) {
  currentTaskId = id;
  //from the task database ba3ed ma 3mlt select all bel container
  var taskTitle = document.getElementById(id).getAttribute("title");
  var taskDescription = document.getElementById(id).getAttribute("data-description");
  var taskDueDate = document.getElementById(id).getAttribute("data-date");
  var taskCategory = document.getElementById(id).getAttribute("data-category");
  var taskPriority = document.getElementById(id).getAttribute("data-priority");
  // l id ll input to get the id of each task
  document.getElementById("edit-task-id-input").value = currentTaskId;
  document.getElementById("edit-task-id-input-done").value = currentTaskId;
  document.getElementById("edit-task-id-input-arc").value = currentTaskId;
  document.getElementById("edit-task-id-input-todo").value = currentTaskId;
  // get by id l items yale bel window,
  var taskTitleElement = document.getElementsByClassName("task-title")[0];
  var taskDescriptionElement = document.getElementsByClassName("task-description")[0];
  var taskDueDateElement = document.getElementsByClassName("task-due-date")[0];
  var taskCategoryElement = document.getElementsByClassName("task-category")[0];
  var taskPriorityElement = document.getElementsByClassName("task-priority")[0];
  // insert in the window
  taskTitleElement.textContent = taskTitle;
  taskDescriptionElement.textContent = taskDescription;
  taskDueDateElement.textContent = "Due date: " + taskDueDate;
  taskCategoryElement.textContent = taskCategory;
  taskPriorityElement.textContent = "Priority: " + taskPriority;
  // fathet ldetails window
  var taskDetailsWindow = document.getElementById("task-details");
  taskDetailsWindow.style.display = "block";
  document.getElementById("add-task-modal").style.display = "none";
  // close
  var closeDetailsBtn = document.getElementsByClassName("close-details")[0];
   closeDetailsBtn.addEventListener("click", function() {
    taskDetailsWindow.style.display = "none";
  });
  
}
// Delete task 
 var currentTaskId = null;
 var deleteBtn = document.getElementById("delete-Task-Btn");
 deleteBtn.addEventListener("click", function() {
    var deleteForm = document.querySelector(".delete-task-form"); // the first element that matches a specified CSS selector
    document.getElementById("task-details").style.display = "none";
    deleteForm.style.display = "block";
    var deleteTaskIdInput = document.getElementById("delete-task-id-input");
    deleteTaskIdInput.value = currentTaskId;
 });
   function canceldelete() {
    
    document.getElementById("delete-task-form").style.display = "none";
    document.getElementById("task-details").style.display = "block";
}
// delete done tasks
 var deletedonebtn=document.getElementById("delete-DoneTask-Btn");
 deletedonebtn.addEventListener("click", function() {
   var deleteDoneForm = document.getElementById("delete-Donetask-form");
   document.getElementById("task-details").style.display = "none";
    deleteDoneForm.style.display = "block";
    var deleteTaskIdInput = document.getElementById("delete-Donetask-id-input");
    deleteTaskIdInput.value = currentTaskId;
 });
 function canceldeletedone() {
  document.getElementById("delete-Donetask-form").style.display = "none";
  document.getElementById("done-task-form").style.display = "block";
}

// edit task
 var editBtn = document.getElementById("edit-task-btn");
 editBtn.addEventListener("click", function() {
  var taskId = currentTaskId;
  var taskTitle = document.getElementById(taskId).textContent;
  var taskDescription = document.getElementById(taskId).getAttribute("data-description");
  var taskDueDate =  document.getElementById(taskId).getAttribute("data-date"); 
  var taskCategory = document.getElementById(taskId).getAttribute("data-category");
  var taskPriority = document.getElementById(taskId).getAttribute("data-priority");

  // Fill the edit form with the task details
  document.getElementById("edit-task-id-input").value = taskId;
  document.getElementById("edit-title").value = taskTitle;
  document.getElementById("edit-due-date").value=taskDueDate;
  document.getElementById("edit-description").value = taskDescription;
  document.getElementById("edit-category").value = taskCategory;
  document.getElementById("edit-priority").value = taskPriority;
  //display the form
  var editForm = document.querySelector(".edit-task-form");
  document.getElementById("task-details").style.display = "none";
  editForm.style.display = "block";
  var editTaskIdInput = document.getElementById("edit-task-id-input");
  editTaskIdInput.value = currentTaskId;
 });
 function cancelEdit() {
  document.getElementById("edit-task-form").style.display = "none";
  document.getElementById("task-details").style.display = "block";
}
 // add new category 
  const categorySelect = document.getElementById("category");
  const newCategoryModal = document.getElementById("new-category");
  categorySelect.addEventListener("change", function() {
  const selectedValue = this.value;
  if (selectedValue === "create-new") {
  newCategoryModal.style.display = "block";
  } else {
    newCategoryModal.style.display = "none";
  }
  });
  function cancelnewcat() {
     document.getElementById("add-task-modal").style.display = "block";

    document.getElementById("new-category").style.display = "none";
}   
</script>

</body>
</html>

