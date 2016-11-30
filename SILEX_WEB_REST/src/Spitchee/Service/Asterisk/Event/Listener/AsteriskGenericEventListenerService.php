<?php

namespace Spitchee\Service\Asterisk\Event\Listener;

use Spitchee\Entity\NamiEvent;
use Spitchee\Service\Generic\ContainerAwareService;

/**
 * Class AsteriskGenericEventListenerService
 * @package Spitchee\Service\Asterisk\Event\Listener
 */
class AsteriskGenericEventListenerService extends ContainerAwareService implements AsteriskEventListenerService
{
    /**
     * @param $brutEventArray
     * @return NamiEvent
     */
    public function processEvent($brutEventArray)
    {
        return new NamiEvent($brutEventArray['event'], $brutEventArray);   
    }
}