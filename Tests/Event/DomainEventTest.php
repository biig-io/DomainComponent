<?php

namespace Biig\Component\Domain\Tests\Event;

use Biig\Component\Domain\Event\DomainEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\GenericEvent;

class DomainEventTest extends TestCase
{
    public function testItIsInstanceOfGenericEvent()
    {
        $event = new DomainEvent();
        $this->assertInstanceOf(GenericEvent::class, $event);
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

    /**
     * @expectedException \Biig\Component\Domain\Exception\InvalidArgumentException
     */
    public function testItThrowsAnErrorIfOrignalEventIsNotAnEvent()
    {
        $event = new DomainEvent(null, [], 'foo');
    }
}
