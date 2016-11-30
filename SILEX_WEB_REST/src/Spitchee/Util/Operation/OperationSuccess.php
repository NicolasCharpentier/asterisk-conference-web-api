<?php

namespace Spitchee\Util\Operation;

/**
 * Class OperationSuccess
 * @package Spitchee\Util\Operation
 */
class OperationSuccess implements OperationResult
{
    /**
     * @return bool
     */
    public function isSuccessfull()
    {
        return true;
    }

    /**
     * @return OperationSuccess
     */
    public static function create()
    {
        return new self();
    }
}