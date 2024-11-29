<?php

class QueryMaker {
    public static function makeTotalFilesQuery ($idColumnName, $tableName) {
        return "SELECT COUNT($idColumnName) AS TotalFiles
                FROM $tableName;";
    }

    public static function makeSelectBlockOfFilesQuery($idColumnName, $columnToEncryptArray, $tableName) {
        // Creamos un string con todos los nombres de columnas
        $columnsStr = implode(', ', $columnToEncryptArray);
        
        return "SELECT $idColumnName, $columnsStr
                FROM $tableName 
                ORDER BY $idColumnName ASC 
                OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";
    }

    public static function makeUpdateQuery($idColumnName, $columnToEncryptArray, $tableName) {
        // Iteramos el array y creamos un nuevo array con $column = ?
        //$setParts = array_map(fn($column) => "$column = ?", $columnToEncryptArray);  ---> Descomentar en caso de PHP v 7.4 o mayor
        $setParts = array_map(function($column) {
            return "$column = ?";
        }, $columnToEncryptArray);
        // Concatenamos el valor de los arrays con una coma y espacio
        $setColumnsStr = implode(', ', $setParts);

        return "UPDATE $tableName SET $setColumnsStr WHERE $idColumnName = ?;";
    }
}
