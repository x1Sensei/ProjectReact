<?php
// Configuración de CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Incluir las clases necesarias
require_once './backend/comics.class.php';
require_once './backend/respuestas.php';

$_comics = new comics();
$_respuestas = new respuestas();

// Manejo de solicitudes preflight para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Manejo de la solicitud según el método HTTP
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Autenticación y consulta de cómics
    if (isset($_GET["pagina"], $_GET["cadena"], $_GET["token"])) {
        $pagina = $_GET["pagina"];
        $cadena = $_GET["cadena"];
        $token = $_GET["token"];
        
        $listacomics = $_comics->listarComics($pagina, $cadena, $token);
        http_response_code(200); // Código de éxito
        header('Content-Type: application/json');
        echo json_encode($listacomics);
        
    } elseif (isset($_GET["id_comic"], $_GET["token"])) {
        $id_comic = $_GET["id_comic"];
        $token = $_GET["token"];
        $comic = $_comics->obtenerComic($id_comic, $token);
        http_response_code(200); // Código de éxito
        header('Content-Type: application/json');
        echo json_encode($comic);
        
    } else {
        // Error si faltan parámetros
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode($_respuestas->error_400());
    }

} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Procesar solicitud POST para crear un nuevo cómic
    $postBody = file_get_contents("php://input");
    $datosArray = $_comics->post($postBody);
    header('Content-Type: application/json');

    // Manejo de errores
    if (isset($datosArray["result"]["error_id_comic"])) {
        http_response_code($datosArray["result"]["error_id_comic"]);
    } else {
        http_response_code(201); // Código 201 Created para la creación exitosa
    }
    echo json_encode($datosArray);

} elseif ($_SERVER['REQUEST_METHOD'] == "PUT") {
    $putBody = file_get_contents("php://input");
    error_log("Datos recibidos en PUT: " . $putBody); // Log para verificar el JSON recibido

    $datosArray = $_comics->put($putBody);
    header('Content-Type: application/json');

    if (isset($datosArray["result"]["error_id_comic"])) {
        error_log("Error en PUT: " . json_encode($datosArray)); // Log para revisar el error
        http_response_code($datosArray["result"]["error_id_comic"]);
    } else {
        http_response_code(200); // Código 200 OK para actualización exitosa
    }
    echo json_encode($datosArray);

} elseif ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    // Mostrar todos los parámetros de la URL
    var_dump($_GET);  // Esto imprimirá todos los parámetros que se reciben a través de la URL
    
    if (isset($_GET["id_comic"], $_GET["token"])) {
        $id_comic = $_GET["id_comic"];
        $token = $_GET["token"];

        $datosArray = $_comics->delete($id_comic, $token);
        header('Content-Type: application/json');

        if (isset($datosArray["result"]["error_id_comic"])) {
            http_response_code($datosArray["result"]["error_id_comic"]);
        } else {
            http_response_code(200); // Código 200 OK para eliminación exitosa
        }
        echo json_encode($datosArray);
    } else {
        // Error si faltan parámetros en solicitud DELET
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode($_respuestas->error_400());
    }

} else {
    // Manejo de métodos HTTP no permitid_comicos
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode($_respuestas->error_405());
}
