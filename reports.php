<?php
session_start();
 include('dbconfig/connect.php');
 if (empty($_SESSION['user_info'])) {
 header("Location: login.php");
 exit();  }
 $userrole=$_SESSION['user_info']['role'];
 $title = "Reports";
 require_once "headerr.php";
 ?>
 <!DOCTYPE html>
 <html lang="en">
 <head>

 <link rel="stylesheet" href="css/reportstyle.css">
 <title>Document</title>
 </head>
 <body>
  <table>
 <tr>
  <th>Project</th>
  <th>Tasks</th>
  <th>Done Tasks</th>
  <th>Progress</th>
  <th>Status</th>
  <th>Action</th>
 </tr>
  <?php
  $adminID = $_SESSION['user_info']['id'];
  if ($userrole=='Community Admin'){
  $query = "SELECT p.id, p.name, p.description, p.start_date, p.end_date, COUNT(t.task_id) AS total_tasks, SUM(t.status='Done') AS done_tasks
  FROM project_list p
  LEFT JOIN task t ON p.id = t.project_id
  WHERE p.communityadmin_id = $adminID
  GROUP BY p.id, p.name, p.start_date, p.end_date";
  }
  elseif ($userrole == 'Project Manager'){
  $query = "SELECT p.id, p.name, p.description,p.start_date, p.end_date, COUNT(t.task_id) AS total_tasks, SUM(t.status='Done') AS done_tasks
  FROM project_list p
  LEFT JOIN task t ON p.id = t.project_id
  where  p.manager_id = $adminID
  GROUP BY p.id, p.name, p.start_date, p.end_date";
  }
  $result = mysqli_query($con, $query);
  if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
  $projectName = $row['name'];
  $description=$row['description'];
  $totalTasks = $row['total_tasks'];
  $doneTasks = $row['done_tasks'];
  $startDate = $row['start_date'];
  $endDate = $row['end_date'];
  $progress = ($totalTasks > 0) ? round(($doneTasks / $totalTasks) * 100) : 0;
  $status = (strtotime($endDate) < time()) ? '<span style="color: red;">Delayed (' . $endDate . ')</span>' : (($progress == 100) ? 'Done' : 'On Track');
  echo "<tr>";
  echo "<td>$projectName</td>";
  echo "<td>$totalTasks</td>";
  echo "<td>$doneTasks</td>";
  echo "<td>$progress %</td>";
  echo "<td>$status</td>";
  echo "<td><a href='generate_report.php?projectName=" . urlencode($projectName) . "&description=" . urlencode($description) . "&totalTasks=" . urlencode($totalTasks) . "&doneTasks=" . urlencode($doneTasks) . "&startDate=" . urlencode($startDate) . "&endDate=" . urlencode($endDate) . "&progress=" . urlencode($progress) . "&status=" . urlencode($status) . "'><button style='background-color:#f855a6d0;border-radius: 5%;font-size:1rem;'><i class='fas fa-print'></i>Print Report</button></a></td>";

  echo "</tr>";
  }
  } else {
  echo "<tr><td colspan='6'>No projects found.</td></tr>";
  }
  ?>
</table>
 </body>
 </html>