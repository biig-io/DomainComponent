<?php

namespace Biig\Component\Domain\Event;

use Biig\Component\Domain\Exception\InvalidArgumentException;
use Biig\Component\Domain\Model\ModelInterface;
use Biig\Component\Domain\Rule\DomainRuleInterface;
use Biig\Component\Domain\Rule\PostPersistDomainRuleInterface;
use Biig\Component\Domain\Rule\RuleInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

final class DomainEventDispatcher extends EventDispatcher implements DomainEventDispatcherInterface
{
    /**
     * @var DelayedListener[]
     */
    private $delayedListeners;

    public function __construct()
    {
        parent::__construct();
        $this->delayedListeners = [];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function addRule(RuleInterface $rule)
    {
        if (!$rule instanceof DomainRuleInterface && !$rule instanceof PostPersistDomainRuleInterface) {
            throw new InvalidArgumentException('The domain rule must be an instance of DomainRuleInterface or PostPersistDomainRuleInterface.');
        }

        if ($rule instanceof DomainRuleInterface) {
            $this->addDomainRule($rule);
        }

        if ($rule instanceof PostPersistDomainRuleInterface) {
            $this->addPostPersistDomainRuleInterface($rule);
        }
    }

    public function addDomainRule(DomainRuleInterface $rule)
    {
        $events = $rule->on();

        if (!is_array($events)) {
            $events = [$events];
        }

        foreach ($events as $event) {
            $this->addListener($event, [$rule, 'execute']);
        }
    }

    public function addPostPersistDomainRuleInterface(PostPersistDomainRuleInterface $rule)
    {
        $events = $rule->after();

        if (!is_array($events)) {
            $events = [$events];
        }

        foreach ($events as $event) {
            if ($event instanceof DelayedListener) {
                $this->delayedListeners[] = $event;
                continue;
            }
            $this->delayedListeners[] = new DelayedListener($event, [$rule, 'execute']);
        }
    }

    /**
     * @param Event|null $event
     *
     * @throws \Biig\Component\Domain\Exception\InvalidDomainEvent
     *
     * @return Event
     */
    public function dispatch($event, string $eventName = null): object
    {
        $event = parent::dispatch($event, $eventName);

        if ($event instanceof DomainEvent) {
            foreach ($this->delayedListeners as $listener) {
                if ($listener->getEventName() === $eventName) {
                    $listener->occur($event);
                }
            }
        }

        return $event;
    }

    public function persistModel(ModelInterface $model)
    {
        foreach ($this->delayedListeners as $listener) {
            if ($listener->shouldOccur($model)) {
                $listener->process($model);
            }
        }
    }

    /**
     * @return DelayedListener[]
     */
    public function getDelayedListeners(): array
    {
        return $this->delayedListeners;
    }
}
