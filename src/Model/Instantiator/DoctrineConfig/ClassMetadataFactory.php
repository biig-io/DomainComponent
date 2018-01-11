<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\Common\Persistence\Mapping\ReflectionService;
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
     * {@inheritdoc}
     */
    protected function wakeupReflection(ClassMetadataInterface $class, ReflectionService $reflService)
    {
        if ($class instanceof ClassMetadata) {
            $class->wakeupReflectionWithInstantiator($reflService, new Instantiator($this->dispatcher));

            return;
        }

        $class->wakeupReflection($reflService);
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
