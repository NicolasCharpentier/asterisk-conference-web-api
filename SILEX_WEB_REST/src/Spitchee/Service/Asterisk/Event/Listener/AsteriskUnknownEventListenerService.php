<?php

namespace Spitchee\Service\Asterisk\Event\Listener;

use Spitchee\Entity\NamiEvent;
use Spitchee\Service\Asterisk\Event\AsteriskEventsDefinitionService;
use Spitchee\Service\Generic\ContainerAwareService;

/**
 * Class AsteriskUnknownEventListenerService
 * @package Spitchee\Service\Asterisk\Event\Listener
 */
class AsteriskUnknownEventListenerService extends ContainerAwareService implements AsteriskEventListenerService
{
    /**
     * @param $brutEventArray
     * @return NamiEvent
     */
    public function processEvent($brutEventArray)
    {
        return new NamiEvent(
            AsteriskEventsDefinitionService::TYPE_NOT_HANDLED . ' - ' . $brutEventArray['event'],
            $brutEventArray
        );
    }
}