<html>
  <head>
  <meta charset="UTF-8">
  </head>
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
$mail->Username   = "tiago0liveira.dev.test@gmail.com";
$mail->Password   = "zpzr aewz cxda brjm";

$mail->CharSet = "UTF-8";
$mail->Encoding = "base64";

$mail->IsHTML(true);
$mail->AddAddress("tiago_oliveira2001@hotmail.com", "recipient-name");
$mail->SetFrom("tiago0liveira.dev.test@gmail.com", "TechnArt");
//$mail->AddReplyTo("iago_oliveira2001@hotmail.com", "reply-to-name");
//$mail->AddCC("iago_oliveira2001@hotmail.com", "cc-recipient-name");
$mail->addCustomHeader("MIME-Version", "1.0");
$mail->addCustomHeader("Content-type", "text/html;charset=UTF-8");

$mail->Subject = "TechnArt - Newsletter";


$content = '';

$content = '
      <!DOCTYPE html>
      <html lang="en">
      <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Email</title>
      <style>
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
      }
      .x_container {
        max-width: 90%;
        padding: 8px;
      }
      .x_project-info {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
      }
      .x_project-text {
        margin-left: 20px;
      }
      h2 {
        color: #333;
      }
      p {
        color: #666;
      }
      .x_project-image {
        height: 90%;
        width: auto;
        max-height: 90%;
      }
      </style>
      </head>
      <body>
      ';

// Adiciona o corpo do e-mail ao conteúdo
$content .= '<div class="container">';
$content .= '<h1> TechnArt - Newsletter </h1>';


foreach ($dadosEmail as $projeto) {
  $array = explode("||", $projeto);
  $content .= '<div style="margin-bottom: 20px; display: flex; align-items: center;">';
  $content .= "<img src='http://novotechneart.ipt.pt/backoffice/assets/projetos/" .$array[2]. "' class='project-image' alt='ImagemProjeto' width='350' height='250'>";
  $content .= '<div style="margin-left: 20px;">';
  $content .= "<p><b>" . $array[0] . "</b></p>";
  $content .= "<p>" . $array[1] . "</p>";
  $content .= "</div>";
  $content .= "</div>";
}
$content .= "</div>";
$content .= '</body>';
$content .= '</html>';



 
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