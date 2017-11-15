<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory as BaseClassMetadataFactory;

final class ClassMetadataFactory extends BaseClassMetadataFactory
{
    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    public function newClassMetadataInstance($className)
    {
        return new ClassMetadata($className, new Instantiator($this->dispatcher), $this->entityManager->getConfiguration()->getNamingStrategy());
    }

    /**
     * @param DomainEventDispatcher $dispatcher
     */
    public function setDispatcher(DomainEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
        parent::setEntityManager($em);
    }
}
