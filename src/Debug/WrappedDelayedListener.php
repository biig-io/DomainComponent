<?php

namespace Biig\Component\Domain\Debug;

use Biig\Component\Domain\Event\DelayedListener;
use Symfony\Component\VarDumper\Caster\ClassStub;

class WrappedDelayedListener
{
    /**
     * @var callable
     */
    private $listener;

    /**
     * @var string
     */
    private $pretty;

    /**
     * @var string Class function stub
     */
    private $stub;

    /**
     * @var mixed|string
     */
    private $name;

    /**
     * Does ClassStub class exists ?
     *
     * @var bool|null
     */
    private static $hasClassStub;

    public function __construct(DelayedListener $listener)
    {
        $this->listener = $this->getListener($listener);

        $this->name = \is_object($this->listener[0]) ? \get_class($this->listener[0]) : $this->listener[0];
        $this->pretty = $this->name . '::' . $this->listener[1];

        if (null === self::$hasClassStub) {
            self::$hasClassStub = class_exists(ClassStub::class);
        }
    }

    private function getListener(DelayedListener $listener)
    {
        $reflectionClass = new \ReflectionClass(get_class($listener));
        $listenerProperty = $reflectionClass->getProperty('listener');
        $listenerProperty->setAccessible(true);

        return $listenerProperty->getValue($listener);
    }

    public function getInfo($eventName)
    {
        if (null === $this->stub) {
            $this->stub = self::$hasClassStub ? new ClassStub($this->pretty . '()', $this->listener) : $this->pretty . '()';
        }

        return [
            'event' => $eventName,
            'priority' => 0,
            'pretty' => $this->pretty,
            'stub' => $this->stub,
        ];
    }
}
