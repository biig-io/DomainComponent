<?php

namespace Biig\Component\Domain\Model;

use Biig\Component\Domain\Event\DomainEventDispatcher;

interface ModelInterface
{
    public function setDispatcher(DomainEventDispatcher $dispatcher);
}
