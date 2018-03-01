<?php

namespace Biig\Component\Domain\Model\Instantiator;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\ModelInterface;

/**
 * Use me to instantiate domain models. So I can inject the domain dispatcher.
 *
 * If they are not models from the domain I still can instantiate them. But I
 * will not inject the domain dispatcher, I promise.
 */
class Instantiator implements DomainModelInstantiatorInterface
{
    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    public function __construct(DomainEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function instantiate($className)
    {
        $object = new $className();
        $this->injectDispatcher($object);

        return $object;
    }

    public function instantiateWithArguments(string $className, ...$args)
    {
        $object = new $className(...$args);
        $this->injectDispatcher($object);

        return $object;
    }

    protected function injectDispatcher($object)
    {
        if ($object instanceof ModelInterface) {
            $object->setDispatcher($this->dispatcher);
        }
    }
}
