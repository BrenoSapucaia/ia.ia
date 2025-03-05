<?php
header('Content-Type: text/plain');

// Recebe a mensagem do usuário
$message = isset($_POST['message']) ? $_POST['message'] : '';
if (empty($message)) {
    echo "Por favor, envie uma mensagem.";
    exit;
}

// Configurações da API da Cohere
$apiKey = 'BQ4X0yXursJ95bTGjRKC2ZoJnbhD0qy9KDDyDvTs'; // Substitua pela sua chave de API da Cohere
$url = 'https://api.cohere.ai/v1/generate';

// Dados para a requisição
$data = [
    'prompt' => $message,
    'max_tokens' => 100, // Limite de tokens na resposta
    'temperature' => 0.7, // Controla a criatividade (0.0 a 1.0)
    'k' => 0, // Top-k sampling (0 desativa)
    'p' => 0.95, // Top-p sampling
    'stop_sequences' => ["\n"], // Para a geração em uma nova linha
];

// Inicializa a requisição cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json',
]);

// Executa a requisição
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Verifica erros
if ($response === false || $httpCode !== 200) {
    $error = curl_error($ch);
    echo "Erro na API da Cohere: " . ($error ? $error : "Código HTTP $httpCode");
    curl_close($ch);
    exit;
}

// Decodifica a resposta JSON
$responseData = json_decode($response, true);
curl_close($ch);

// Extrai o texto gerado
if (isset($responseData['generations'][0]['text'])) {
    $aiResponse = trim($responseData['generations'][0]['text']);
    echo $aiResponse;
} else {
    echo "Desculpe, não consegui gerar uma resposta.";
}
?>