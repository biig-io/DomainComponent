<?php

namespace Biig\Component\Domain\Integration\Symfony\Serializer;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\ModelInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class DomainDenormalizer.
 *
 * @final in v2.x
 */
/* final */ class DomainDenormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $decorated;

    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    public function __construct(NormalizerInterface $decorated, DomainEventDispatcher $dispatcher)
    {
        $this->decorated = $decorated;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $domain = $this->decorated->denormalize($data, $class, $format, $context);

        if (is_subclass_of($class, ModelInterface::class)) {
            $domain->setDispatcher($this->dispatcher);
        }

        return $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $this->decorated->normalize($object, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
