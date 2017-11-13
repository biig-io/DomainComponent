Injection of DomainEventDispatcher in Doctrine entities
=======================================================

_This feature allows you to merge your doctrine entities with DDD model._

_To achieve this goal it provides you a set of classes to extends doctrine behavior on entities instantiation._

How it works
------------

Doctrine uses an `Instantiator` class to instantiate entities. (this is some kind of factory)

As this `Instantiator` is hardly instantiated by Doctrine, we need to extends the ORM core. Which mean
this feature **may** be in **conflict** with some other packages that may extends doctrine behavior (I don't know any).


### Usage without integration
 
```php
<?php
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Biig\Component\Domain\Event\DomainEventDispatcher;

$dispatcher = new DomainEventDispatcher();
$configuration = new \Doctrine\ORM\Configuration();
$configuration->setClassMetadataFactoryName(new ClassMetadataFactory($dispatcher));
$entityManager = new \Doctrine\ORM\EntityManager($connection, $configuration);
```

### Symfony integration

And then you can enable or disable this feature. Here is the default configuration:

```yaml
biig_domain:
    override_doctrine_instantiator: true
```

⚠ You need to know it
----------------------

When you use this feature, you need to keep in mind that instantiate entities by hand makes no sense. Be sure
to use at least the default instantiator. Accessible with the service `biig_domain.instantiator.default` if you use Symfony bundle.
