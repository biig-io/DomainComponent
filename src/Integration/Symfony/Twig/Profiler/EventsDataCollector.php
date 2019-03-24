<?php

namespace Biig\Component\Domain\Integration\Symfony\Twig\Profiler;

use Biig\Component\Domain\Event\DomainEventDispatcherTracer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class EventsDataCollector extends DataCollector
{
    /** @var DomainEventDispatcherTracer */
    private $traceableDomainDispatcher;

    public function __construct(DomainEventDispatcherTracer $traceableDomainDispatcher)
    {
        $this->traceableDomainDispatcher = $traceableDomainDispatcher;
    }

    /**
     * Collects data for the given Request and Response.
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $data = [];

        foreach ($this->traceableDomainDispatcher->getEventsFired() as $firedEvent) {
            $listenersCalled = [];
            $listeners = $this->traceableDomainDispatcher->getListeners($firedEvent);
            dump($listeners);
            dump($firedEvent);
            $eventsTracking = [
                'listenersCalled'    => [],
                'listenersNotCalled' => [],
            ];

            foreach ($this->traceableDomainDispatcher->getCalledListeners() as $calledListener) {
                dump($calledListener);
                if ($firedEvent === $calledListener['event']) {
                    $eventsTracking['listenersCalled'][] = $calledListener;
                }
            }

            foreach ($this->traceableDomainDispatcher->getNotCalledListeners() as $notCalledListener) {
                dump($notCalledListener);
                if ($firedEvent === $notCalledListener['event']) {
                    $eventsTracking['listenersNotCalled'][] = $notCalledListener;
                }
            }


            $data[$firedEvent] = $eventsTracking;
        }
    }


    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'biig.domain_events';
    }

    public function reset()
    {
        $this->data = [];
    }

    public function getEventsPretty()
    {
        return array_map(function($data) {
            return $data['pretty'];
        }, $this->data);
    }

    private function getDecorated()
    {
        return $this->traceableDomainDispatcher->getDecoratedDispatcher();
    }
}
