<?php

class TemplaterSQL 
{
    

    /**
     * Summary of SELECT_ALL
     * @param string $tableName
     * @param string[]|null $colNames
     * @return string
     */
    public static function SELECT_FROM(string $tableName, ?array $colNames): string 
    {
        if (is_null($colNames)) {
            return "SELECT * FROM $tableName";
        }
        $colList = implode(', ', $colNames);
        return "SELECT $colList FROM $tableName";
    }

    public static function SELECT_ALL(string $tableName): string
    {
        return "SELECT * FROM $tableName";
    }

    public static function UPDATE(string $tableName, array $colNames): string
    {
        $setList = implode(', ', array_map(function($name) {
            return "$name = :$name";
        }, $colNames));


        return "UPDATE $tableName SET $setList ";
    }

    public static function DELETE_FROM(string $tableName): string
    {
        return "DELETE FROM $tableName";
    }

    public static function WHERE_EQUALS(string $colName, string $valueName): string
    {
        return "WHERE $colName = :$valueName";
    }

    /**
     * Summary of WHERE_IN
     * @param string $colName
     * @param string[] $possibleValues
     * @return string
     */
    public static function WHERE_IN(string $colName, array $possibleValues): string
    {
        $placeholders = implode(', ', array_map(function($value) {
            return ":$value";
        }, $possibleValues));
        return "WHERE $colName IN ($placeholders)";
    }

    public static function WHERE_LIKE(string $colName, array $possibleValues): string
    {
        $likeClauses = implode(' OR ', array_map(function($value) use ($colName) {
            return "$colName LIKE :$value";
        }, $possibleValues));

        // WHERE plantName LIKE :sunflower OR plantName LIKE :rose..
        return "WHERE $likeClauses";
    }

    /**
     * @param string[] $colNames
     * @param mixed[][]
     */
    
    public static function INSERT_INTO(string $tableName, array $colNames, ?array $valuesMatrix = null): string
    {
        $colList = implode(', ', $colNames);
        
        if ($valuesMatrix !== null) {
            // array(array(1,2,3), array(4,5,6), ...) -> (1,2,3), (4,5,6), ...
            $valuesList = implode(', ', array_map(function($values) {
                return "(". implode(', ', $values) .')';
            }, $valuesMatrix));
            
            return "INSERT INTO $tableName ($colList) VALUES $valuesList";
        }
        $placeholders = implode(", ", array_map(function($colName) { return ":$colName"; }, $colNames));

        return "INSERT INTO $tableName ($colList) VALUES ($placeholders)";
    }

    /**
     * Summary of createQuery
     * @param string[] $statements
     * @return string
     */
    public static function combine(array $statements) : string 
    {
        $query = implode(' ', $statements);
        return $query;
    }
}
