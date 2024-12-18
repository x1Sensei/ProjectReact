<?php
header('Access-Control-Allow-Origin: *'); // Cambiado
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
require_once './backend/auth.class.php';
require_once './backend/respuestas.php';
$_auth = new auth;
$_respuestas = new respuestas;
if ($_SERVER['REQUEST_METHOD'] == "POST") { //Para autenticación usamos el método POST
    //recibir datos
    $postBody = file_get_contents("php://input");
    //Enviamos datos al manejador
    $datosArray = $_auth->login($postBody);
    //devolvemos una respuesta
    header('Content-Type: application/json');
    if (isset($datosArray["result"]["error_id"])) {
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    } else {
        http_response_code(200);
    }
    echo json_encode($datosArray);
} else {
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
