Domain component
================

This library is design to help you to build your application with a Domain Design Development approach.

It is well integrated with:

- Symfony >= 3.3
- ApiPlatform >= 2.1
- Doctrine >=2.5

But you can use it with any PHP project.

Features
--------

Domain Events:

* [Domain event dispatcher](docs/domain_event_dispatcher.md)
* [Injection of the dispatcher in Doctrine entities](docs/injection_in_doctrine_entities.md)
* [Symfony serializer integration](docs/symfony_serializer_integration.md)


Installation
------------

```bash
composer require biig/domain-component
```

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
