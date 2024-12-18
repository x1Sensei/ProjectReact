<?php
require_once "conexion.php";
require_once "respuestas.php";

class auth extends conexion
{
    //Clase para realizar el logueo/autenticación
    public function login($json)
{
    $_respuestas = new respuestas; // Instanciamos la clase respuestas
    $datos = json_decode($json, true);

    if (!isset($datos['usuario']) || !isset($datos['contrasena'])) {
        return $_respuestas->error_400();
    } else {
        $usuario = $datos['usuario'];
        $contrasena = $datos['contrasena'];
        $query = "SELECT * FROM autores WHERE usuario = '$usuario' AND contrasena = sha1('$contrasena')";
        $data = parent::obtenerDatos($query);

        // Verificamos si se encontró el usuario
        if (isset($data[0]['id_autor'])) {
            // Si el usuario existe, generamos un token
            $verificar = $this->insertar_token($data[0]['id_autor']);
            if ($verificar) {
                $result = $_respuestas->response;
                $result['result'] = array(
                    "token" => $verificar
                );
                return $result;
            } else {
                return $_respuestas->error_200("Error al generar el token");
            }
        } else {
            // Si no se encontró el usuario, verificamos si la contraseña es correcta
            $queryContraseña = "SELECT * FROM autores WHERE usuario = '$usuario'";
            $usuarioExistente = parent::obtenerDatos($queryContraseña);

            if (isset($usuarioExistente[0]['id_autor'])) {
                // Usuario existe pero la contraseña es incorrecta
                return $_respuestas->error_200("Contraseña incorrecta");
            } else {
                // Usuario no existe
                return $_respuestas->error_200("Usuario y contraseña incorrectos");
            }
        }
    }
}


    private function insertar_token($id)
    {
        $valor = true;
        $tokeng = bin2hex(openssl_random_pseudo_bytes(16, $valor));
        date_default_timezone_set('America/Guayaquil');
        $fecha = date("Y-m-d H-i-s"); //2024-09-13
        $query = "INSERT INTO token (`id_token`, `token_g`, `fecha`, `activo`, `autor_id`) VALUES (NULL, '$tokeng', '$fecha', '$id', '1')";
        $verifica  = parent::nonQueryId($query);
        if ($verifica) {
            return $tokeng;
        } else {
            return 0;
        }
    }
}
