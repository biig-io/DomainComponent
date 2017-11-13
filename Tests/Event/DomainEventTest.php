<?php

namespace Biig\Component\Domain\Tests\Event;

use Biig\Component\Domain\Event\DomainEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

class DomainEventTest extends TestCase
{
    public function testItIsInstanceOfSymfonyEvent()
    {
        $event = new DomainEvent();
        $this->assertInstanceOf(Event::class, $event);
    }
}
