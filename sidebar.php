<?php
include('dbconfig/connect.php');
$userrole = $_SESSION['user_info']['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
  .view-btn {
  position: fixed;
  top: 20%;
  left: 0;
  transform: translateY(-50%);
  width: 50px;
  height: 50px;
  border-radius: 50%;
  z-index: 999;
  transition: all 0.3s ease;
  box-shadow: 0 5px 30px #b60155 ;
  }

  .sidebar {
  background-color:#c9378e !important;
  position: fixed;
  top: 16%;padding-left: 3%;
  left: -20% ; 
  width: 50% !important;border-radius:10%;
  height: max-content;
  transition: left 0.3s ease;
  }

  .sidebar.show {
  left: 0;
  }
  a{
  color:#535353!important;
  }
  .nav-link:hover{
  transform: scale(1.05) !important;
  color:white !important;
  }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>
  <button class="btn btn-primary view-btn" type="button" data-target="#sidebar"> <i class="fas fa-bars"></i></button>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="sidebar-sticky" style="width:100%">
          <ul class="nav flex-column" style="width:100%">
          <?php if($userrole=="Employee"){?>
            <li class="nav-item" style="width:100%">
            <a class="nav-link" href="collaboration.php"><i class='fas fa-comments fa-lg circleicons'></i> Collaboration</a>
            </li>
            <li class="nav-item" style="width:100%">
              <a class="nav-link" href="dashboard.php"><i class="far fa-check-square fa-lg circleicons" ></i>   My To-Do List</a>
            </li>
            <li class="nav-item" style="width:100%">
            <a class="nav-link" href=""></a>
            </li>
            <li class="nav-item" style="width:100%">
            <a class="nav-link" href=""></a>
            </li>
            <?php } elseif($userrole=="Project Manager"){?>
            <li class="nav-item" style="width:100%">
            <a class="nav-link" href="collaboration.php"><i class='fas fa-comments fa-lg circleicons'></i> Collaboration</a>
            </li>
            <li class="nav-item" style="width:100%">
              <a class="nav-link" href="reports.php"><i class="fas fa-th-list fa-lg circleicons"></i>       Reports</a>
            </li>
            <li class="nav-item" style="width:100%">
              <a class="nav-link" href="listuser.php"><i class="fas fa-users fa-lg circleicons" ></i>       Users</a>
            </li>
            <li class="nav-item" style="width:100%">
              <a class="nav-link" href="dashboard.php"><i class="far fa-check-square fa-lg circleicons" ></i>   My To-Do List</a>
            </li>
          <?php } else { ?>
            <li class="nav-item" style="width:100%">
              <a class="nav-link" href="reports.php"><i class="fas fa-th-list fa-lg circleicons"></i>       Reports</a>
            </li>
            <li class="nav-item" style="width:100%">
              <a class="nav-link" href="listuser.php"><i class="fas fa-users fa-lg circleicons" ></i>       Users</a>
            </li>
            <!--<li class="nav-item" style="width:100%">
            <a class="nav-link" href="calendar.php"><i class="fas fa-calendar-alt"></i>       Calendar</a>
            </li>-->
            
            <li class="nav-item" style="width:100%">
              <a class="nav-link" href="dashboard.php"><i class="far fa-check-square fa-lg circleicons" ></i>   My To-Do List</a>
            </li>
            <?php } ?>
          </ul>
        </div>
      </nav>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      $(".view-btn").click(function() {
        $("#sidebar").toggleClass("show");
      });
    });
  </script> 
</body>
</html>
