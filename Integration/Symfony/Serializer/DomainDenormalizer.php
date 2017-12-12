<?php

namespace Biig\Component\Domain\Integration\Symfony\Serializer;

use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\DomainModel;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class DomainDenormalizer implements DenormalizerInterface
{
    use DomainModelDenormalizer;

    /**
     * @var ObjectNormalizer
     */
    private $decorated;

    public function __construct(ObjectNormalizer $decorated, DomainEventDispatcher $dispatcher)
    {
        $this->decorated = $decorated;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type) && is_subclass_of($type, DomainModel::class);
    }
}
