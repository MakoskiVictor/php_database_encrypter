<?php

require_once("../Config.php");

class DB {
    
    private $servidor = SERVIDOR;
	private $bd = BD;
	private $usuario = USUARIO;
	private $clave = CLAVE;
	private $connection;

    private function connect() {
        $bd_info = array("Database"=>$this->bd, "UID"=>$this->usuario, "PWD"=>$this->clave);
        $this->connection = sqlsrv_connect($this->servidor, $bd_info);
        if(!$this->connection) {
            echo "Problema al conectarse a la Base de Datos (ᗒᗣᗕ)՞"."\n";
            return false;
        }
        echo "Conectado a la Base de Datos con éxito ＼（＾∀＾）人（＾∀＾）ノ"."\n";
        return true;
    }

    private function disconnect () {
        if(!is_null($this->connection)) {
            sqlsrv_close($this->connection);
        }
    }

    private function sendQuery($sql, $params = []) {
        if(is_null($this->connection)) {
            echo "No tenemos conexión a la DB para enviar la Query (.づ◡﹏◡)づ."."\n";
            return false;
        }
        // Preparar la consulta
        $statement = sqlsrv_prepare($this->connection, utf8_decode($sql), $params);
        echo $statement . "\n";
        if($statement === false) {
            echo "Tuvimos un problema preparando la Query (´Д｀。"."\n";
            return false;
        }
        // Ejecutar la consulta
        if(!sqlsrv_execute($statement)) {
            echo "Una consulta ha salido mal ¯\_(ツ)_/¯ "."\n";
            echo $statement;
            return false;
        }
        return true;
    }
}
