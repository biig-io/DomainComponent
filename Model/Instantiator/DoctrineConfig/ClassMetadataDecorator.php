<?php

namespace Biig\Component\Domain\Model\Instantiator\DoctrineConfig;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\Instantiator\InstantiatorInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class ClassMetadataDecorator
 *
 * This class decorates the `ClassMetadata` class to use a different `Instantiator`.
 */
final class ClassMetadataDecorator implements ClassMetadataInterface
{
    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var InstantiatorInterface
     */
    private $instantiator;

    public function __construct(ClassMetadata $classMetadata, InstantiatorInterface $instantiator = null)
    {
        $this->classMetadata = $classMetadata;
        $this->instantiator = $instantiator;
    }

    /**
     * {@inheritdoc}
     */
    public function newInstance()
    {
        return $this->instantiator->instantiate($this->classMetadata->name);
    }

    /**
     * This method is needed because the ClassMetadataInfo class implements some more methods
     * than the interface (`newInstance` is one of them for example).
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->classMetadata, $name)) {
            return $this->classMetadata->$name(...$arguments);
        }

        throw new \BadMethodCallException(sprintf('The method %s does not exists.'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->classMetadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->classMetadata->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getReflectionClass()
    {
        return $this->classMetadata->getReflectionClass();
    }

    /**
     * {@inheritdoc}
     */
    public function isIdentifier($fieldName)
    {
        return $this->classMetadata->isIdentifier($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($fieldName)
    {
        return $this->classMetadata->hasField($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation($fieldName)
    {
        return $this->classMetadata->hasAssociation($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleValuedAssociation($fieldName)
    {
        return $this->classMetadata->isSingleValuedAssociation($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function isCollectionValuedAssociation($fieldName)
    {
        return $this->classMetadata->isCollectionValuedAssociation($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldNames()
    {
        return $this->classMetadata->getFieldNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierFieldNames()
    {
        return $this->classMetadata->getIdentifierFieldNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationNames()
    {
        return $this->classMetadata->getAssociationNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeOfField($fieldName)
    {
        return $this->classMetadata->getTypeOfField($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationTargetClass($assocName)
    {
        return $this->classMetadata->getAssociationTargetClass($assocName);
    }

    /**
     * {@inheritdoc}
     */
    public function isAssociationInverseSide($assocName)
    {
        return $this->classMetadata->isAssociationInverseSide($assocName);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationMappedByTargetField($assocName)
    {
        return $this->classMetadata->getAssociationMappedByTargetField($assocName);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierValues($object)
    {
        return $this->classMetadata->getIdentifierValues($object);
    }
}
