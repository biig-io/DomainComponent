<?php

namespace Biig\Component\Domain\Integration\Symfony\Serializer;

use Biig\Component\Domain\Event\DomainEventDispatcher;

/**
 * Trait DomainModelDenormalizer.
 *
 * @deprecated this trait is not maintained, use the `DomainDenormalizer` directly instead
 */
trait DomainModelDenormalizer
{
    /**
     * @var DomainEventDispatcher
     */
    private $dispatcher;

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $domain = $this->decorated->denormalize($data, $class, $format, $context);
        $domain->setDispatcher($this->dispatcher);

        return $domain;
    }
}
