<?php
require 'vendor/autoload.php';

use Github\Client;
use Github\Exception\RuntimeException;

header('Content-Type: application/json');

// Configurações de exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Token codificado em base64
$tokenBase64 = 'Z2hwX3pxREFZSkVCaUU1VWR4TzBGclFwSHE2TFlEZGxJVjNFWmZZQw=='; // Substitua com seu token codificado

// Decodifica o token do formato base64
$apiKey = base64_decode($tokenBase64);

// Recebe os dados enviados via POST
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['url'])) {
    $repoUrl = $data['url'];

    // Extrai o nome do proprietário e do repositório da URL
    $urlParts = parse_url($repoUrl);
    $pathParts = explode('/', trim($urlParts['path'], '/'));

    if (count($pathParts) >= 2) {
        $owner = $pathParts[0];
        $repo = $pathParts[1];

        try {
            // Configura o cliente do GitHub com a API Key decodificada
            $client = new Client();
            $client->authenticate($apiKey, null, Client::AUTH_ACCESS_TOKEN);

            // Verifica as GitHub Actions do repositório
            $workflows = $client->api('repo')->workflow()->all($owner, $repo);

            if (!empty($workflows['workflows'])) {
                echo json_encode(['actionsExist' => true, 'workflows' => $workflows['workflows']]);
            } else {
                echo json_encode(['actionsExist' => false]);
            }
        } catch (RuntimeException $e) {
            echo json_encode(['error' => 'Erro ao acessar a API do GitHub: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'URL inválida.']);
    }
} else {
    echo json_encode(['error' => 'URL não fornecida.']);
}
