<?php

namespace Biig\Component\Domain\Event;

use Biig\Component\Domain\Exception\InvalidArgumentException;
use Biig\Component\Domain\Model\ModelInterface;
use Biig\Component\Domain\Rule\DomainRuleInterface;
use Biig\Component\Domain\Rule\PostPersistDomainRuleInterface;
use Biig\Component\Domain\Rule\RuleInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Stopwatch\Stopwatch;

class DomainEventDispatcherTracer extends TraceableEventDispatcher implements DomainEventDispatcherInterface
{
    /**
     * @var DomainEventDispatcher
     */
    private $decorated;

    public function __construct(DomainEventDispatcher $domainEventDispatcher)
    {
        $this->decorated = $domainEventDispatcher;
        parent::__construct($domainEventDispatcher, new Stopwatch(), new NullLogger());
    }

    /**
     * @param RuleInterface $rule
     *
     * @throws InvalidArgumentException
     */
    public function addRule(RuleInterface $rule)
    {
        $this->decorated->addRule($rule);
    }

    /**
     * @param DomainRuleInterface $rule
     */
    public function addDomainRule(DomainRuleInterface $rule)
    {
        $this->decorated->addDomainRule($rule);
    }

    /**
     * @param PostPersistDomainRuleInterface $rule
     */
    public function addPostPersistDomainRuleInterface(PostPersistDomainRuleInterface $rule)
    {
        $this->decorated->addPostPersistDomainRuleInterface($rule);
    }

    /**
     * @param ModelInterface $model
     */
    public function persistModel(ModelInterface $model)
    {
        $this->decorated->persistModel($model);
    }
}
