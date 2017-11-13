<?php

class FakeModel extends \Biig\Component\Domain\Model\DomainModel
{
    private $dispatcher;

    public function setDispatcher(\Biig\Component\Domain\Event\DomainEventDispatcher $dispatcher)
    {
        parent::setDispatcher($dispatcher);
        $this->dispatcher = $dispatcher;
    }

    public function hasDispatcher()
    {
        return $this->dispatcher !== null;
    }
}
