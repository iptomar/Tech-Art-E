<?php
// Inclua o arquivo dbconnection.php
require "../config/dbconnection.php";

// Defina um array para armazenar a resposta
$response = array();

// Chame a função pdo_connect_mysql() para obter uma conexão PDO
try {
    $conn = pdo_connect_mysql();
    
    // Verifique se o método da requisição é POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifique se o campo de e-mail foi enviado e não está vazio
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            // Sanitize o e-mail
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            
            // Verifique se o email já existe na tabela
            $stmt_check = $conn->prepare("SELECT COUNT(*) AS count FROM newsletter WHERE email = ?");
            $stmt_check->execute([$email]);
            $result_check = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            if ($result_check['count'] > 0) {
                // Definir a mensagem de erro na resposta se o email já existir na tabela
                $response['status'] = 'error';
                $response['message'] = 'Este e-mail já está inscrito na newsletter.';
            } else {
                // Preparar e executar a consulta SQL para inserir o e-mail no banco de dados
                $stmt = $conn->prepare("INSERT INTO newsletter (email) VALUES (?)");
                $stmt->execute([$email]);

                // Definir a mensagem de sucesso na resposta
                $response['status'] = 'success';
                $response['message'] = 'Obrigado por se inscrever na nossa newsletter!';
            }
        } else {
            // Definir a mensagem de erro na resposta
            $response['status'] = 'error';
            $response['message'] = 'O campo de e-mail é obrigatório.';
        }
    }
} catch (PDOException $e) {
    // Definir a mensagem de erro na resposta
    $response['status'] = 'error';
    $response['message'] = 'Erro ao ligar à base de dados: ' . $e->getMessage();
}

// Enviar a resposta como JSON de volta para a página que chamou o script
header('Content-Type: application/json');
echo json_encode($response);
?>
