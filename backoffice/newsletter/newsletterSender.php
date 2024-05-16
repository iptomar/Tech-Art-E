<html>

<head>
  <meta charset="UTF-8">
</head>
<?php

header('Content-Type: text/html; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../libraries/lib-smtp/Exception.php';
require '../../libraries/lib-smtp/PHPMailer.php';
require '../../libraries/lib-smtp/SMTP.php';

// Receber os dados do e-mail do JavaScript
$checkedItems = json_decode(file_get_contents('php://input'), true);
$checkedProjectsJSON = $checkedItems['checkedProjects'];
$checkedReceiversJSON = $checkedItems['checkedReceivers'];
$checkedNewsJSON = $checkedItems['checkedNews'];
$checkedSend2AllJSON = $checkedItems['sendToAll'];
$checkedSend2AllResearchersJSON = $checkedItems['sendToAllR'];
echo json_encode($checkedProjectsJSON);
echo json_encode($checkedReceiversJSON);
echo json_encode($checkedNewsJSON);
echo json_encode($checkedSend2AllJSON);

// Verificar se os dados foram recebidos com sucesso
if ($checkedItems === null) {
  // Se os dados não puderem ser decodificados, houve um erro
  $response = ['error' => 'Erro ao receber os dados.'];
} else {
  // Os dados foram recebidos com sucesso
  // Agora podemos fazer o que quisermos com os dados
  // Neste exemplo, apenas retornaremos os dados recebidos
  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->Mailer = "smtp";

  $mail->SMTPDebug = 1;
  $mail->SMTPAuth = TRUE;
  $mail->SMTPSecure = "tls";
  $mail->Port = 587;
  $mail->Host = "smtp.gmail.com";
  $mail->Username = "tiago0liveira.dev.test@gmail.com";
  $mail->Password = "zpzr aewz cxda brjm";

  $mail->CharSet = "UTF-8";
  $mail->Encoding = "base64";

  $mail->IsHTML(true);
  $mail->ContentType = 'text/html; charset=utf-8';
  //$mail->AddAddress("tiago_oliveira2001@hotmail.com", "recipient-name");
  $mail->SetFrom("tiago0liveira.dev.test@gmail.com", "TechnArt");
  //$mail->AddReplyTo("iago_oliveira2001@hotmail.com", "reply-to-name");
  //$mail->AddCC("iago_oliveira2001@hotmail.com", "cc-recipient-name");

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

  foreach ($checkedProjectsJSON as $project) {
    $titulo = $project['titulo'];
    $desc = $project['descricao'];
    $img = $project['imgid'];

    //echo "Título: $titulo, Descrição: $desc, Imagem: $img<br>";

    $content .= '<div style="margin-bottom: 20px; display: flex; align-items: center;">';
    $content .= "<img src='http://novotechneart.ipt.pt/backoffice/assets/projetos/" . $img . "' class='project-image' alt='ImagemProjeto' width='350' height='250'>";
    $content .= '<div style="margin-left: 20px;">';
    $content .= "<p><b>" . $titulo . "</b></p>";
    $content .= "<p>" . $desc . "</p>";
    $content .= "</div>";
    $content .= "</div>";


  }

  foreach ($checkedNewsJSON as $new) {
    $titulo = $new['titulo'];
    $desc = $new['descricao'];
    $img = $new['imgid'];

    //echo "Título: $titulo, Descrição: $desc, Imagem: $img<br>";

    $content .= '<div style="margin-bottom: 20px; display: flex; align-items: center;">';
    $content .= "<img src='http://novotechneart.ipt.pt/backoffice/assets/noticias/" . $img . "' class='project-image' alt='ImagemNoticia' width='350' height='250'>";
    $content .= '<div style="margin-left: 20px;">';
    $content .= "<p><b>" . $titulo . "</b></p>";
    $content .= "<p>" . $desc . "</p>";
    $content .= "</div>";
    $content .= "</div>";


  }

  $content .= "</div>";
  $content .= '</body>';
  $content .= '</html>';


  foreach ($checkedReceiversJSON as $email) {
    $mail->AddAddress($email["email"]);
  }

  if ($checkedSend2AllJSON) {
    // Incluir arquivo de ligação com a base de dados
    require '../config/basedados.php';

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, $charset);

    $sql = "SELECT email FROM newsletter";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {

        $mail->AddAddress($row["email"]);
      }
    } else {
      echo "0 resultados";
    }

  }

  if ($checkedSend2AllResearchersJSON) {
    // Incluir arquivo de ligação com a base de dados
    require '../config/basedados.php';

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, $charset);

    $sql = "SELECT email FROM investigadores";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {

        $mail->AddAddress($row["email"]);
      }
    } else {
      echo "0 resultados";
    }

  }

  $mail->MsgHTML($content);

  if (!$mail->Send()) {
    echo "Error while sending Email.";
    var_dump($mail);
  } else {
    echo "Email sent successfully";
  }
  $response = ['message' => 'Dados recebidos com sucesso.', 'data' => $checkedItems];
}




http_response_code(200);
exit;
?>