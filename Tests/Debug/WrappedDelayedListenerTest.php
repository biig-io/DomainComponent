<?php

namespace Biig\Component\Domain\Tests\Debug;

use Biig\Component\Domain\Debug\WrappedDelayedListener;
use Biig\Component\Domain\Event\DelayedListener;
use PHPUnit\Framework\TestCase;

class WrappedDelayedListenerTest extends TestCase
{
    public function testItCanInstantiateWrappedDelayedListener()
    {
        $callable = ['Biig\Component\Domain\Tests\Debug\FakeCalleable', 'execute'];
        $delayedListener = new DelayedListener('eventTest', $callable);
        $wrappedDelayedListener = new WrappedDelayedListener($delayedListener);
        $this->assertInstanceOf(WrappedDelayedListener::class, $wrappedDelayedListener);
    }

    public function testItGetsInfo()
    {
        $callable = ['Biig\Component\Domain\Tests\Debug\FakeCalleable', 'execute'];
        $delayedListener = new DelayedListener('eventTest', $callable);

        $wrappedDelayedListener = new WrappedDelayedListener($delayedListener);
        $this->assertEquals(
            [
                'event' => 'eventTest',
                'priority' => 0,
                'pretty' => 'Biig\Component\Domain\Tests\Debug\FakeCalleable::execute',
                'stub' => 'Biig\Component\Domain\Tests\Debug\FakeCalleable::execute($someParameter)',
            ],
            $wrappedDelayedListener->getInfo('eventTest')
        );
    }


}

class FakeCalleable
{
    public function execute($someParameter)
    {}
}
