<?php
namespace Biig\Component\Domain\Tests\Debug;

use Biig\Component\Domain\Debug\TraceableDomainEventDispatcher;
use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Event\DomainEventDispatcher;
use PHPUnit\Framework\TestCase;

class TraceableDomainEventDispatcherTest extends TestCase
{
    public function testItSetsFiredEventOnDispatch()
    {
        $tdispatcher = new TraceableDomainEventDispatcher(new DomainEventDispatcher());
        $tdispatcher->dispatch(new DomainEvent(), 'foo');
        $this->assertContains('foo', $tdispatcher->getEventsFired());
    }
}
