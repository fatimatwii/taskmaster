<?php session_start();
 include('dbconfig/connect.php');
 require("vendor/autoload.php");
  use SendinBlue\Client\Configuration;
  use SendinBlue\Client\Api\TransactionalEmailsApi;
  $_SESSION['reset_code']='';
 if($_SERVER['REQUEST_METHOD'] == "POST"){
  $email = $_POST['email'];
  $code = generateRandomCode();
  $_SESSION['reset_code'] = $code;
  $_SESSION['reset_email']=$email;
  sendResetEmail($email, $code);
  header("Location: verifycode.php");
  exit();
}
function generateRandomCode($length = 6) {
  $characters = '0123456789';
  $code = '';
  $max = strlen($characters) - 1;
  for ($i = 0; $i < $length; $i++) {
  $code .= $characters[mt_rand(0, $max)];
  }
  return $code;
}

function sendResetEmail($email, $code) {
  $apiKey = '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';
  $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
  $apiInstance = new TransactionalEmailsApi(null, $config);
  $emailParams = [
    'sender' => ['email' => 'taskmasterapp01@gmail.com'],
    'to' => [['email' => $email]],
    'subject' => 'Password reset',
    'htmlContent' => '<p>Hello Dear,</p>
    <p>We received a request to reset your TASK MASTER password. If you initiated this request, please use the following code to reset your password:</p>
    <p>Reset Code: <strong>'.$code.'</strong></p>
    <p>If you did not request a password reset, please ignore this email.</p>
    <p>Thank you,</p>
    <p>TASK MASTER Team</p>'
  ];
  $response = $apiInstance->sendTransacEmail($emailParams);
  $Message='';
  if ($response->getMessageId()) {
    $Message = 'Email sent successfully.';
} else {
    $Message = 'Failed to send email.' ;
}
}
?>
