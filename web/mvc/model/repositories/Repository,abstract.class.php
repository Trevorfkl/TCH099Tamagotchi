<?php

abstract class Repository {
    protected static function map_ids(array $objects): array 
    {
        $mapping = [];
        foreach ($objects as $obj) {
            assert($obj instanceof Idable);
            $mapping[$obj->getId()] = $obj;
        }
        return $mapping;
    }

    abstract public function create(object $object): object;

    abstract public function delete(object $object): bool;

    abstract public function find(object $object): ?object;
}

?>