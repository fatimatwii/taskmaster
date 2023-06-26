<?php
require("vendor/autoload.php");
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
session_start();
include('dbconfig/connect.php');
$adminid = $_SESSION['user_info']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];
  $manager_ids = isset($_POST['manager_id']) ? $_POST['manager_id'] : array();
  $manager_id_string = implode(',', $manager_ids);
  $manageremailsql="select email,username from user where id='$manager_id_string'";
  $managerresult = mysqli_query($con, $manageremailsql);
  $manageremail='';
  $managername='';
  if ($managerresult && mysqli_num_rows($managerresult) > 0) {
    $row = mysqli_fetch_assoc($managerresult);
  $manageremail=$row['email'];
  $managername=$row['username'];
  }
  $description = $_POST['description'];
  $query = "INSERT INTO project_list (name, description, status, start_date, end_date, manager_id, date_created, communityadmin_id) VALUES ('$name', '$description', 'pending', '$start_date', '$end_date', '$manager_id_string', NOW(), '$adminid')";
  $result = mysqli_query($con, $query);
  $lastInsertedID = mysqli_insert_id($con);
  $selectedUserIds = $_POST['selectedUserIds'];
  var_dump($selectedUserIds);
  $userIds = explode(',', $selectedUserIds);
  foreach ($userIds as $userId) {
    // insert id la kel employee
   $insertQuery = "INSERT INTO project_members (project_id, user_id) VALUES ('$lastInsertedID', '$userId')";
   mysqli_query($con, $insertQuery);
   // beb3at email la kel employee
   $membernamesql="select username,email from user where id='$userId'";
   $memberresult = mysqli_query($con, $membernamesql);
   $membername='';
   $memberemail='';
   if ($memberresult && mysqli_num_rows($memberresult) > 0) {
     $row = mysqli_fetch_assoc($memberresult);
     $membername=$row['username'];
     $memberemail=$row['email'];
     // check l notifications men l settings
     $sqlnotifications="select email_notifications from user_settings where user_id='$userId'";
     $notificationsresult = mysqli_query($con, $sqlnotifications);
      if ($row = mysqli_fetch_assoc($notificationsresult)) {
      $emailNotifications = $row['email_notifications'];
      if ($emailNotifications == 1) {
     $apiKey = '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';
     // Create a configuration object
     $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
     $apiInstance = new TransactionalEmailsApi(null, $config);
     $emailParams = [
     'sender' => ['email' => 'taskmasterapp01@gmail.com'],
     'to' => [['email' => $memberemail]],
     'subject' => 'TASK MASTER:New Project Assignment',
     'htmlContent' => '
     <p>Dear '.$membername.',</p>
     <p>I hope this email finds you well. I wanted to inform you that a new project has been assigned to you. Below are the details of the project:</p>
      <p><strong>Project Name:</strong>'. $name.'</p>
      <p><strong>Assigned By:</strong>'.$_SESSION['user_info']['username'].'</p>
      <p><strong>Start Date:</strong>'.$start_date.'</p>
      <p><strong>Deadline:</strong>'.$end_date.'</p>
      <p><strong>Description:</strong>'.$description.'</p>
      <p>Please review the project details and make sure to familiarize yourself with the project requirements and objectives. If you have any questions or need clarifications, don\'t hesitate to reach out to your manager or the assigned project team.</p>
      <p>We trust in your skills and expertise to successfully execute this project. We appreciate your commitment and dedication in contributing to our team\'s success.</p>
      <p>Thank you for your attention to this matter.</p>
      <p>Best regards,</p>
      <p>'.$_SESSION['user_info']['username'].'</p>
      <p>[Community Admin]</p>'
  ];
  $response = $apiInstance->sendTransacEmail($emailParams);}}
  }
  }
  // email ll manager
  if ($result) {
    $sqlnotifications="select email_notifications from user_settings where user_id='$manager_id_string'";
    $notificationsresult = mysqli_query($con, $sqlnotifications);
    if ($row = mysqli_fetch_assoc($notificationsresult)) {
    $emailNotifications = $row['email_notifications'];
    if ($emailNotifications == 1) {
    $apiKey = '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';
    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
    $apiInstance = new TransactionalEmailsApi(null, $config);
    $emailParams = [
      'sender' => ['email' => 'taskmasterapp01@gmail.com'],
      'to' => [['email' => $manageremail]],
      'subject' => 'TASK MASTER:New Project Assignment',
      'htmlContent' => '
        <p>Dear '.$managername.',</p>
        <p>I hope this email finds you well. I would like to inform you that a new project has been assigned to our team. Below are the details of the project:</p>
        <p><strong>Project Name:</strong>'. $name.'</p>
        <p><strong>Assigned By:</strong>'.$_SESSION['user_info']['username'].'</p>
        <p><strong>Start Date:</strong>'.$start_date.'</p>
        <p><strong>Deadline:</strong>'.$end_date.'</p>
        <p><strong>Description:</strong>'.$description.'</p>
        <p>As the manager of our team, I kindly request your support and guidance in successfully executing this project. Your expertise and leadership will be instrumental in achieving the project goals.</p>
        <p>Please let me know if you have any questions or need further information regarding the project. I look forward to your valuable insights and guidance throughout the project\'s duration.</p>
        <p>Thank you for your attention to this matter.</p>
        <p>Best regards,</p>
        <p>'.$_SESSION['user_info']['username'].'</p>
        <p>[Community Admin]</p>'
    ];
    $response = $apiInstance->sendTransacEmail($emailParams);}}
    header("Location: panel.php");
    exit();
  } else {
    echo "Error inserting project: " . mysqli_error($con);
 }
  
}

mysqli_close($con);
?>
