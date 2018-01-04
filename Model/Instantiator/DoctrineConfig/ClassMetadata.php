<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Biig\Component\Domain\Model\Instantiator\DomainModelInstantiatorInterface;
use Doctrine\Common\Persistence\Mapping\ReflectionService;
use Doctrine\Instantiator\InstantiatorInterface;
use Doctrine\ORM\Mapping\ClassMetadata as BaseClassMetadata;
use Doctrine\ORM\Mapping\NamingStrategy;

/**
 * Class ClassMetadata for domain dispatcher injection.
 *
 * In the future the class to extends will be ClassMetadataInfo
 */
class ClassMetadata extends BaseClassMetadata
{
    /**
     * @var InstantiatorInterface
     */
    private $instantiator;

    public function __construct($entityName, InstantiatorInterface $instantiator, NamingStrategy $namingStrategy = null)
    {
        parent::__construct($entityName, $namingStrategy);
        $this->instantiator = $instantiator;
    }

    /**
     * {@inheritdoc}
     */
    public function newInstance()
    {
        return $this->instantiator->instantiate(parent::newInstance($this->name));
    }

    /**
     * @param ReflectionService                $reflService
     * @param DomainModelInstantiatorInterface $instantiator
     */
    public function wakeupReflectionWithInstantiator($reflService, $instantiator)
    {
        $this->instantiator = $instantiator;
        parent::wakeupReflection($reflService);
    }
}
