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
$mail->Username   = "";
$mail->Password   = "";

$mail->CharSet = "UTF-8";
$mail->Encoding = "base64";

$mail->IsHTML(true);
$mail->AddAddress("tiago_oliveira2001@hotmail.com", "recipient-name");
$mail->SetFrom("", "TechnArt");
//$mail->AddReplyTo("iago_oliveira2001@hotmail.com", "reply-to-name");
//$mail->AddCC("iago_oliveira2001@hotmail.com", "cc-recipient-name");
$mail->Subject = "TechnArt - Newsletter";

$content = '';

// Adiciona o cabeçalho do e-mail ao conteúdo
$content .= '<!DOCTYPE html>';
$content .= '<html lang="pt">';
$content .= '<head>';
$content .= '<meta charset="UTF-8">';
$content .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
$content .= '<title>TechnArt - Newsletter</title>';
$content .= '<style>';
// Estilos CSS aqui (opcional)
$content .= '</style>';
$content .= '</head>';
$content .= '<body>';

// Adiciona o corpo do e-mail ao conteúdo
$content .= '<div>';
//$content .= '<h1>Olá, ' . $nome . '!</h1>';
$content .= '<h1> TechnArt - Newsletter </h1>';

foreach ($dadosEmail as $projeto) {
  echo $projeto . '; ';

  $array = explode("||", $projeto);

  $imgtest = "5c.JPG";
  
  $content .= "<p><b>" . $array[0] . "</b></p>";
  $content .= "<p>" . $array[1] . "</p>";
  $content .= "<p> <img src='../assets/projetos/'". $imgtest . " class='project-image' alt='ImagemProjeto'></p>";
}

$content .= '</div>';
$content .= '<div>';
$content .= '<p>Mais info ...</p>';
$content .= '</div>';

// Fecha o corpo do e-mail
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