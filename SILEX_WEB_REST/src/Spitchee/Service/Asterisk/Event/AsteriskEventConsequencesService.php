<?php

namespace Spitchee\Service\Asterisk\Event;

use Container;
use Spitchee\Entity\NamiEvent;

/**
 * Class AsteriskEventConsequencesService
 * @package Spitchee\Service\Asterisk\Event
 */
class AsteriskEventConsequencesService
{
    /** @var \Monolog\Logger $logger */
    private $logger;

    /** @var array $consequences */

    private $consequences;

    /** @var NamiEvent $event */
    private $event;

    /**
     * AsteriskEventConsequencesService constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->logger = $container->getLogger();
        $this->consequences = array();
    }

    /**
     * @param $consequence
     * @return $this
     */
    public function addConsequence($consequence)
    {
        $this->consequences[] = $consequence;

        return $this;
    }

    /**
     * @param NamiEvent|null $event
     * @return $this
     */
    public function setCause(NamiEvent $event = null)
    {
        $this->event = $event;

        return $this;
    }

    public function log()
    {
        if (/*null === $this->event and*/ 0 == count($this->consequences)) {
            return $this;
        }

        if (null === $this->event) {
            $eventDescription = 'Evenement non géré';
        } else {
            $eventDescription = 'Evenement #' . $this->event->getId() . ' de type ' . $this->event->getType();
        }

        if (0 == count($this->consequences)) {
            $consequence = 'rien du tout samer';
        } else {
            $consequence = join(' + ', $this->consequences);
        }

        $this->logger->addNotice("$eventDescription a causé [ $consequence ]");

        return $this;
    }
}