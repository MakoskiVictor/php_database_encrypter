<?php

include_once("db/DB.php");


class Postulantes extends DB {

    /* function __construct() {
        $this->bd = new BD();
        if($this)
    } */

    public function prueba() {
        $sql = 'USE Qa_SMU_RBK;
                SELECT postulanteid, nombre, email, telefono FROM [dbo].[Postulantes]
                ORDER BY postulanteid ASC
                OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY;';
        $response = parent::sendQuery($sql);
        if(!$response) {
            echo "No hay respuesta de la DB (ಠ╭╮ಠ)";
            return false;
        }
        echo $response;
    }
}