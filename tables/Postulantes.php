<?php

include_once("db/DB.php");
include_once("services/Encrypter.php");

class Postulantes extends DB {

    private $fileToInit = 219; // Fila donde inicia el paginado => Inicia con el 0
    private $filesToRequestPerPage = 10; // Cantidad de filas por página
    private $totalFiles;

    function __construct() {
        $this->encrypter = new Encrypter();
    }

    // MAIN FUNCTION
    public function encriptarPostulantes() {

        // TODO --------------> AGREGAR QUERY PARA AUMENTAR EL MAXIMO DE LAS FILAS A ENCRIPTAR -> VARCHAR(MAX)

        // Pedimo la cantidad de filas a modificar
        self::requestTotalFiles();
        // Iniciamos el proceso
        while($this->fileToInit <= 223) {  // RECORDAR PONER ACÁ EL $totalFiles
            // Pedimos los datos a encriptar
            $filesToEncrypt = self::callRows();
            // Encriptamos los datos
            $encryptedFiles = self::toEncrypt($filesToEncrypt);
            // Reemplazamos los datos con los nuevos encriptados
            parent::beginTransaction();
            foreach ($encryptedFiles as $row) {
                self::sendUpdate($row);
                echo "\n"."ID: " . $row['postulanteid'] . " Encriptado con éxito!"."\n";
            }
            parent::endTransaction();
            // Actualizamos el index
            $this->fileToInit += $this->filesToRequestPerPage;
        }
    }

    public function requestTotalFiles() {
        // traemos el total de filas a encriptar
        $sql = "SELECT COUNT(postulanteid) AS TotalFiles
                FROM [dbo].[Postulantes];";
        $response = parent::sendQuery($sql);
        if(!$response) {
            echo "\n"."No hay respuesta de la DB (ಠ╭╮ಠ)";
            return false;
        }
        echo "\n"."Cantidad de filas a procesar: " . $response[0]['TotalFiles'] . " ⊂(・﹏・⊂)"."\n";
        $this->totalFiles = $response[0]['TotalFilas'];
        return true;
    }

    public function callRows () {
        // Traemos los datos a encriptar
        $sql = "SELECT postulanteid, nombre, email, telefono 
                FROM [dbo].[Postulantes] 
                ORDER BY postulanteid ASC 
                OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";
        $params = [$this->fileToInit, $this->filesToRequestPerPage];
        $response = parent::sendQuery($sql, $params);
        if(!$response) {
            echo "\n"."No hay respuesta de la DB (ಠ╭╮ಠ)";
            return false;
        }
        return $response;
    }

    public function toEncrypt($files) {
        // Encriptar resultados
        return $this->encrypter->EncriptarArrayDeArray($files, ['nombre', 'email', 'telefono']);
    }
    public function sendUpdate($files) {
        $sql = "UPDATE [dbo].[Postulantes]
                SET nombre = ?,
                email = ?,
                telefono = ?
                WHERE postulanteid = ?
                SELECT CASE 
                    WHEN @@ROWCOUNT > 0 THEN CAST(1 AS BIT)
                    ELSE CAST(0 AS BIT)
                END AS Actualizado;";

        $params = [$files['nombre'],$files['email'],$files['telefono'],$files['postulanteid']];

        try {
            $response = parent::updateQuery($sql, $params);
            if(!$response) {
                echo "\n"."No hay respuesta de la DB Postulantes (ಠ╭╮ಠ)";
                return false;
            }
            return true;
        } catch (Exception $e) {
            parent::restoreData();
            echo "Error: " . $e->getMessage();
        }
    }
}
