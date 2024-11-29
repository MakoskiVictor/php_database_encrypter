<?php

include_once("db/DB.php");
include_once("services/Encrypter.php");
include_once("services/QueryMaker.php");

class Orchestrator extends DB {

    private $fileToInit = 274; // Fila donde inicia el paginado => Inicia con el 0
    private $filesToRequestPerPage = 10; // Cantidad de filas por página
    private $totalFiles;
    private $encrypter;
    private $tableName = "[dbo].[Postulantes]";
    private $idColumnName = "postulanteid";
    private $columnToEncryptArray = ['nombre', 'email', 'telefono'];

    function __construct() {
        $this->encrypter = new Encrypter();
    }

    // -------------------------- MAIN FUNCTION ----------------------------------------------------
    public function startEncryption() {

        // TODO --------------> AGREGAR QUERY PARA AUMENTAR EL MAXIMO DE LAS FILAS A ENCRIPTAR -> VARCHAR(MAX)

        // Pedimo la cantidad de filas a modificar
        if (!self::requestTotalFiles()) {
            die(print_r("No hay filas que modificar en la tabla" . $this->tableName ."\n". 
            "Por favor, verifique si ingresó los datos correctamente"));
        }
        // Iniciamos el proceso
        while($this->fileToInit <= 276) {  // TODO -----------------------------> RECORDAR PONER ACÁ EL $totalFiles
            // Pedimos los datos a encriptar
            $filesToEncrypt = self::callRows();
            if (!$filesToEncrypt) {
                echo ("\n"."¡100% de los datos encriptados! —ฅ/ᐠ. ̫ .ᐟ\ฅ —"."\n");
                break;
            }
            // Encriptamos los datos
            $encryptedFiles = self::toEncrypt($filesToEncrypt);
            // Reemplazamos los datos con los nuevos encriptados
            try {
                parent::beginTransaction();
                foreach ($encryptedFiles as $row) {
                    self::sendUpdate($row);
                    echo "\n"."ID: " . $row['postulanteid'] . " Encriptado con éxito!"."\n";
                }
                parent::endTransaction();
                // Actualizamos el index
                $this->fileToInit += $this->filesToRequestPerPage;
            } catch (Exception $e) {
                parent::restoreData();
                die(print_r("Error: " . $e->getMessage()));
            }
        }
    }

    // -------------------------- SECUNDARIES FUNCTIONS ----------------------------------------------------
    public function requestTotalFiles() {
        // traemos el total de filas a encriptar
        $sql = QueryMaker::makeTotalFilesQuery ($this->idColumnName, $this->tableName);
        $response = parent::sendQuery($sql);
        if(!$response) {
            echo "\n"."No hay respuesta de la DB (ಠ╭╮ಠ)";
            return false;
        }
        echo "\n"."Cantidad de filas en la tabla: " . $response[0]['TotalFiles'] . " ⊂(・﹏・⊂)"."\n";
        $this->totalFiles = $response[0]['TotalFiles'];
        return true;
    }

    public function callRows () {
        // Traemos los datos a encriptar
        $sql = QueryMaker::makeSelectBlockOfFilesQuery($this->idColumnName, $this->columnToEncryptArray, $this->tableName);
        $params = [$this->fileToInit, $this->filesToRequestPerPage];
        $response = parent::sendQuery($sql, $params);
        if(!$response) {
            return false;
        }
        return $response;
    }

    public function toEncrypt($files) {
        // Encriptar resultados
        return $this->encrypter->EncriptarArrayDeArray($files, ['nombre', 'email', 'telefono']);
    }
    public function sendUpdate($files) {
        $sql = QueryMaker::makeUpdateQuery($this->idColumnName, $this->columnToEncryptArray, $this->tableName);
        $params = [$files['nombre'],$files['email'],$files['telefono'],$files['postulanteid']];

            $response = parent::updateQuery($sql, $params);
            if(!$response) {
                echo "\n"."No hay respuesta de la DB Postulantes (ಠ╭╮ಠ)";
                return false;
            }
            return true;
    }
}
