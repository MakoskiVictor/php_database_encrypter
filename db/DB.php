<?php

include_once("Config.php");

class DB {
    
    private $servidor = SERVIDOR;
	private $bd = BD;
	private $usuario = USUARIO;
	private $clave = CLAVE;
	private $connection;

    public function connect() {
        $bd_info = array("Database"=>$this->bd, "UID"=>$this->usuario, "PWD"=>$this->clave);
        $this->connection = sqlsrv_connect($this->servidor, $bd_info);
        if(!$this->connection) {
            echo "Problema al conectarse a la Base de Datos (ᗒᗣᗕ)՞ <br>"."\n";
            return false;
        }
        echo "Conectado a la Base de Datos con éxito ＼（＾∀＾）人（＾∀＾）ノ <br>"."\n";
        return true;
    }

    public function disconnect () {
        if(!is_null($this->connection)) {
            sqlsrv_close($this->connection);
        }
    }

    public function sendQuery($sql, $params = []) {
        if(is_null($this->connection)) {
            echo "No tenemos conexión a la DB para enviar la Query (.づ◡﹏◡)づ. <br>"."\n";
            return false;
        }
        // Preparar la consulta
        $statement = sqlsrv_prepare($this->connection, utf8_decode($sql), $params);
        echo $statement . "\n";
        if($statement === false) {
            echo "Tuvimos un problema preparando la Query (´Д｀。 <br>"."\n";
            return false;
        }
        // Ejecutar la consulta
        if(!sqlsrv_execute($statement)) {
            echo "Una consulta ha salido mal ¯\_(ツ)_/¯  <br>"."\n";
            echo $statement;
            return false;
        }
        return true;
    }
}
