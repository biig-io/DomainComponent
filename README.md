Domain component
================

[![Build Status](https://travis-ci.org/biig-io/DomainComponent.svg?branch=master)](https://travis-ci.org/biig-io/DomainComponent)
[![Latest Stable Version](https://poser.pugx.org/biig/domain/v/stable)](https://packagist.org/packages/biig/domain)
[![License](https://poser.pugx.org/biig/domain/license)](https://packagist.org/packages/biig/domain)

This library is design to help you to build your application with a Domain Design Development approach.

It is well integrated with:

- Symfony >= 4.3 (for >=3.3 compatibility, install the version 1.5 of the domain component)
- ApiPlatform >= 2.1
- Doctrine >=2.5

But you can use it with any PHP project.

[Here are some slides](http://talks.nekland.fr/DoctrineDomainEvents/) that explain how we get there.

Features
--------

Domain Events:

* [Domain event dispatcher](docs/domain_event_dispatcher.md)
* [Injection of the dispatcher in Doctrine entities](docs/injection_in_doctrine_entities.md)
* [Symfony serializer integration](docs/symfony_serializer_integration.md)
* [Learn how do more with our cookbooks](docs/cookbooks.md)

Drawbacks
---------

This library is build to allow you to use Doctrine models as Domain model. This has some cost:
you can't instantiate domain model by hand anymore. This means that you need a factory for any of
the usage of your domain model.

This component provides the implementation for Symfony serializer and Doctrine. For your own
needs you should use the class (service if you use the bundle) `Biig\Component\Domain\Model\Instantiator\Instantiator`.

Installation
------------

```bash
composer require biig/domain
```

Basic usage
-----------

```php
class YourModel extends DomainModel
{
    public const CREATION = 'creation';
    public function __construct()
    {
        $this->dispatch(new DomainEvent($this), self::CREATION);
    }
}
```

```php
class DomainRule implements DomainRuleInterface
{
    public function on()
    {
        return YourModel::CREATION;
    }
    
    public function execute(DomainEvent $event)
    {
        // Do Something on your model creation
    }
}
```

As your model needs a dispatcher you need to call the `setDispatcher()` method any time you create a new instance of your model. To avoid doing this manually you can use the `Instantiator` that the library provides.

> It doesn't use the constructor to add the dispatcher because in PHP you can create objects without the constructor. For instance, that's what Doctrine does.

Integration to Symfony
----------------------

Use the bundle :

```php
<?php
// config/bundles.php

return [
    // ...
    Biig\Component\Domain\Integration\Symfony\DomainBundle::class => ['all' => true],
];
```

Learn more about [Symfony Integration](/docs/domain_event_dispatcher.md#symfony-integration)

Versions
--------

| Version | Status     | Documentation | Symfony Version | PHP Version |
|---------|------------|---------------| --------------- | ------------|
| 1.x     | Maintained | [v1][v1-doc]  | '>= 3.3 && <5'  | '>= 7.1'    |
| 2.x     | Latest     | [v2][v2-doc]  | '>= 4.3'        | '>= 7.1'    |

[v1-doc]: https://github.com/biig-io/DomainComponent/tree/v1
[v2-doc]: https://github.com/biig-io/DomainComponent
