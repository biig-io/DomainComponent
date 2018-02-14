<?php

namespace Biig\Component\Domain\PostPersistListener;

use Biig\Component\Domain\Event\DomainEventDispatcher;

/**
 * The job of the children of this class is to take a persist event
 * and send it to the domain dispatcher.
 */
abstract class AbstractBridgeListener
{
    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    public function __construct(DomainEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    protected function getDispatcher()
    {
        return $this->dispatcher;
    }
}
