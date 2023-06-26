<?php ob_start(); 
$db_host="localhost";
$db_username="root";
$db_pass="";
$db_name="todolist";
$con=mysqli_connect($db_host,$db_username,$db_pass,$db_name);
if(mysqli_connect_errno()){
    echo"connection error";
    mysqli_connect_error();
    exit();

}
?>
