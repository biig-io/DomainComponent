<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Doctrine\Instantiator\InstantiatorInterface;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\ClassMetadata as BaseClassMetadata;

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
}
