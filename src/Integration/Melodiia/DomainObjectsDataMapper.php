<?php

namespace Biig\Component\Domain\Integration\Melodiia;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\DomainModel;
use Biig\Melodiia\Bridge\Symfony\Form\DomainObjectsDataMapper as OriginalDomainObjectsDataMapper;
use Biig\Melodiia\Bridge\Symfony\Form\DomainObjectDataMapperInterface;

/**
 * The DomainObjectsDataMapper class of Melodiia is a sort of instantiator.
 * We need to decorate it in order to make things smooth for the user with the domain component.
 */
class DomainObjectsDataMapper implements DomainObjectDataMapperInterface
{
    /**
     * @var DomainObjectDataMapperInterface
     */
    private $decorated;

    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    public function __construct(DomainObjectDataMapperInterface $dataMapper, DomainEventDispatcher $dispatcher)
    {
        $this->decorated = $dataMapper;
        $this->dispatcher = $dispatcher;
    }

    public function createObject(iterable $form, string $dataClass = null)
    {
        $object = parent::createObject($form, $dataClass);

        if ($object instanceof DomainModel) {
            $object->setDispatcher($this->dispatcher);
        }

        return $object;
    }
}
