Domain Event Dispatcher
=======================

The domain event dispatcher is a special dispatcher in your application that dispatch only
domain events.


Make a rule
-----------

To make a new rule (its a listener) you should implement the `DomainRuleInterface`.

### Standalone usage

#### Add a standard rule

```php
<?php
use Biig\Component\Domain\Rule\DomainRuleInterface;
use Biig\Component\Domain\Event\DomainEvent;

$dispatcher->addRule(new class implements DomainRuleInterface {
    public function execute(DomainEvent $event) {
        // add some specific behavior
    }
    
    public function on() {
        return 'on.event';
    }
});
```

#### Add a post persist delayed rule

A post persist rule will occure only if the specified event is emit, but only after the data is persisted in storage. Basically flushed in the case of Doctrine.

```php
<?php
use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Rule\PostPersistDomainRuleInterface;

$dispatcher->addRule(new class implements PostPersistDomainRuleInterface {
    public function execute(DomainEvent $event) {
        // add some specific behavior
    }
    
    public function after() {
        return 'on.event'; // You have to specify the model
    }
});
```

Please notice you **need** to add some configuration to make it work:

```yaml
biig_domain:
    persist_listeners:
        # As doctrine supports many connections, you need to enable your connections one by one.
        # The most common is named "default".
        doctrine: ['default']
```


### Symfony Integration

If you use the Symfony Bundle with auto-configuration of your services.
**You don't have anything to do.**

If you don't auto-discover your services and don't enable auto-configuration, then you will need to add the tag:
```yaml
My\Domain\Rule:
    tags:
        - { name: biig_domain.rule }
```

If you don't want to use the given interface or want more control on the
configuration you still can configure your service by hand:

```yaml
My\Domain\Rule:
    tags:
        # You may add many tags to add many listeners to your business rule
        - { name: biig_domain.rule, event: 'your.event.name', method: 'execute', priority: 0 }
```

_Notice: the priority field is optional._


#### Configuration reference

```yaml
biig_domain:
    # It modifies the DoctrineBundle configuration to register a new
    # ClassMetadataInfo class so the instantiator now set the domain event
    # dispatcher to your models automatically
    override_doctrine_instantiator: true
    
    # By default it will override the doctrine instantiator only for
    # the "default" entity manager of your application. You can specify
    # many entity managers if you want.
    entity_managers: []
    
    # Post persist events are not activated by default, you need to enable the post persist listeners
    persist_listeners:
        # As doctrine supports many connections, you need to enable your connections one by one
        doctrine: ['default']
```
