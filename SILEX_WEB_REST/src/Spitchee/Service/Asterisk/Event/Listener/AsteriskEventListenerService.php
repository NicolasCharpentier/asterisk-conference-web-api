<?php

namespace Spitchee\Service\Asterisk\Event\Listener;


/**
 * Interface AsteriskEventListenerService
 * @package Spitchee\Service\Asterisk\Event\Listener
 */
interface AsteriskEventListenerService
{
    /**
     * @param $brutEventArray
     * @return mixed
     */
    public function processEvent($brutEventArray);
}