<?php

namespace Biig\Component\Domain\Tests\Event;

require_once __DIR__ . '/../fixtures/FakeModel.php';

use Biig\Component\Domain\Event\DelayedListener;
use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\DomainModel;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Biig\Component\Domain\Rule\PostPersistDomainRuleInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;

class DelayedListenerTest extends TestCase
{
    public function testICanInstantiateDelayedListener()
    {
        $delayedListener = new DelayedListener('foo', function () {});
        $this->assertInstanceOf(DelayedListener::class, $delayedListener);
    }

    public function testItProcessEventOnlyOneTime()
    {
        $count = 0;
        $delayedListener = new DelayedListener('foo', function () use (&$count) {
            ++$count;
        });

        $fakeModel = new FakeDomainModel();
        $delayedListener->occur(new DomainEvent($fakeModel));
        $delayedListener->occur(new DomainEvent($fakeModel));
        $this->assertTrue($delayedListener->shouldOccur($fakeModel));

        $delayedListener->process($fakeModel);
        $this->assertFalse($delayedListener->shouldOccur($fakeModel));
        $this->assertEquals(2, $count);

        $delayedListener->process($fakeModel);
        $this->assertEquals(2, $count);
    }

    /**
     * @expectedException \Biig\Component\Domain\Exception\InvalidDomainEvent
     */
    public function testItFailsToRegisterOtherThanCurrentModel()
    {
        $model = new class() {
            public $foo;
        };

        $listener = new DelayedListener('foo', function () {});
        $listener->occur(new DomainEvent($model));
    }

    public function testItInsertInBddAfterFlushing()
    {
        $tmpPath = \sys_get_temp_dir() . '/testItInsertInBddAfterFlushing.' . \microtime() . '.sqlite';
        copy(__DIR__ . '/../fixtures/dbtest/fake_model.db', $tmpPath);

        $config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . '/../fixtures/config'), true);
        $config->setClassMetadataFactoryName(ClassMetadataFactory::class);
        $conn = [
            'driver' => 'pdo_sqlite',
            'path' => $tmpPath,
        ];
        $entityManager = EntityManager::create($conn, $config);

        $model = new \FakeModel();
        $model->setFoo('Model 1');
        $dispatcher = new DomainEventDispatcher();
        $model->setDispatcher($dispatcher);

        $entityManager->getMetadataFactory()->setDispatcher($dispatcher);
        $rule = new class($entityManager) implements PostPersistDomainRuleInterface {
            private $entityManager;

            public function __construct(EntityManager $entityManager)
            {
                $this->entityManager = $entityManager;
            }

            public function after()
            {
                return [\FakeModel::class => 'action'];
            }

            public function execute(\Biig\Component\Domain\Event\DomainEvent $event)
            {
                $model = new \FakeModel();
                $model->setFoo('RulePostPersist');
                $this->entityManager->persist($model);
                $this->entityManager->flush($model);
            }
        };
        $dispatcher->addRule($rule);

        $entityManager->persist($model);
        $entityManager->flush($model);
        $model->doAction();
        $dispatcher->persistModel($model);

        $this->assertEquals(count($entityManager->getRepository(\FakeModel::class)->findAll()), 3);
        @unlink($tmpPath);
    }
}

class FakeDomainModel extends DomainModel
{
}
