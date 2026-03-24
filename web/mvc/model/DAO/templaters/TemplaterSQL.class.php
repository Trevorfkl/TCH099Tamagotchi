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

    public static function INSERT_INTO(string $tableName, array $colNames): string
    {
        $colList = implode(', ', $colNames);
        $placeholders = implode(', ', array_map(function($name) {
            return ":$name";
        }, $colNames));
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
