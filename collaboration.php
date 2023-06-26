<?php 
include('dbconfig/connect.php');
session_start();
$userrole=$_SESSION['user_info']['role'];
$userid=$_SESSION['user_info']['id'];
$title='Collaborations';
require "headerr.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deletecollaboration') {
  $collaborationId = $_POST['collaboration-id'];
  $deletechat = "DELETE FROM chat WHERE collaboration_id = $collaborationId";
  mysqli_query($con, $deletechat);
  $sql = "DELETE FROM collaboration WHERE id = '$collaborationId'";
  $result = mysqli_query($con, $sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'addchat') {
  $message = $_POST['message'];
  $collaborationId = $_POST['collaboration-id'];
  $senderid=$userid;
  $sql = "insert into chat (sender_id, message,collaboration_id) VALUES ('$senderid', '$message','$collaborationId')";
  $result = mysqli_query($con, $sql);
}
?>
<!DOCTYPE html>
<html>
<head>
<style>
  .chat-container {
  border: 1px solid #ccc;
  padding: 10px;
  margin-bottom: 1%;
  overflow-y: auto;
  }
 .chat-message {
  background-color: #f2f2f2;
  padding: 1%;
  margin-bottom: 1%;
  }
 .chat-message p {
  margin: 0;
  }
  .chat-message strong {
  font-weight: bold;
  }
 .chat-input {
  display: flex;
  margin-top: 10px;
  }
  .chat-input input[type="text"] {
  flex-grow: 1;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
  width:94%;
  margin-right: 1%;
  }
  .tab-content > .tab-pane {
  width: 100%;
  padding: 20px;
  box-sizing: border-box;
  }
  button:hover{ transform: scale(1.1);
  }
  .nav-link{
  color:#535353;
  }
  .nav-link:hover{
    transform: scale(1.1);
    color: #c9378e;
  }
  .nav-linkleft{
    color:red;
    text-decoration: none !important;
    margin-top:3%;
  }
  .nav-linkleft:hover{
    transform: scale(1.1);
    color: red;
    text-decoration: underline !important;
  }
</style>

  <title>Collaboration Pages</title>  
</head>
<body>
  <div class="container-fluid">
  <ul class="nav nav-tabs">
    <?php $linkclass='';
    $sql="SELECT * FROM collaboration";
    $collaborations = mysqli_query($con,$sql);
    while ($row = mysqli_fetch_assoc($collaborations)) {
    $collaborationId = $row['id'];
    $projectname=$row['projectname'];
    $date=$row['date'];
    $starttime=$row['start_time'];
    $dayMonth = date('d F', strtotime($date));
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s'); 
    
    if ($currentDate > $date || ($currentDate == $date && $currentTime > $starttime)) {
      $linkclass='nav-linkleft';
    } else {
      $linkclass='nav-link';
    }
    $starttime = $row['start_time'];
    $activeClass = ($collaborationId == 1) ? 'active' : '';
    echo '<li class="nav-item">';
    echo '<a class="'.$linkclass.'' . $activeClass . '" id="collab' . $collaborationId . '-tab" data-bs-toggle="tab" href="#collab' . $collaborationId . '"> at:'.$dayMonth.'"' . $starttime . '"</a>';
    echo '</li>';
    }
    ?>
    </ul>
   <div class="tab-content">
    <?php
    mysqli_data_seek($collaborations, 0);
    while ($row = mysqli_fetch_assoc($collaborations)) {
    $collaborationId = $row['id'];
    $activeClass = ($collaborationId == 1) ? 'show active' : ''; 
    $chatQuery = "SELECT c.*, u.username, u.role   FROM chat c INNER JOIN user u ON c.sender_id = u.id WHERE c.collaboration_id = $collaborationId";
   $chatResult = mysqli_query($con, $chatQuery);
    echo '<div class="tab-pane fade ' . $activeClass . '" id="collab' . $collaborationId . '">';
    echo '<h6>Collaboration for ' . $projectname . ' project.';
    if ($userrole=='Project Manager'){echo '<button style="border-radius: 50%; background-color: red !important;margin-left:75%;margin-top:1%;" onclick="deletecollaboration('.$collaborationId.')"><i class="fas fa-trash-alt" style="color: white;"></i></button></h6>';}
    else{
      echo '</h6>'; 
    }
    echo '<div class="chat-container">';
    while ($chatRow = mysqli_fetch_assoc($chatResult)) {
    $senderName = $chatRow['username'];
    $senderRole = $chatRow['role'];
    $message = $chatRow['message'];
    echo '<div class="chat-message">';
    echo '<p><strong>' . $senderName . ' (' . $senderRole . '):</strong></p>';
    echo '<p>' . $message . '</p>';
    echo '</div>';
    }
    echo '</div>';
    
    echo '<form method="post">';
    echo '<input type="hidden" name="collaboration-id" value="' . $collaborationId . '">';
    echo '<input type="text" name="message" placeholder="Write your message" style="width:93%">';
    echo '<input type="hidden" name="action" value="addchat">';
    echo '<button style="background-color: #f855a6d0; border-radius: 10px !important;margin-left: 1% !important;width:5% !important;height:3% !important;padding-left:2%"><i class="fas fa-paper-plane" style="color:white"></i></button>';
      
    echo '</form>';
    echo '</div>';
    }
    ?>
    </div>
  </div>
<!-- delete task-->
<div class="modal" id="delete-collaboration-form" style="margin-top:15%;width:30%;height:30%;margin-left:35%">
  <div class="modal-content">
    <h3 style="text-align:center;margin-top:5%;margin-bottom:10%">Delete Collaboration?</h3>
    <form method="post">
      <input type="hidden" name="collaboration-id" value="" id="collaboration-id">
      <input type="hidden" name="action" value="deletecollaboration">
      <button class="statusclick" id="confirm-delete-btn" style="margin-left: 30%; background-color: red;border-radius:10px;margin-bottom:4%">Delete</button>
      <button onclick="closeModal('delete-collaboration-form')"; class="statusclick" type="button" style="margin-left:5%;border-radius:10px;background-color: #f855a6d0">Cancel</button>
    </form>
  </div>
 </div>
 <script>
  collaborationId='';
  function deletecollaboration(id) {
  collaborationId = id;
  var collaboration = document.getElementById("collaboration-id");
  collaboration.value = collaborationId;
  var modal = document.getElementById("delete-collaboration-form");
  modal.style.display = "block";
  }
  function closeModal(formName) {
    var modal = document.getElementById(formName);
    modal.style.display = "none";
}

 </script>
</body>
</html>
