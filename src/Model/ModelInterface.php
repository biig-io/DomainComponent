<?php

namespace Biig\Component\Domain\Model;

use Biig\Component\Domain\Event\DomainEventDispatcherInterface;

interface ModelInterface
{
    public function setDispatcher(DomainEventDispatcherInterface $dispatcher);
}
