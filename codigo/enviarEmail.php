<?php
header("Content-Type: application/json");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL);
    $mensagem = htmlspecialchars($_POST["mensagem"] ?? '', ENT_QUOTES, 'UTF-8');
    $sobrenome = htmlspecialchars($_POST["sobrenome"] ?? '', ENT_QUOTES, 'UTF-8');
    $nome = htmlspecialchars($_POST["nome"] ?? '', ENT_QUOTES, 'UTF-8');

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['MAIL_PORT'];
        $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
        $mail->addReplyTo($email);
        $mail->addAddress($_ENV['MAIL_TO']);

        $mail->isHTML(true);
        $mail->Subject = 'Nova mensagem do formulário';
        $mail->Body = "
            <p><strong>Nome:</strong> $nome</p>
            <p><strong>Sobrenome:</strong> $sobrenome</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Mensagem:</strong> $mensagem</p>
        ";

        $mail->send();

        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "E-mail enviado com sucesso!"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Erro ao enviar o e-mail: " . $mail->ErrorInfo
        ]);
    }
} else {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Método inválido"
    ]);
}
