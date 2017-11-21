<?php

namespace Biig\Component\Domain\Tests\Event;

use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\DomainModel;
use Biig\Component\Domain\Rule\DomainRuleInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DomainEventDispatcherTest extends TestCase
{
    public function testItIsAnInstanceOfDispatcher()
    {
        $dispatcher = new DomainEventDispatcher();
        $this->assertInstanceOf(EventDispatcherInterface::class, $dispatcher);
    }

    public function testItAllowAdditionOfRule()
    {
        // Fixtures
        $rule = new class implements DomainRuleInterface {
            public function execute(DomainEvent $event)
            {
                $event->getSubject()->setLowerFoo(strtolower($event->getSubject()->getFoo()));
            }

            public function on()
            {
                return 'foo.changed';
            }
        };
        $model = new class extends DomainModel {
            private $foo;
            private $lowerFoo;
            public function setFoo($foo) {
                $this->foo = $foo;
                $this->dispatch('foo.changed', new DomainEvent($this));
            }
            public function getFoo() { return $this->foo; }
            public function setLowerFoo($lower) { $this->lowerFoo = $lower; }
            public function getLowerFoo() { return $this->lowerFoo; }
        };


        // Test initialization
        $dispacher = new DomainEventDispatcher();
        $dispacher->addRule($rule);
        $model->setDispatcher($dispacher);

        // Test
        $model->setFoo('Hello');
        $this->assertEquals('hello', $model->getLowerFoo());
    }

    public function testItDispatchManyEventsForARule()
    {
        // Fixtures
        $rule = new class implements DomainRuleInterface {
            public function execute(DomainEvent $event)
            {
                $event->getSubject()->setLowerFoo(
                    strtolower($event->getSubject()->getFoo() . $event->getSubject()->getBar())
                );
            }

            public function on()
            {
                return [ 'foo.changed', 'bar.changed' ];
            }
        };
        $model = new class extends DomainModel {
            private $foo;
            private $bar;
            private $lowerFoo;
            public function setFoo($foo) {
                $this->foo = $foo;
                $this->dispatch('foo.changed', new DomainEvent($this));
            }
            public function setBar($bar) {
                $this->bar = $bar;
                $this->dispatch('bar.changed', new DomainEvent($this));
            }
            public function getFoo() { return $this->foo; }
            public function getBar() { return $this->bar; }
            public function setLowerFoo($lower) { $this->lowerFoo = $lower; }
            public function getLowerFoo() { return $this->lowerFoo; }
        };

        // Test initialization
        $dispacher = new DomainEventDispatcher();
        $dispacher->addRule($rule);
        $model->setDispatcher($dispacher);

        // Test
        $model->setFoo('Hello');
        $model->setBar(' World');
        $this->assertEquals('hello world', $model->getLowerFoo());
    }
}

