<?php

// Réglages pour le reporting d'erreurs et l'encodage
error_reporting(E_ALL);
ini_set('display_errors', 0); // Mettre à 1 pour le débogage, 0 en production
ini_set('log_errors', 1);
// Assurez-vous que le chemin du fichier de log est accessible en écriture par le serveur
// ini_set('error_log', __DIR__ . '/../logs/php_errors.log'); 

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Pour le développement local. À restreindre en production.
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Gérer les requêtes pre-flight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Autoloader simple pour charger les classes de l'application
spl_autoload_register(function ($class) {
    // Transforme le namespace en chemin de fichier
    // App\Services\OpenMeteoClient -> app/Services/OpenMeteoClient.php
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Controllers\ApiController;

// Vérifier que la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => true, 'message' => 'Méthode non autorisée. Seules les requêtes POST sont acceptées.']);
    exit;
}

// Récupérer et décoder le corps de la requête JSON
$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => true, 'message' => 'JSON malformé.']);
    exit;
}

// Valider les données d'entrée
$latitude = $input['latitude'] ?? null;
$longitude = $input['longitude'] ?? null;

if (!is_numeric($latitude) || !is_numeric($longitude)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => true, 'message' => 'Les paramètres latitude et longitude sont requis et doivent être des nombres.']);
    exit;
}

// Instancier le contrôleur et traiter la requête
try {
    $controller = new ApiController();
    $response = $controller->handleAnalysisRequest((float)$latitude, (float)$longitude);
} catch (Exception $e) {
    // error_log("Internal Server Error: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    $response = ['error' => true, 'message' => 'Une erreur interne est survenue.'];
}

// Renvoyer la réponse
echo json_encode($response);

