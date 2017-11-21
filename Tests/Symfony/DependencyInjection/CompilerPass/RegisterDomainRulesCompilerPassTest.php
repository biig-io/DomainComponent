<?php

namespace Biig\Component\Domain\Tests\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass\RegisterDomainRulesCompilerPass;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\DomainExtension;
use Biig\Component\Domain\Rule\DomainRuleInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterDomainRulesCompilerPassTest extends TestCase
{
    public function testStandardRule()
    {
        $container = new ContainerBuilder();
        $container->register(FooRule::class, FooRule::class)->addTag(DomainExtension::DOMAIN_RULE_TAG, []);
        $container->register('biig_domain.dispatcher', DomainEventDispatcher::class);

        $registerListenersPass = new RegisterDomainRulesCompilerPass();
        $registerListenersPass->process($container);

        $container->compile();

        /** @var DomainEventDispatcher $dispatcher */
        $dispatcher = $container->get('biig_domain.dispatcher');
        $listeners = $dispatcher->getListeners();

        $this->assertTrue(is_callable($listeners['test.event'][0]));
    }

    public function testUserDefinedRule()
    {
        $container = new ContainerBuilder();
        $container->register('foo', Foo::class)->addTag(DomainExtension::DOMAIN_RULE_TAG, [
            'method' => 'hello',
            'event' => 'foo.changed'
        ]);
        $container->register('biig_domain.dispatcher', DomainEventDispatcher::class);

        $registerListenersPass = new RegisterDomainRulesCompilerPass();
        $registerListenersPass->process($container);

        $container->compile();

        /** @var DomainEventDispatcher $dispatcher */
        $dispatcher = $container->get('biig_domain.dispatcher');
        $listeners = $dispatcher->getListeners();

        $this->assertTrue(is_callable($listeners['foo.changed'][0]));
    }

    public function testDomainRuleUserDefined()
    {
        $container = new ContainerBuilder();
        $container->register('foo', FooRule::class)->addTag(DomainExtension::DOMAIN_RULE_TAG, [
            'method' => 'execute',
            'event' => 'test.event'
        ]);
        $container->register('biig_domain.dispatcher', DomainEventDispatcher::class);

        $registerListenersPass = new RegisterDomainRulesCompilerPass();
        $registerListenersPass->process($container);

        $container->compile();

        /** @var DomainEventDispatcher $dispatcher */
        $dispatcher = $container->get('biig_domain.dispatcher');
        $listeners = $dispatcher->getListeners();

        $this->assertTrue(is_callable($listeners['test.event'][0]));
    }
}

class FooRule implements DomainRuleInterface
{
    public function execute(DomainEvent $event) {}

    public function on()
    {
        return 'test.event';
    }
}

class Foo
{
    public function hello() {}
}
