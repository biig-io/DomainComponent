<?php

namespace Biig\Component\Domain\Integration\Symfony\DependencyInjection\CompilerPass;

use Biig\Component\Domain\Exception\InvalidArgumentException;
use Biig\Component\Domain\Integration\Symfony\DependencyInjection\DomainExtension;
use Biig\Component\Domain\Rule\DomainRuleInterface;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterDomainRulesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('biig_domain.dispatcher');

        foreach ($container->findTaggedServiceIds(DomainExtension::DOMAIN_RULE_TAG, true) as $id => $attributes) {
            $def = $container->getDefinition($id);

            $class = $container->getParameterBag()->resolveValue($def->getClass());

            if (!is_subclass_of($class, DomainRuleInterface::class) || $this->notEmpty($attributes)) {
                foreach ($attributes as $attribute) {
                    $this->addListenerForEventsInDefinition($id, $class, $attribute, $definition);
                }
            } else {
                $definition->addMethodCall('addRule', [
                    new Reference($id),
                ]);
            }
        }
    }

    /**
     * @param string     $id
     * @param string     $class
     * @param array      $attribute
     * @param Definition $definition
     *
     * @throws InvalidArgumentException
     */
    private function addListenerForEventsInDefinition(string $id, string $class, array $attribute, Definition $definition)
    {
        // Rules may not implement the
        $method = $attribute['method'] ?? null;
        $event = $attribute['event'] ?? null;
        $priority = $attribute['priority'] ?? 0;

        if (!class_exists($class, false)) {
            throw new InvalidArgumentException(
                sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id)
            );
        }

        if (null === $method || null === $event) {
            throw new InvalidArgumentException(sprintf(
                'Impossible to register class "%s" as domain listener: the service configuration is wrong.',
                $class
            ));
        }

        $definition->addMethodCall('addListener', [
            $event,
            [new ServiceClosureArgument(new Reference($id)), $method],
            $priority,
        ]);
    }

    /**
     * `!empty()` is not enough to check multidimensional arrays emptiness.
     *
     * @param array $attributes
     *
     * @return bool
     */
    private function notEmpty(array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (!empty($attribute)) {
                return true;
            }
        }

        return false;
    }
}
