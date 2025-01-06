<?php

include_once("services/TableProcessor.php");

class Orchestrator {

    private $tableDefinitionPostulantes;
    private $conn;

    function __construct($conn) {
        $this->conn = $conn;

        $this->tableDefinitionPostulantes = [
            'Postulantes' => [
                'fileToInit' => 0, 
                'filesToRequestPerPage' => 10, 
                'tableName' => 'Postulantes',
                'idColumnName' => 'postulanteid',
                'columnToEncryptArray' => ['nombre', 'email', 'telefono']
            ],
            /* 'Personas' => [
                'fileToInit' => 0, 
                'filesToRequestPerPage' => 10, 
                'tableName' => 'personas',
                'idColumnName' => 'personaid',
                'columnToEncryptArray' => ['nombre', 'appaterno', 'apmaterno', 'correo', 'fono', 'telefono']
            ],
            'CargaMovimientos_Pareo' => [
                'fileToInit' => 0, 
                'filesToRequestPerPage' => 10, 
                'tableName' => 'CargaMovimientos_Pareo',
                'idColumnName' => 'registroid',
                'columnToEncryptArray' => ['NombreTrabajador']
            ],
            'Solicitud' => [
                'fileToInit' => 0, 
                'filesToRequestPerPage' => 10, 
                'tableName' => 'solicitud',
                'idColumnName' => 'id',
                'columnToEncryptArray' => ['correo', 'telefono']
            ] */
        ];
    }

    public function manageTables() {
        foreach($this->tableDefinitionPostulantes as $tableName => $tableDefinition) {
            $tableProcessor = new TableProcessor($tableDefinition);
            $tableProcessor->useConection($this->conn);
            $tableProcessor->startEncryption();
        }
    }
}
