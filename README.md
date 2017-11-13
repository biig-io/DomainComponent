Domain component
================

Features
--------

* [Domain event dispatcher](docs/domain_event_dispatcher.md)
* [Injection of the dispatcher in Doctrine entities](docs/injection_in_doctrine_entities.md)


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
