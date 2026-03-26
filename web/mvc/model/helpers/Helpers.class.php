<?php

class Helper {


    public static function updateSortableObjectAttribute(array $aArray, array $bArray, callable $getAttribute, callable $setAttribute): void 
    {
        
        $aIds = array_map(function ($a) use ($getAttribute) {
            return $getAttribute($a);
        }, $aArray);

        $bIds = array_map(function ($b) use ($getAttribute) {
            return $getAttribute($b);
        }, $bArray);


        $aCurrentIndex = 0;
        $bCurrentIndex = 0;
        $toAdd = 0;
        while ($bCurrentIndex < count($bArray)) {
            while ($aIds[$aCurrentIndex] + $toAdd < $bIds[$bCurrentIndex]) {
                $setAttribute($aArray[$aCurrentIndex], $aArray[$aCurrentIndex]->id + $toAdd);
                
            }
            $sortedArray[] = $bArray[$bCurrentIndex++];
            $toAdd++;
        }
        while ($aCurrentIndex < count($aArray)) {
            $setAttribute($aArray[$aCurrentIndex], $aArray[$aCurrentIndex]->id + $toAdd);
        }
        
    }
}

?>