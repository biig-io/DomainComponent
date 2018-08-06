<?php

namespace Biig\Component\Domain\Event;

use Biig\Component\Domain\Exception\InvalidArgumentException;
use Biig\Component\Domain\Model\ModelInterface;
use Biig\Component\Domain\Rule\DomainRuleInterface;
use Biig\Component\Domain\Rule\PostPersistDomainRuleInterface;
use Biig\Component\Domain\Rule\RuleInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DomainEventDispatcher extends EventDispatcher
{
    /**
     * @var DelayedListener[]
     */
    private $delayedListeners;

    public function __construct()
    {
        $this->delayedListeners = [];
    }

    /**
     * @param RuleInterface $rule
     *
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

    /**
     * @param DomainRuleInterface $rule
     */
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

    /**
     * @param PostPersistDomainRuleInterface $rule
     */
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
     * @param string     $eventName
     * @param Event|null $event
     *
     * @return Event
     */
    public function dispatch($eventName, Event $event = null)
    {
        $event = parent::dispatch($eventName, $event);

        if ($event instanceof DomainEvent) {
            foreach ($this->delayedListeners as $listener) {
                if ($listener->getEventName() === $eventName) {
                    $listener->occur($event);
                }
            }
        }

        return $event;
    }

    /**
     * @param ModelInterface $model
     */
    public function persistModel(ModelInterface $model)
    {
        foreach ($this->delayedListeners as $listener) {
            if ($listener->shouldOccur($model)) {
                $listener->process($model);
            }
        }
    }
}
