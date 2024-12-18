<?php
require_once "conexion.php";
require_once "respuestas.php";

class comics extends conexion
{
    private $id_comic = "";
    private $titulo = "";
    private $fecha_publicacion = "";
    private $numero_paginas = "";
    private $sinopsis = "";
    private $isbn = "";
    private $precio = "";
    private $editorial_id = "";
    private $genero_id = "";
    private $token = "";

    public function ListarComics($pagina, $cadena, $token)
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

            // Consulta para obtener los datos de los cómics
            $query = "select id_comic,titulo, fecha_publicacion, numero_paginas, sinopsis, isbn, precio from comics where titulo like '%$cadena%' limit $inicio, $cantidad";

            $datos = parent::obtenerDatos($query);

            // Consulta para obtener el número de páginas
            $queryNumPage = "select ceil(count(*)/$cantidad) as num_paginas from comics where titulo like '%$cadena%'";
            $resultado = parent::obtenerDatos($queryNumPage);
            $numero_paginas = $resultado ? $resultado[0]["num_paginas"] : 0;

            return [$datos, $numero_paginas];
        } else {
            // Error de token inválido
            return $_respuesta->error_401();
        }
    }

    public function obtenerComic($id_comic, $token)
    {
        $_respuesta = new respuestas;

        // Verificamos el token
        $arrayToken = parent::buscarToken($token);

        if ($arrayToken) {
            // Consulta para obtener los datos del cómic
            $query = "select * from comics where id_comic = '$id_comic'";
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
                if (!isset($datos["id_comic"]) || !isset($datos["titulo"]) || !isset($datos["fecha_publicacion"]) || !isset($datos["numero_paginas"]) || !isset($datos["sinopsis"]) || !isset($datos["isbn"]) || !isset($datos["precio"]) || !isset($datos["editorial_id"]) || !isset($datos["genero_id"])) {
                    return $_respuesta->error_400();
                } else {
                    // Validar si las claves foráneas existen
                    $isbnExistente = $this->validarExistenciaIsbn($datos["isbn"]);
                    $editorialExistente = $this->validarExistenciaEditorial($datos["editorial_id"]);
                    $generoExistente = $this->validarExistenciaGenero($datos["genero_id"]);

                    if (!$isbnExistente || !$editorialExistente || !$generoExistente) {
                        return $_respuesta->error_400("Una o más claves foráneas no existen.");
                    }

                    $this->id_comic = $datos["id_comic"];
                    $this->titulo = $datos["titulo"];
                    $this->fecha_publicacion = $datos["fecha_publicacion"];
                    $this->numero_paginas = $datos["numero_paginas"];
                    $this->sinopsis = $datos["sinopsis"];
                    $this->isbn = $datos["isbn"];
                    $this->precio = $datos["precio"];
                    $this->editorial_id = $datos["editorial_id"];
                    $this->genero_id = $datos["genero_id"];

                    // Insertar el cómic en la base de datos
                    $query = "INSERT INTO comics (titulo, fecha_publicacion, numero_paginas, sinopsis, isbn, precio, editorial_id, genero_id) VALUES ('" . $this->titulo . "', '" . $this->fecha_publicacion . "', '" . $this->numero_paginas . "', '" . $this->sinopsis . "', '" . $this->isbn . "', '" . $this->precio . "', '" . $this->editorial_id . "', '" . $this->genero_id . "');";
                    $resp = parent::nonQueryId($query);

                    if ($resp) {
                        $respuesta = $_respuesta->response;
                        $respuesta["result"] = array(
                            "id_comic" => $resp
                        );
                        return $respuesta;
                    } else {
                        return $_respuesta->error_500();
                    }
                }
            } else {
                return $_respuesta->error_401();
            }
        }
    }

    // Funciones para validar las claves foráneas
    private function validarExistenciaIsbn($isbn)
    {
        $query = "SELECT id_comic FROM comics WHERE isbn = '$isbn'";
        $resultado = parent::obtenerDatos($query);
        return !empty($resultado);
    }

    private function validarExistenciaEditorial($editorial_id)
    {
        $query = "SELECT id_editorial FROM editoriales WHERE id_editorial = '$editorial_id'";
        $resultado = parent::obtenerDatos($query);
        return !empty($resultado);
    }

    private function validarExistenciaGenero($genero_id)
    {
        $query = "SELECT id_genero FROM generos WHERE id_genero = '$genero_id'";
        $resultado = parent::obtenerDatos($query);
        return !empty($resultado);
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
            // Validar las claves foráneas
            $isbnExistente = $this->validarExistenciaIsbn($datos["isbn"]);
            $editorialExistente = $this->validarExistenciaEditorial($datos["editorial_id"]);
            $generoExistente = $this->validarExistenciaGenero($datos["genero_id"]);

            if (!$isbnExistente || !$editorialExistente || !$generoExistente) {
                return $_respuesta->error_400("Una o más claves foráneas no existen.");
            }

            $this->id_comic = $datos["id_comic"];
            $this->titulo = $datos["titulo"];
            $this->fecha_publicacion = $datos["fecha_publicacion"];
            $this->numero_paginas = $datos["numero_paginas"];
            $this->sinopsis = $datos["sinopsis"];
            $this->isbn = $datos["isbn"];
            $this->precio = $datos["precio"];
            $this->editorial_id = $datos["editorial_id"];
            $this->genero_id = $datos["genero_id"];
            
            $query = "UPDATE comics SET titulo= '" . $this->titulo . "', fecha_publicacion= '" . $this->fecha_publicacion . "', numero_paginas= '" . $this->numero_paginas . "', sinopsis= '" . $this->sinopsis . "', isbn= '" . $this->isbn . "', precio= '" . $this->precio . "', editorial_id= '" . $this->editorial_id . "', genero_id= '" . $this->genero_id . "' WHERE id_comic='" . $this->id_comic . "';";
            $resp = parent::nonQuery($query);
            
            if ($resp) {
                $respuesta = $_respuesta->response;
                $respuesta["result"] = array(
                    "id_comic" => $this->id_comic
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


    public function delete($id_comic, $token)
    {
        $_respuesta = new respuestas;
        $this->token = $token;
        echo $this->id_comic;
        // Agregar depuración
        error_log("Token recibido en delete: " . $this->token);
        error_log("ID recibido en delete: " . $id_comic);

        $arrayToken = parent::buscarToken($this->token);
        if ($arrayToken) {
            $query = "delete from comics where id_comic = '$id_comic';";
            $resp = parent::nonQuery($query);
            if ($resp) {
                $respuesta = $_respuesta->response;
                $respuesta["result"] = array(
                    "id_comic" => $id_comic
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
