<?php

namespace Biig\Component\Domain\Tests\Event;

use Biig\Component\Domain\Event\DomainEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

class DomainEventTest extends TestCase
{
    public function testItIsInstanceOfSymfonyEvent()
    {
        $event = new DomainEvent();
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testItAcceptAnEventAsParameter()
    {
        $event = new DomainEvent(null, [], new GenericEvent());

        $this->assertInstanceOf(GenericEvent::class, $event->getOriginalEvent());
    }

    public function testItReturnIfDelayedOrNot()
    {
        $event = new DomainEvent();

        $this->assertFalse($event->isDelayed());
        $event->setDelayed();
        $this->assertTrue($event->isDelayed());
    }
}
