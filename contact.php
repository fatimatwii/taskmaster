<?php
require_once 'config.php';
require("vendor/autoload.php");
session_start();
include('dbconfig/connect.php');
if (empty($_SESSION['user_info'])) {
header("Location: login.php");
exit();  }
 $title = "Contact Us";
 $userid=$_SESSION['user_info']['id'];
 require_once "headerr.php";
 // theme
 $query="select color_scheme from user_settings where user_id='$userid'";
 $result = mysqli_query($con, $query);
 if ($result && mysqli_num_rows($result) > 0) {
 $row = mysqli_fetch_assoc($result);
 $cssbutton = ($row['color_scheme'] === 'dark') ? 'darkbutton' : 'lightbutton';}
 use SendinBlue\Client\Configuration;
 use SendinBlue\Client\Api\TransactionalEmailsApi;

 $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', API_KEY);
 $apiInstance = new TransactionalEmailsApi(null, $config);
 
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $name = $_POST['name'];
     $email = $_POST['email'];
     $message = $_POST['message'];
     $emailParams = [
         'sender' => ['name' => $name, 'email' => $email],
         'to' => [['email' => 'taskmasterapp01@gmail.com']],
         'subject' => 'Your Email Subject',
         'htmlContent' => '<p>Name: '.$name.'</p><p>Email: '.$email.'</p><p>Message: '.$message.'</p>'
     ];
     $response = $apiInstance->sendTransacEmail($emailParams);
     if ($response->getMessageId()) {
         $successMessage = 'Email sent successfully.';
     } else {
         $errorMessage = 'Failed to send email.' ;
     }
 }
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/contactstyle.css">
    <title>Contact</title>
</head>
<body>
    <div class="container">
         <form method="POST" action="contact.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>

            <input class="<?php echo $cssbutton; ?>"  type="submit" value="Submit">
        </form>
        <?php if (isset($successMessage)) : ?>
            <div class="success-message" style="color: #c9378e;font-family: Verdana, sans-serif;font-size: 1rem ;font-weight: bolder;margin-top:1%"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if (isset($errorMessage)) : ?>
            <div class="error-message" style="color: red;font-family: Verdana, sans-serif;font-size: 1rem ;font-weight: bolder;margin-top:1%"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

       
    </div>
</body>
</html>
