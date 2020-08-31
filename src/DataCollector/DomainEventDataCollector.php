<?php

namespace Biig\Component\Domain\DataCollector;

use Biig\Component\Domain\Debug\TraceableDomainEventDispatcher;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

abstract class AbstractDomainEventDataCollector extends DataCollector implements LateDataCollectorInterface
{
    public const NAME = 'biig_domain.domain_event_data_collector';

    /**
     * @var TraceableDomainEventDispatcher
     */
    private $dispatcher;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Request|null
     */
    protected $currentRequest;

    /**
     * DomainEventDataCollector constructor.
     */
    public function __construct(TraceableDomainEventDispatcher $dispatcher, RequestStack $requestStack)
    {
        $this->dispatcher = $dispatcher;
        $this->requestStack = $requestStack;
    }

    public function getName()
    {
        return self::NAME;
    }

    public function reset()
    {
        $this->data = [];
        $this->dispatcher->reset();
    }

    public function lateCollect()
    {
        $calledListeners = $this->dispatcher->getCalledListeners($this->currentRequest);
        $calledDelayedListeners = $this->dispatcher->getDelayedListenersCalled();
        $notCalledListeners = $this->dispatcher->getNotCalledListeners($this->currentRequest);

        foreach ($this->dispatcher->getEventsFired() as $firedEvent) {
            $this->data['called_listeners'] = array_merge(
                $this->data['called_listeners'],
                $this->extractListeners($calledListeners, $firedEvent)
            );

            $this->data['called_delayed_listeners'] = array_merge(
                $this->data['called_delayed_listeners'],
                $this->extractListeners($calledDelayedListeners, $firedEvent)
            );

            $this->data['not_called_listeners'] = array_merge(
                $this->data['not_called_listeners'],
                $this->extractListeners($notCalledListeners, $firedEvent)
            );
        }

        $this->data = $this->cloneVar($this->data);
    }

    private function extractListeners(array $listeners, string $firedEvent): array
    {
        $filteredListeners = [];

        foreach ($listeners as $listener) {
            if (isset($listener['event']) && $firedEvent === $listener['event']) {
                $filteredListeners[] = $listener;
            }
        }

        return $filteredListeners;
    }

    /**
     * Sets the called listeners.
     *
     * @param array $listeners An array of called listeners
     *
     * @see TraceableEventDispatcher
     */
    public function setCalledListeners(array $listeners)
    {
        $this->data['called_listeners'] = $listeners;
    }

    /**
     * Gets the called listeners.
     *
     * @return array An array of called listeners
     *
     * @see TraceableEventDispatcher
     */
    public function getCalledListeners()
    {
        return $this->data['called_listeners'] ?? [];
    }

    /**
     * Gets the delayed called listeners.
     *
     * @return array
     */
    public function getCalledDelayedListeners()
    {
        return $this->data['called_delayed_listeners'] ?? [];
    }

    /**
     * Sets the not called listeners.
     *
     * @see TraceableEventDispatcher
     */
    public function setNotCalledListeners(array $listeners)
    {
        $this->data['not_called_listeners'] = $listeners;
    }

    /**
     * Gets the not called listeners.
     *
     * @return array
     *
     * @see TraceableEventDispatcher
     */
    public function getNotCalledListeners()
    {
        return $this->data['not_called_listeners'] ?? [];
    }
}

if (method_exists(TraceableEventDispatcher::class, 'preDispatch')) {
    class DomainEventDataCollector extends AbstractDomainEventDataCollector
    {
        // Compatibility layer with Sf 4.3 & 4.4
        public function collect(Request $request, Response $response /*, \Throwable $exception = null */)
        {
            $this->currentRequest = $this->requestStack->getMasterRequest() !== $request ? $request : null;
            $this->data = [
                'called_listeners' => [],
                'called_delayed_listeners' => [],
                'not_called_listeners' => [],
            ];
        }
    }
} else {
    class DomainEventDataCollector extends AbstractDomainEventDataCollector
    {
        public function collect(Request $request, Response $response, \Throwable $exception = null)
        {
            $this->currentRequest = $this->requestStack->getMasterRequest() !== $request ? $request : null;
            $this->data = [
                'called_listeners' => [],
                'called_delayed_listeners' => [],
                'not_called_listeners' => [],
            ];
        }
    }
}
