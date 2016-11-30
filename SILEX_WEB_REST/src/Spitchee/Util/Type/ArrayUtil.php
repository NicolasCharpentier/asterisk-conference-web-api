<?php

namespace Spitchee\Util\Type;

/**
 * Class ArrayUtil
 * @package Spitchee\Util\Type
 */
class ArrayUtil
{
    /**
     * @param $array
     * @return array
     */
    static public function asCleanNumericArray($array)
    {
        $cleaned = array();

        foreach ($array as $val) {
            $cleaned[] = $val;
        }

        return $cleaned;
    }
}