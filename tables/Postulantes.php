<?php

include_once("db/DB.php");


class Postulantes extends DB {

    /* function __construct() {
    } */

    public function prueba() {
        $sql = "SELECT postulanteid, nombre, email, telefono 
        FROM [dbo].[Postulantes] 
        ORDER BY postulanteid ASC 
        OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY;";
        $response = parent::sendQuery($sql);
        if(!$response) {
            echo "\n"."No hay respuesta de la DB (ಠ╭╮ಠ)";
            return false;
        }
        // Procesar los resultados
        foreach ($response as $row) {
            echo "\n"."ID: " . $row['postulanteid'] . " | Nombre: " . $row['nombre'] . " | Email: " . $row['email'] . " | Teléfono: " . $row['telefono'];
        }
    }
}
