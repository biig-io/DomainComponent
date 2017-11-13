<?php

namespace Biig\Component\Domain\Tests\Event;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DomainEventDispatcherTest extends TestCase
{
    public function testItIsAnInstanceOfDispatcher()
    {
        $dispatcher = new DomainEventDispatcher();
        $this->assertInstanceOf(EventDispatcherInterface::class, $dispatcher);
    }
}
