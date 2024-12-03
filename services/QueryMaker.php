<?php

class QueryMaker {
    public static function makeTotalFilesQuery ($idColumnName, $tableName) {
        return "SELECT COUNT($idColumnName) AS TotalFiles FROM [dbo].[$tableName];";
    }

    public static function makeSelectBlockOfFilesQuery($idColumnName, $columnToEncryptArray, $tableName) {
        // Creamos un string con todos los nombres de columnas
        $columnsStr = implode(', ', $columnToEncryptArray);
        
        return "SELECT $idColumnName, $columnsStr FROM [dbo].[$tableName] ORDER BY $idColumnName ASC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY;";
    }

    public static function makeUpdateQuery($idColumnName, $columnToEncryptArray, $tableName) {
        // Iteramos el array y creamos un nuevo array con $column = ?
        //$setParts = array_map(fn($column) => "$column = ?", $columnToEncryptArray);  ---> Descomentar en caso de PHP v 7.4 o mayor
        $setParts = array_map(function($column) {
            return "$column = ?";
        }, $columnToEncryptArray);
        // Concatenamos el valor de los arrays con una coma y espacio
        $setColumnsStr = implode(', ', $setParts);

        return "UPDATE [dbo].[$tableName] SET $setColumnsStr WHERE $idColumnName = ?;";
    }

    public static function makeObtainColumnTypeAndLengthQuery($columnToEncryptArray, $tableName) {
        $columnArr = array_map(function($column) { return "'".$column."'"; }, $columnToEncryptArray);
        $columnsStr = implode(', ', $columnArr);

        return "SELECT 
                    COLUMN_NAME AS ColumnName,
                    DATA_TYPE AS DataType,
                    CHARACTER_MAXIMUM_LENGTH AS MaxLength
                FROM 
                    INFORMATION_SCHEMA.COLUMNS
                WHERE 
                    TABLE_NAME = '$tableName'
                    AND TABLE_SCHEMA = 'dbo'
                    AND COLUMN_NAME IN ($columnsStr);";
    }

    public static function makeEditColumnQuery ($columnName, $tableName) {
        return "ALTER TABLE [dbo].[$tableName] ALTER COLUMN $columnName VARCHAR(MAX); "."\n";
    }
}
