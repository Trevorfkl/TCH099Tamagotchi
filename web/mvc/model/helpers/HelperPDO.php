<?php

class HelperPDO 
{
    public static function bindAutoParam(PDOStatement $request, string $param, mixed $value) : void
    {
        switch (gettype($value)) {
            case 'integer':
                $paramType = PDO::PARAM_INT;
                break;
            case 'boolean':
                $paramType = PDO::PARAM_BOOL;
                break;
            case 'string':
                $paramType = PDO::PARAM_STR;
                break;
            case 'NULL':
                $paramType = PDO::PARAM_NULL;
                break;
            default:
                throw new InvalidArgumentException("Unsupported parameter type: " . gettype($value));
        }
        $request->bindValue($param, $value, $paramType);
    }
}

?>