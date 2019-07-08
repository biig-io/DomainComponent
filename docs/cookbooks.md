Cookbooks
=========

This section is about some things you can do while using this component.

1. [Use events from another component in the domain dispatcher](#support_other_events)


Support other events
--------------------

In some special cases you may have to handle some other events. For example
the workflow of Symfony dispatch events specific to this composant. You can
transform these events by redefining the dispatcher and transform the event:

```php
<?php

class WorkflowDomainEventDispatcher extends DomainEventDispatcher
{
    public function dispatch(Event $event, $eventName = null)
    {
        if ($event instanceof \Symfony\Component\Workflow\Event\Event) {
            $event = new DomainEvent($event->getSubject(), [], $event);
        }

        return parent::dispatch($event, $eventName);
    }
}
```

You can do this because the domain event support embedded events. 👍
