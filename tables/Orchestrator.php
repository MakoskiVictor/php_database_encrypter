<?php

include_once("db/DB.php");
include_once("services/Encrypter.php");
include_once("services/QueryMaker.php");

class Orchestrator extends DB {

    private $fileToInit = 289; // Fila donde inicia el paginado => Inicia con el 0
    private $filesToRequestPerPage = 10; // Cantidad de filas por página
    private $totalFiles;
    private $encrypter;
    private $tableName = "Postulantes";
    private $idColumnName = "postulanteid";
    private $columnToEncryptArray = ['nombre', 'email', 'telefono'];

    function __construct() {
        $this->encrypter = new Encrypter();
    }

    // -------------------------- MAIN FUNCTION ----------------------------------------------------
    public function startEncryption() {

        // Verificamos que las columnas cumplan los requisitos y modificamos el length de ser necesario
        $typeOfColumns = self::requestTypesAndLengthOfColumns();
        self::manageColumns($typeOfColumns);
        
        // Pedimo la cantidad de filas a modificar
        if (!self::requestTotalFiles()) {
            die(print_r("No hay filas que modificar en la tabla" . $this->tableName ."\n". 
            "Por favor, verifique si ingresó los datos correctamente"));
        }
        // Iniciamos el proceso
        while($this->fileToInit <= $this->fileToInit + 10) {  // TODO -----------------------------> RECORDAR PONER ACÁ EL $totalFiles
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
    // TYPE AND LENGTH OF COLUMNS
    public function requestTypesAndLengthOfColumns() {
        echo "\n"."Iniciando petición de columnas a modificar"."\n"; 
        $sql = QueryMaker::makeObtainColumnTypeAndLengthQuery($this->columnToEncryptArray, $this->tableName);
        $response = parent::sendQuery($sql);
        if(!$response) {
            return false;
        }
        return $response;
    }

    public function manageColumns($arrayOfColumns) {
        echo "\n"."Iniciando revisión de TYPE y LENGTH de columnas a encriptar"."\n"; 
        $columnsToChange = '';
        foreach($arrayOfColumns as $column) {
            if($column['DataType'] != 'varchar') {
                die(print_r("\n"."Una columna es distinta a VARCHAR. El script se detendrá por no tener un handler para este caso"."\n"));
            } else if($column['DataType'] == 'varchar' && (int)$column['MaxLength'] !== -1) {
                $columnsToChange.= QueryMaker::makeEditColumnQuery ($column['ColumnName'], $this->tableName);
            }
        }

        if($columnsToChange != '') {
            try {
                parent::beginTransaction();
                $response = parent::updateQuery($columnsToChange);
                if(!$response) {
                    die(print_r("\n"."Problema al modificar las columnas de la tabla ".$this->tableName."\n"."El Script se detendrá para su análisis "."\n"));
                }
                parent::endTransaction();
                echo "\n"."Columnas modificadas correctamente";
                return true;
            } catch (Exception $e) {
                parent::restoreData();
                print_r("Error: " . $e->getMessage());
                die(print_r("\n"."Problema al modificar las columnas de la tabla ".$this->tableName."\n"."El Script se detendrá para su análisis "."\n"));
            }
        }
        echo "\n"."No se han detectado la necesidad de modificar columnas"."\n";
        return true;
    }

    // ENCRYPT
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
