<?php

namespace App\Services;

class ExplodeAndSum
{

    /**
     * Summary of explodeAndSum
     * @param array|string $arrayOfString
     * @return int|float|null
     */
    public function explodeAndSum(array|string $arrayOfString): int|float|null
    {
        $total = 0;
        $arrayOfString = is_array($arrayOfString) ? $arrayOfString : [$arrayOfString];
        foreach ($arrayOfString as $string) {
            $stringToArray = explode('#', $string);
            foreach ($stringToArray as $value) {
                $number = explode(",", $value);
                if (isset($number[1]) && !empty($number[1])) {
                    $total += $number[1];
                }
            }
        }
        return $total;
    }
}