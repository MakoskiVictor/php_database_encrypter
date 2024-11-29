<?php

include_once("Config.php");
include_once("logs/Messages.php");

class DB {
    
    private $servidor = SERVIDOR;
	private $bd = BD;
	private $usuario = USUARIO;
	private $clave = CLAVE;
	private $connection;

    public function connect() {
        echo "\n"."Iniciando conexión a la Base  de Datos"."\n";
        Messages::curiousCat();

        $bd_info = array("Database"=>$this->bd, "UID"=>$this->usuario, "PWD"=>$this->clave);
        $this->connection = sqlsrv_connect($this->servidor, $bd_info);
        if(!$this->connection) {
            echo "\n"."Problema al conectarse a la Base de Datos (ᗒᗣᗕ)՞ "."\n";
            die(print_r(sqlsrv_errors(), true));
        }
        echo "\n"."Conectado a la Base de Datos con éxito ≽^•⩊•^≼"."\n";
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
            echo "\n" . "Conexión cerrada correctamente" . "\n";
            Messages::sleepyCat();
        }
    }

    public function prepareQuery ($sql, $params) {
        if(is_null($this->connection)) {
            echo "No tenemos conexión a la DB para enviar la Query (.づ◡﹏◡)づ."."\n";
            print_r(sqlsrv_errors());
            return false;
        }
        // Preparar la consulta
        $statement = sqlsrv_prepare($this->connection, $sql, $params);
        if($statement === false) {
            echo "\n"."Tuvimos un problema preparando la Query (´Д｀。"."\n";
            print_r(sqlsrv_errors());
            return false;
        }
        return $statement;
    }

    public function sendQuery($sql, $params = []) {
        // Preparar la Query
        $statement = self::prepareQuery($sql, $params);
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

    public function updateQuery($sql, $params = []) {
        // Preparar la Query
        $statement = self::prepareQuery($sql, $params);
        // Ejecutar la consulta
        $response = sqlsrv_execute($statement);
        if(!$response) {
            echo "\n"."Una consulta ha salido mal ¯\_(ツ)_/¯ "."\n";
            print_r(sqlsrv_errors());
            return false;
        }
        return $response;
    }

    public function beginTransaction() {
        sqlsrv_begin_transaction($this->connection);
    }

    public function endTransaction() {
        sqlsrv_commit($this->connection);
    }

    public function restoreData() {
        sqlsrv_rollback($this->connection);
    }
    
}
