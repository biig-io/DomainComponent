<?php

class FakeModel implements \Biig\Component\Domain\Model\ModelInterface
{
    use \Biig\Component\Domain\Model\DomainModelTrait;

    private $id;
    private $foo;
    private $something;

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
        $this->dispatch(new \Biig\Component\Domain\Event\DomainEvent($this), 'previous_action');
        $this->dispatch(new \Biig\Component\Domain\Event\DomainEvent($this), 'action');
    }
}
