<?php
session_start();
$projectName = $_GET['projectName'];
$description = $_GET['description'];
$totalTasks = $_GET['totalTasks'];
$doneTasks = $_GET['doneTasks'];
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$progress = $_GET['progress'];
$status = $_GET['status'];
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
$pdf = new TCPDF();
$pdf->SetCreator('Task Master');
$pdf->SetAuthor($_SESSION['user_info']['username']);
$pdf->SetTitle($projectName.' Report');
$pdf->AddPage();
$content = '<h4><strong>Project Name:</strong>'. $projectName.'</h4>
            <p><strong>Description:</strong>'.$description.'</p>
            <p><strong>Total Tasks:</strong>'.$totalTasks.'</p>
            <p><strong>Done Tasks:</strong>'.$doneTasks.'</p>
            <p><strong>Progress:</strong>'.$progress.'</p>
            <p><strong>Status:</strong>'.$status.'</p>
            <p><strong>Start Date:</strong>'.$startDate .'</p>
            <p><strong>End Date:</strong>'.$endDate.'</p>';
$pdf->writeHTML($content, true, false, true, false, '');
$pdf->Output('report.pdf', 'D');
?>
