<?php

namespace Spitchee\Util\Operation;

/**
 * Class OperationFailure
 * @package Spitchee\Util\Operation
 */
class OperationFailure implements OperationResult
{
    const REASON_TYPE_SERVER = 1;
    const REASON_TYPE_CLIENT = 2;

    /** @var int $reason */
    private $reason;

    /** @var string|null $details */
    private $details;

    /**
     * OperationFailure constructor.
     * @param $reason
     * @param $details
     */
    private function __construct($reason, $details)
    {
        $this->reason   = $reason;
        $this->details  = $details;
    }

    /**
     * @param null $details
     * @return OperationFailure
     */
    public static function fromServer($details = null)
    {
        return new self(self::REASON_TYPE_SERVER, $details);
    }

    /**
     * @param null $details
     * @return OperationFailure
     */
    public static function fromClient($details = null)
    {
        return new self(self::REASON_TYPE_CLIENT, $details);
    }

    /**
     * @return bool
     */
    public function isSuccessfull()
    {
        return false;
    }

    /**
     * @return null|string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return int
     */
    public function getReason()
    {
        return $this->reason;
    }
}