<?php
header('Content-Type: application/json; charset=utf-8');

$envPath = __DIR__ . '/../.env';
$apiKey = '';

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0 || empty($line))
            continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            if (trim($key) === 'GEMINI_API_KEY') {
                $apiKey = trim($value);
                $apiKey = trim($apiKey, '"\'');
                break;
            }
        }
    }
}

if (empty($apiKey)) {
    echo json_encode(["error" => "API Key no configurada en el servidor."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$userText = $data['text'] ?? '';

if (empty($userText)) {
    echo json_encode(["error" => "No se ha recibido texto."]);
    exit;
}

$systemPrompt = "Eres PimasBot, el asistente Inteligente TCG de la prestigiosa tienda PokePimas. Eres experto, amable y breve. Das soluciones rápidas de e-commerce (envíos en 24h/48h asegurados, metodos de pago como tarjeta, bizum y paypal seguros, productos 100% verificados y devoluciones en 14 dias para sellados). Usa unos pocos emojis si es necesario, sin pasarte. Responde medianamente breve. Responde la siguiente duda del usuario: ";

$body = [
    "contents" => [
        [
            "parts" => [
                ["text" => $systemPrompt . $userText]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
        "maxOutputTokens" => 2500
    ]
];

$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(["error" => "CURL Error: " . curl_error($ch)]);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $errorData = json_decode($response, true);
    if (isset($errorData['error']['code']) && $errorData['error']['code'] == 503) {
        echo json_encode(["error" => "Hay mucha demanda, espera unos segundos"]);
    } else {
        echo json_encode(["error" => "Error de la API de Gemini: " . $response]);
    }
    exit;
}

$responseData = json_decode($response, true);
$modelReply = "Uf, mis pensamientos se entrecruzaron. 😵 ¿Podrías repetirlo?";

if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $modelReply = $responseData['candidates'][0]['content']['parts'][0]['text'];
}

$modelReply = str_replace("\n", "<br>", $modelReply);

echo json_encode(["reply" => $modelReply]);
?>