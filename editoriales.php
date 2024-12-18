<?php
header('Access-Control-Allow-Origin: *'); // Cambiado
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
require_once './backend/editoriales.class.php';
require_once './backend/respuestas.php';
$_editoriales = new editoriales;
$_respuestas = new respuestas;



if ($_SERVER['REQUEST_METHOD'] == "GET") {

    if (isset($_GET["pagina"]) && isset($_GET["cadena"]) && isset($_GET["token"])) {
        $pagina = $_GET["pagina"];
        $cadena = $_GET["cadena"];
        $token = $_GET["token"];
        $listaEditoriales = $_editoriales->ListarEditoriales($pagina, $cadena, $token);

        header('Content-Type: application/json');
        echo json_encode($listaEditoriales);
        http_response_code(200);
    } else if (isset($_GET["id"]) && isset($_GET["token"])) {
        $id = $_GET["id"];
        $token = $_GET["token"];
        $Editorial = $_editoriales->ObtenerEditorial($id, $token);

        header('Content-Type: application/json');
        echo json_encode($Editorial);
        http_response_code(200);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
    $postBody = file_get_contents("php://input");
    $datosArray = $_editoriales->post($postBody);
    header('Content-Type: application/json');
    if (isset($datosArray["result"]["error_id"])) {
        $responseCode = $datosArray["result"]["error_id"];
    } else {
        http_response_code(200);
    }
    echo json_encode($datosArray);
} elseif ($_SERVER['REQUEST_METHOD'] == "PUT") {
    $putBody = file_get_contents("php://input");
    $datosArray = $_editoriales->put($putBody);
    header('Content-Type: application/json');
    if (isset($datosArray["result"]["error_id"])) {
        $responseCode = $datosArray["result"]["error_id"];
    } else {
        http_response_code(200);
    }
    echo json_encode($datosArray);
} elseif ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    if (isset($_REQUEST["id"]) && isset($_REQUEST["token"])) {
        $id = $_REQUEST["id"];
        $token = $_REQUEST["token"];
        $datosArray = $_editoriales->delete($id, $token);
        header('Content-Type: application/json');
        if (isset($datosArray["result"]["error_id"])) {
            $responseCode = $datosArray["result"]["error_id"];
        } else {
            http_response_code(200);
        }
        echo json_encode($datosArray);
    } else {
        $_respuestas->error_400();
    }
}
