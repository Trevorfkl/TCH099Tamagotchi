<?php

class projectUtils {
    public static function evenlySpreadIndices(int $a, int $b): array  
	{  
		if ($a < 1 || $b < $a) {  
			throw new InvalidArgumentException("Require b >= a >= 1");  
		}  
		  
		$indices = [];  
		  
		if ($a === 1) {  
			return [0];  
		}  
		  
		$step = ($b - 1) / ($a - 1);  
		  
		for ($i = 0; $i < $a; $i++) {  
			$indices[] = (int) round($i * $step);  
		}  
		  
		return $indices;  
	}

    public static function getClosestIndex(int $value, array $sortedArr): int 
    {
        if (count($sortedArr) < 2) {
            throw new InvalidArgumentException("Require array size >= 2");  
        }
        $lowerBound = $sortedArr[0];
        if ($lowerBound === $value) {
            return 0;
        }

        for ($i = 1; $i < count($sortedArr); $i++) {
            if ($sortedArr[$i] === $value) {
                return $i;
            }
            $lowerBound = $sortedArr[$i - 1];
            if ($value > $lowerBound && $value < $sortedArr[$i]) {
                return $i;
            }
        }
        return 1;
    }

    public static function calculateNewCurrentIndex(int $prevArraySize, int $newArraySize, int $currentIndex) : int 
    {
        if ($newArraySize >= $prevArraySize) {
            return $currentIndex;
        }
        $ratio = $newArraySize / $prevArraySize;
        return (int) round($ratio * $currentIndex);
    }
}

?>