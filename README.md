Domain component
================

[![Build Status](https://travis-ci.org/biig-io/DomainComponent.svg?branch=master)](https://travis-ci.org/biig-io/DomainComponent)

This library is design to help you to build your application with a Domain Design Development approach.

It is well integrated with:

- Symfony >= 3.3
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
        $this->dispatch(self::CREATION, new DomainEvent($this);
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
    
    public function execute(DomainEvent)
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
// In your Kernel class
public function registerBundles()
{
      return array(
          // ...
          new \Biig\Component\Domain\Integration\Symfony\DomainBundle(),
          // ...
      );
}
```
