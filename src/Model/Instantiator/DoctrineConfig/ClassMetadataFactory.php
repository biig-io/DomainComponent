<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Event\DomainEventDispatcherInterface;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as OldClassMetadataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory as BaseClassMetadataFactory;
use Doctrine\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\Persistence\Mapping\ReflectionService;

if (interface_exists(\Doctrine\Persistence\Mapping\ClassMetadata::class)) {
    final class ClassMetadataFactory extends BaseClassMetadataFactory
    {
        /**
         * @var DomainEventDispatcherInterface
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

        public function setDispatcher(DomainEventDispatcherInterface $dispatcher)
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

        public function setEntityManager(EntityManagerInterface $em)
        {
            $this->entityManager = $em;
            parent::setEntityManager($em);
        }
    }
} else {
    // Compatibility layer for Doctrine ORM <= 2.6
    final class ClassMetadataFactory extends BaseClassMetadataFactory
    {
        /**
         * @var DomainEventDispatcherInterface
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

        public function setDispatcher(DomainEventDispatcherInterface $dispatcher)
        {
            $this->dispatcher = $dispatcher;
        }

        /**
         * {@inheritdoc}
         */
        protected function wakeupReflection(OldClassMetadataInterface $class, ReflectionService $reflService)
        {
            if ($class instanceof ClassMetadata) {
                $class->wakeupReflectionWithInstantiator($reflService, new Instantiator($this->dispatcher));

                return;
            }

            $class->wakeupReflection($reflService);
        }

        public function setEntityManager(EntityManagerInterface $em)
        {
            $this->entityManager = $em;
            parent::setEntityManager($em);
        }
    }
}
