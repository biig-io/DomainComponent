<?php

class FakeModel extends \Biig\Component\Domain\Model\DomainModel
{
    private $id;
    private $foo;
    private $dispatcher;
    private $something;

    public function setDispatcher(\Biig\Component\Domain\Event\DomainEventDispatcher $dispatcher)
    {
        parent::setDispatcher($dispatcher);
        $this->dispatcher = $dispatcher;
    }

    public function hasDispatcher()
    {
        return null !== $this->dispatcher;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @return mixed
     */
    public function getSomething()
    {
        return $this->something;
    }

    /**
     * @param mixed $something
     */
    public function setSomething($something)
    {
        $this->something = $something;
    }

    /**
     * @param string $foo
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    /**
     * Raise a domain event.
     */
    public function doAction()
    {
        $this->dispatch('action', new \Biig\Component\Domain\Event\DomainEvent($this));
    }
}
