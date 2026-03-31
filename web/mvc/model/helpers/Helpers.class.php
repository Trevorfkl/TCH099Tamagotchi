<?php

class Helper {


    /**
     * Réassigne les keys d'un array ordonné par une clé d'ordre pour inclure d'autre membre. La clé d'ordre des objets à insérer PREND PRIORITÉ (Par example, si deux objets ont une clé d'ordre avec la valeur de 2, l'objet dans l'array à insérer gardera sa valeur, tandis que l'original sera muté).
     * @param object[] $parentArray L'array d'objets original, l'attribut d'ordre de chaque élément de cet array sera muté
     * @param object[] $insertedArray L'array d'objets re-classifiant l'ordre.  
     * @param callable $getAttribute Accessor pour l'attribut qui définit l'ordre.
     * @param callable $setAttribute Mutator pour l'attribut qui définit l'ordre.
     * @throws InvalidArgumentException
     * @return void
     */
    public static function updateSortableObjectAttribute(array $parentArray, array $insertedArray, callable $getAttribute, callable $setAttribute): void 
    {
        if ($getAttribute(end($insertedArray)) >= count($parentArray) + count($insertedArray)) {
            $invalidKey = $getAttribute(end($insertedArray));
            $combinedSize = count($parentArray) + count($insertedArray);
            throw new InvalidArgumentException("La valeur de la clé du dernier élément ($invalidKey) ne peut pas dépasser la grandeur des deux arrays ($combinedSize) - 1");
        }
        // TODO: Checks pour l'ordre du array parent (doit être de forme 0, 1, ... n, n + 1)

        // TODO: Check pour que les clés d'ordres de l'array à insérer soit strictement ascendant 
        
        
        $parentKeys = array_map(fn($object) => $getAttribute($object), $parentArray);
        $insertedKeys = array_map(fn($object) => $getAttribute($object), $insertedArray);


        $parentCurrentIndex = 0;
        $insertedCurrentIndex = 0;
        $toAdd = 0;
        while ($insertedCurrentIndex < count($insertedArray)) {
            while ($parentKeys[$parentCurrentIndex] + $toAdd < $insertedKeys[$insertedCurrentIndex]) {
                $parentNewKey = $parentKeys[$parentCurrentIndex] + $toAdd;
                $parentRef = $parentArray[$parentCurrentIndex];
                $setAttribute($parentRef, $parentNewKey);
                
            }
            $toAdd++;
        }
        while ($parentCurrentIndex < count($parentArray)) {
            $parentNewKey = $parentKeys[$parentCurrentIndex] + $toAdd;
            $parentRef = $parentArray[$parentCurrentIndex];
            $setAttribute($parentRef, $parentNewKey);
        }
        
    }
}

?>