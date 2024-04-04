<?php

header('Content-Type: text/html; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'lib/Exception.php';
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';

// Receber os dados do e-mail do JavaScript
$dadosEmail = json_decode(file_get_contents('php://input'), true);

//echo("123456");


$mail = new PHPMailer(); $mail->IsSMTP(); $mail->Mailer = "smtp";

$mail->SMTPDebug  = 1;  
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port       = 587;
$mail->Host       = "smtp.gmail.com";
$mail->Username   = "tiagoptgamer@gmail.com";
$mail->Password   = "aduz hgqr ebgy jekq";

$mail->IsHTML(true);
$mail->AddAddress("tiago_oliveira2001@hotmail.com", "recipient-name");
$mail->SetFrom("tiagoptgamer@gmail.com", "from-name");
$mail->AddReplyTo("iago_oliveira2001@hotmail.com", "reply-to-name");
$mail->AddCC("iago_oliveira2001@hotmail.com", "cc-recipient-name");
$mail->Subject = "Test is Test Email sent via Gmail SMTP Server using PHP Mailer";
$content = "<b>This is a Test Email sent via Gmail SMTP Server using PHP mailer class.</b>";

foreach ($dadosEmail as $projeto) {
  echo $projeto . '; ';
  $content .= $projeto . '; ';
}



 
$mail->MsgHTML($content); 
if(!$mail->Send()) {
  echo "Error while sending Email.";
  var_dump($mail);
} else {
  echo "Email sent successfully";
}

http_response_code(200);
exit;
?>