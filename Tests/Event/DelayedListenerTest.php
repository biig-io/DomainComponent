<?php

namespace Biig\Component\Domain\Tests\Event;

use Biig\Component\Domain\Event\DelayedListener;
use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Model\DomainModel;
use PHPUnit\Framework\TestCase;

class DelayedListenerTest extends TestCase
{
    public function testICanInstantiateDelayedListener()
    {
        $delayedListener = new DelayedListener('foo', function () {});
        $this->assertInstanceOf(DelayedListener::class, $delayedListener);
    }

    public function testItProcessEventOnlyOneTime()
    {
        $count = 0;
        $delayedListener = new DelayedListener('foo', function () use (&$count) {
            ++$count;
        });

        $fakeModel = new FakeDomainModel();
        $delayedListener->occur(new DomainEvent($fakeModel));
        $delayedListener->occur(new DomainEvent($fakeModel));
        $this->assertTrue($delayedListener->shouldOccur($fakeModel));

        $delayedListener->process($fakeModel);
        $this->assertFalse($delayedListener->shouldOccur($fakeModel));
        $this->assertEquals(2, $count);

        $delayedListener->process($fakeModel);
        $this->assertEquals(2, $count);
    }

    /**
     * @expectedException \Biig\Component\Domain\Exception\InvalidDomainEvent
     */
    public function testItFailsToRegisterOtherThanCurrentModel()
    {
        $model = new class() {
            public $foo;
        };

        $listener = new DelayedListener('foo', function () {});
        $listener->occur(new DomainEvent($model));
    }
}

class FakeDomainModel extends DomainModel
{
}
