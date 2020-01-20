<?php
namespace Biig\Component\Domain\Tests\Debug;

use Biig\Component\Domain\Debug\TraceableDomainEventDispatcher;
use Biig\Component\Domain\Event\DelayedListener;
use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Event\DomainEventDispatcherInterface;
use Biig\Component\Domain\Model\ModelInterface;
use Biig\Component\Domain\Rule\DomainRuleInterface;
use Biig\Component\Domain\Rule\PostPersistDomainRuleInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TraceableDomainEventDispatcherTest extends TestCase
{
    public function testItSetsFiredEventOnDispatch()
    {
        $tdispatcher = new TraceableDomainEventDispatcher(new DomainEventDispatcher());
        $tdispatcher->dispatch(new DomainEvent(), 'foo');
        $this->assertContains('foo', $tdispatcher->getEventsFired());
    }

    public function testItPersistsModel()
    {
        $domainEventDispatcher = $this
            ->prophesize(DomainEventDispatcherInterface::class);
        $domainEventDispatcher->willImplement(EventDispatcherInterface::class);

        $model = $this->prophesize(ModelInterface::class);

        $delayedListener1 = $this->prophesize(FakeDelayedListener::class);
        $delayedListener1->getEventName()->willReturn('foo');
        $delayedListener1->shouldOccur($model)->willReturn(true);

        $delayedListener2 = $this->prophesize(FakeDelayedListener::class);
        $delayedListener2->getEventName()->willReturn('foo');
        $delayedListener2->shouldOccur($model)->willReturn(false);

        $domainEventDispatcher->getDelayedListeners()->willReturn([$delayedListener1, $delayedListener2]);

        $tdispatcher = new TraceableDomainEventDispatcher($domainEventDispatcher->reveal());

        $domainEventDispatcher->persistModel($model->reveal())->shouldBeCalled();

        $tdispatcher->persistModel($model->reveal());

        $this->assertEquals([
            ['event' => 'foo', 'priority' => 0, 'pretty' => 'FakeCalleable2::execute', 'stub' => 'FakeCalleable2::execute()']
        ], $tdispatcher->getDelayedListenersCalled());
    }

    public function testItAddsDomainRule()
    {
        $domainEventDispatcher = $this
            ->prophesize(DomainEventDispatcherInterface::class);
        $domainEventDispatcher->willImplement(EventDispatcherInterface::class);

        $rule = $this->prophesize(DomainRuleInterface::class);
        $tdispatcher = new TraceableDomainEventDispatcher($domainEventDispatcher->reveal());
        $domainEventDispatcher->addDomainRule($rule->reveal())->shouldBeCalled();
        $tdispatcher->addDomainRule($rule->reveal());
    }

    public function testItAddsPostPersistDomainRuleInterface()
    {
        $domainEventDispatcher = $this
            ->prophesize(DomainEventDispatcherInterface::class);
        $domainEventDispatcher->willImplement(EventDispatcherInterface::class);

        $rule = $this->prophesize(PostPersistDomainRuleInterface::class);
        $tdispatcher = new TraceableDomainEventDispatcher($domainEventDispatcher->reveal());
        $domainEventDispatcher->addPostPersistDomainRuleInterface($rule->reveal())->shouldBeCalled();
        $tdispatcher->addPostPersistDomainRuleInterface($rule->reveal());
    }
}

class FakeCalleable2 {
    public function execute()
    {}
}

class FakeDelayedListener extends DelayedListener {
    public $listener = ['FakeCalleable2', 'execute'];
}
