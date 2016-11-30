<?php

namespace Spitchee\Service\Asterisk\Event\Listener;

/**
 * Class AsteriskNullEventListenerService
 * @package Spitchee\Service\Asterisk\Event\Listener
 */
class AsteriskNullEventListenerService implements AsteriskEventListenerService
{
    /**
     * @param $brutEventArray
     * @return null
     */
    public function processEvent($brutEventArray)
    {
        return null;
    }
}