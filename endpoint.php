<?php
require 'vendor/autoload.php';

use Github\Client;
use Github\Exception\RuntimeException;

// Função para carregar as variáveis do arquivo .env
function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new Exception("Arquivo .env não encontrado");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
    return $env;
}

try {
    // Carrega a senha do arquivo .env
    $env = loadEnv(__DIR__ . '/.env');
    $encryptionKey = $env['ENCRYPTION_KEY'] ?? null;

    // echo -n "github_pat_..." | openssl enc -aes-256-cbc -a -salt -pass pass:xxxxxx
    if (!$encryptionKey) {
        throw new Exception("A chave de criptografia não foi encontrada no arquivo .env");
    }

    // Token criptografado em base64
    $encryptedToken = 'xxx';

    // Descriptografa o token usando OpenSSL
    $apiKey = openssl_decrypt(base64_decode($encryptedToken), 'aes-256-cbc', $encryptionKey, 0, substr($encryptionKey, 0, 16));

    if (!$apiKey) {
        throw new Exception("Falha na descriptografia do token");
    }

    header('Content-Type: application/json');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
