<?php

class conexion
{
    // public => se utiliza cuando nosotros vayamos a utilizar otras clases 
    private $servidor = "localhost";
    private $usuario = "root";
    private $contrasena = "";
    private $basededatos = "comicsjsmc";
    private $puerto = "3306";
    private $connection;

    function __construct()
    {
        $this->connection = new mysqli($this->servidor, $this->usuario, $this->contrasena, $this->basededatos, $this->puerto);
        if ($this->connection->connect_errno) {

            echo "Conexion no establecida";
            die();
        }
    }

    private function convertirUTF8($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'UTF-8', true)) {
                $encoding = mb_detect_encoding($item, 'ISO-8859-1, Windows-1252, ASCII', true);
                $item = mb_convert_encoding($item, 'UTF-8', $encoding);
            }
        });
        return $array;
    }

    public function obtenerDatos($cadenasql)
    {
        $results = $this->connection->query($cadenasql);
        $resultArray = array();
        foreach ($results as $key) {
            $resultArray[] = $key;
        }
        return $this->convertirUTF8($resultArray);
    }

    public function nonQuery($cadenasql)
    { //Ejecutar sentencia sql para editar
        $results = $this->connection->query($cadenasql);
        return $this->connection->affected_rows;
    }

    public function nonQueryId($cadenasql)
    { //Ejecutar sentencia sql para insertar 
        $results = $this->connection->query($cadenasql);
        $filas = $this->connection->affected_rows;

        if ($filas >= 1) {

            return $this->connection->insert_id;
        } else {
            return 0;
        }
    }
    public function buscarToken($token){
        $query = "select id_token from token where token_g = '$token' and activo = '1';";
        $resp = $this->obtenerDatos($query);
        if ($resp) {
            return true;
        } else {
            return 0;
        }
        
    }
}
