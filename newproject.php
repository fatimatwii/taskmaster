<?php
session_start();
include('dbconfig/connect.php');
if (empty($_SESSION['user_info'])) {
header("Location: login.php");
exit();  }
$title = "New Project";
$adminid = $_SESSION['user_info']['id'];
require "headerr.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
  body {
  background-color: #f5f4f4;
  }
  form {
  width: 80%;
  margin: 2% 0% 0% 10%;
  }
  .container {
  display: flex;
  margin-left: 7%;
  }
  input[type="text"],
  input[type="date"],
  select {
  width: 70%;
  border-radius: 5px;
  height: 10%;
  }
  .column {
  flex: 1;
  padding: 5px;
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
<title>New Project</title>
</head>
<body>
  <form method="POST" action="newprojectaction.php" id="manage-project">
    <div class="container">
      <div class="column">
        <label for="name" class="control-label">Name</label><br>
        <input type="text" id="name" name="name">
        <br>
        <label for="start_date" class="control-label" style="margin-top: 3%;">Start Date</label>
        <input type="date" id="start_date" class="form-control form-control-sm" autocomplete="off" name="start_date" value="<?php echo isset($start_date) ? date("Y-m-d", strtotime($start_date)) : '' ?>">
        <br>
        <label for="end_date" class="control-label">End Date</label>
        <input type="date" id="end_date" class="form-control form-control-sm" autocomplete="off" name="end_date" value="<?php echo isset($end_date) ? date("Y-m-d", strtotime($end_date)) : '' ?>">
        <br>
      </div>
      <div class="column" style="margin-top: 1%;">
      <label for="manager_id" class="control-label">Project Manager:</label><br>

     <?php
     $query = "SELECT * FROM user WHERE role='Project Manager' AND communityadmin_id='$adminid'";
     $result = mysqli_query($con, $query);
     if (mysqli_num_rows($result) > 0) {
      echo '<select multiple="multiple" name="manager_id[]" size="1" style="height: 45%;">';
      while ($row = mysqli_fetch_assoc($result)) {
      echo '<option value="' . $row['id'] . '">' . $row['username'] . '</option>';
     }
      echo '</select>';
    } else {
    echo '<p>No Managers in your community.</p>';
       }
       ?>
  <br>
<label for="manager_id" class="control-label">Project Employees:</label><br>
    <?php
    $query = "SELECT * FROM user where role='Employee' and communityadmin_id='$adminid'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
    echo '<select id="userSelect" name="user_ids[]" style="height: 45%;" multiple>';
    while ($row = mysqli_fetch_assoc($result)) {
    $id=$row['id'];
    echo '<option value="'. $id .'" data-id="' . $id . '">' . $row['username'] . '</option>';
   } 
   echo '</select>';
  } else {
  echo '<p>No Employee in your community.</p>';
     }
     ?>
      <div id="selectedUsers">
        Selected Users:</div>
        <input type="hidden" id="selectedUserIds" name="selectedUserIds" value="">
        <br>
      </div>
    </div>
    <div style="text-align: center;margin-top:5%;">
      <label for="description" class="control-label">Description:</label><br>
      <textarea id="description" name="description" cols="5" rows="5" style="width: 70%;height:10%"></textarea><br>
      <button type="submit">Save</button>
      <a href="panel.php" style="text-decoration: none;"><button type="button">Cancel</button></a>
    </div>
  </form>
  <script>
    var selectElement = document.getElementById("userSelect");
   var selectedUsersElement = document.getElementById("selectedUsers");
   var selectedUserIdsElement = document.getElementById("selectedUserIds");
   selectElement.addEventListener("change", function() {
  updateSelectedUsers();
});

function updateSelectedUsers() {
  var selectedOptions = Array.from(selectElement.selectedOptions).map(option => option.value);
  var selectedUserNames = Array.from(selectElement.selectedOptions).map(option => option.text);
  selectedUsersElement.textContent =  selectedUsersElement.textContent +" " + selectedUserNames.join(", ");
  if (selectedUserIdsElement.value === '') {
    selectedUserIdsElement.value = selectedOptions.join(",");
  }
  else{
   selectedUserIdsElement.value = selectedUserIdsElement.value+","+ selectedOptions.join();console.log( selectedUserIdsElement.value);
 }
  }
  </script>
</body>
</html>
