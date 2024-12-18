<?php
require_once "conexion.php";
require_once "respuestas.php";

class editoriales extends conexion
{
    private $id_editorial;
    private $nombre = "";
    private $pais = "";
    private $fecha_fundacion = "";
    private $sitio_web = "";
    private $telefono = "";
    private $direccion = "";
    private $token = "";

    public function ListarEditoriales($pagina, $cadena, $token)
    {
        // Instanciamos el objeto de respuesta
        $_respuesta = new respuestas;

        // Verificamos el token
        $arrayToken = parent::buscarToken($token);

        if ($arrayToken) {
            $inicio = 0;
            $cantidad = 10;

            // Calcular el inicio según la página solicitada
            if ($pagina >= 1) {
                $inicio = $cantidad * ($pagina - 1);
            }

            // Consulta para obtener los datos de las editoriales
            $query = "select id_editorial, nombre, pais, fecha_fundacion, sitio_web, telefono from editoriales where nombre like '%$cadena%' or pais like '%$cadena%' limit $inicio, $cantidad";

            $datos = parent::obtenerDatos($query);

            // Consulta para obtener el número de páginas
            $queryNumPage = "select ceil(count(*)/$cantidad) as num_paginas from editoriales where nombre like '%$cadena%' or pais like '%$cadena%'";
            $numero_paginas = parent::obtenerDatos($queryNumPage);

            return [$datos, $numero_paginas];
        } else {
            // Error de token inválido
            return $_respuesta->error_401();
        }
    }
    public function obtenerEditorial($id, $token)
    {
        $_respuesta = new respuestas;

        // Verificamos el token
        $arrayToken = parent::buscarToken($token);

        if ($arrayToken) {
            // Consulta para obtener los datos de las editoriales
            $query = "select * from editoriales where id_editorial = '$id'";
            $datos = parent::obtenerDatos($query);

            return $datos;
        } else {
            // Error de token inválido
            return $_respuesta->error_401();
        }
    }

    public function post($datos_json)
    {
        $_respuesta = new respuestas;
        $datos = json_decode($datos_json, true);
        if (!isset($datos["token"])) {
            return $_respuesta->error_401();
        } else {
            $this->token = $datos["token"];
            $arrayToken = parent::buscarToken($this->token);
            if ($arrayToken) {
                if (!isset($datos["nombre"]) || !isset($datos["pais"]) || !isset($datos["fecha_fundacion"]) || !isset($datos["sitio_web"]) || !isset($datos["telefono"]) || !isset($datos["direccion"])) {
                    return $_respuesta->error_400();
                } else {
                    $this->nombre = $datos["nombre"];
                    $this->pais = $datos["pais"];
                    $this->fecha_fundacion = $datos["fecha_fundacion"];
                    $this->sitio_web = $datos["sitio_web"];
                    $this->telefono = $datos["telefono"];
                    $this->direccion = $datos["direccion"];
                    $query = "insert into editoriales values (null, '" . $this->nombre . "', '" . $this->pais . "', '" . $this->fecha_fundacion . "', '" . $this->sitio_web . "', '" . $this->telefono . "', '" . $this->direccion . "'); ";
                    $resp = parent::nonQueryId($query);
                    if ($resp) {
                        $respuesta = $_respuesta->response;
                        $respuesta["result"] = array(
                            "id_editorial" => $resp
                        );
                        return $respuesta;
                    } else {
                        return $_respuesta->error_500();
                    }
                    return $_respuesta->error_401();
                }
            }
        }
    }
    public function put($datos_json)
    {
        $_respuesta = new respuestas;
        $datos = json_decode($datos_json, true);
        if (!isset($datos["token"])) {
            return $_respuesta->error_401();
        } else {
            $this->token = $datos["token"];
            $arrayToken = parent::buscarToken($this->token);
            if ($arrayToken) {
                if (!isset($datos["id_editorial"]) || !isset($datos["nombre"]) || !isset($datos["pais"]) || !isset($datos["fecha_fundacion"]) || !isset($datos["sitio_web"]) || !isset($datos["telefono"]) || !isset($datos["direccion"])) {
                    return $_respuesta->error_400();
                } else {
                    $this->id_editorial = $datos["id_editorial"];
                    $this->nombre = $datos["nombre"];
                    $this->pais = $datos["pais"];
                    $this->fecha_fundacion = $datos["fecha_fundacion"];
                    $this->sitio_web = $datos["sitio_web"];
                    $this->telefono = $datos["telefono"];
                    $this->direccion = $datos["direccion"];
                    $query = "update editoriales set nombre= '" . $this->nombre . "', pais= '" . $this->pais . "', fecha_fundacion= '" . $this->fecha_fundacion . "', sitio_web= '" . $this->sitio_web . "',telefono='" . $this->telefono . "', direccion='" . $this->direccion . "' WHERE id_editorial='" . $this->id_editorial . "';";
                    $resp = parent::nonQuery($query);
                    if ($resp) {
                        $respuesta = $_respuesta->response;
                        $respuesta["result"] = array(
                            "id_editorial" => $this->id_editorial
                        );
                        return $respuesta;
                    } else {
                        return $_respuesta->error_500();
                    }
                    return $_respuesta->error_401();
                }
            }
        }
    }

    public function delete($id, $token)
    {
        $_respuesta = new respuestas;
        $this->token = $token;  // Asigna el token recibido al atributo de la clase
        $arrayToken = parent::buscarToken($this->token);
        if ($arrayToken) {
            $query = "delete from editoriales where id_editorial = '$id';";
            $resp = parent::nonQuery($query);
            if ($resp) {
                $respuesta = $_respuesta->response;
                $respuesta["result"] = array(
                    "id_editorial" => $id
                );
                return $respuesta;
            } else {
                return $_respuesta->error_500();
            }
        } else {
            return $_respuesta->error_401();
        }
    }
}
