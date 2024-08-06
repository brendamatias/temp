<?php
// Inclui o arquivo de conexão com o banco de dados
require 'db.php';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $smsProvider = $_POST['smsProvider'];
    $smsLogin = $_POST['smsLogin'];
    $smsPassword = $_POST['smsPassword'];

    // Valida se os campos obrigatórios foram preenchidos
    if (empty($smsProvider) || empty($smsLogin) || empty($smsPassword)) {
        echo "All fields are required.";
        exit;
    }

    // Prepara a query para inserir as configurações de SMS
    $sql = "INSERT INTO sms_configuration (sms_provider, sms_login, sms_password) VALUES (?, ?, ?)";

    try {
        // Prepara a declaração SQL
        $stmt = $pdo->prepare($sql);

        // Executa a query com os valores do formulário
        if ($stmt->execute([$smsProvider, $smsLogin, $smsPassword])) {
            echo "SMS configuration saved successfully.";
        } else {
            echo "Failed to save SMS configuration.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>

