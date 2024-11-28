<?php

include_once("Config.php");

class DB {
    
    private $servidor = SERVIDOR;
	private $bd = BD;
	private $usuario = USUARIO;
	private $clave = CLAVE;
	private $connection;

    public function connect() {
        echo "\n"."Iniciando conexión a la Base  de Datos /ᐠ. ｡.ᐟ\ᵐᵉᵒʷˎˊ˗"."\n";
        $bd_info = array("Database"=>$this->bd, "UID"=>$this->usuario, "PWD"=>$this->clave);
        $this->connection = sqlsrv_connect($this->servidor, $bd_info);
        if(!$this->connection) {
            echo "\n"."Problema al conectarse a la Base de Datos (ᗒᗣᗕ)՞ "."\n";
            die(print_r(sqlsrv_errors(), true));
        }
        echo "\n"."Conectado a la Base de Datos con éxito ＼（＾∀＾）人（＾∀＾）ノ"."\n";
        return true;
    }

    public function useConection(&$con)
	{
		$this->connection = $con;
	}

    public function getConection()
	{
		return $this->connection;
	}

    public function disconnect () {
        if(!is_null($this->connection)) {
            sqlsrv_close($this->connection);
            echo "\n" . "Conexión cerrada correctamente /ᐠ_ ꞈ _ᐟ\ɴʏᴀ~" . "\n";
        }
    }

    public function sendQuery($sql, $params = []) {
        if(is_null($this->connection)) {
            echo "No tenemos conexión a la DB para enviar la Query (.づ◡﹏◡)づ."."\n";
            return false;
        }
        // Preparar la consulta
        $statement = sqlsrv_prepare($this->connection, $sql);
        if($statement === false) {
            echo "\n"."Tuvimos un problema preparando la Query (´Д｀。"."\n";
            print_r(sqlsrv_errors());
            return false;
        }
        // Ejecutar la consulta
        $response = sqlsrv_execute($statement);
        if(!$response) {
            echo "\n"."Una consulta ha salido mal ¯\_(ツ)_/¯ "."\n";
            print_r(sqlsrv_errors());
            return false;
        }
        // Recoger los resultados
        $results = [];
        while ($row = sqlsrv_fetch_array($statement, SQLSRV_FETCH_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }
    
}
