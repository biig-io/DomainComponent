<?php

namespace Biig\Component\Domain\Tests\Event;

require_once __DIR__ . '/../fixtures/FakeModel.php';

use Biig\Component\Domain\Event\DelayedListener;
use Biig\Component\Domain\Event\DomainEvent;
use Biig\Component\Domain\Event\DomainEventDispatcher;
use Biig\Component\Domain\Model\DomainModel;
use Biig\Component\Domain\Model\Instantiator\DoctrineConfig\ClassMetadataFactory;
use Biig\Component\Domain\PostPersistListener\DoctrinePostPersistListener;
use Biig\Component\Domain\Rule\PostPersistDomainRuleInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;

class DelayedListenerTest extends TestCase
{
    private $dbPath;

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
        $event = new DomainEvent($fakeModel);
        $delayedListener->occur($event);
        $delayedListener->occur(new DomainEvent($fakeModel));
        $this->assertTrue($delayedListener->shouldOccur($fakeModel));

        $delayedListener->process($fakeModel);
        $this->assertFalse($delayedListener->shouldOccur($fakeModel));
        $this->assertEquals(2, $count);

        $delayedListener->process($fakeModel);
        $this->assertEquals(2, $count);

        $this->assertTrue($event->isDelayed());
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
        $dispatcher = new DomainEventDispatcher();
        $entityManager = $this->setupDatabase($dispatcher);

        $model = new \FakeModel();
        $model->setFoo('Model1');
        $model->setDispatcher($dispatcher);

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

        $model->doAction();
        $entityManager->persist($model);
        $entityManager->flush($model);

        // 3 because the database was already containing 1 entry
        $this->assertEquals(3, count($entityManager->getRepository(\FakeModel::class)->findAll()));
        $this->dropDatabase();
    }

    public function testItDoesNotExecuteManyTimesSameEvent()
    {
        // Test setup
        $dispatcher = new DomainEventDispatcher();
        $entityManager = $this->setupDatabase($dispatcher);

        $model = new \FakeModel();
        $model->setFoo(0);
        $model->setDispatcher($dispatcher);

        $rule = new CountAndInsertRule($entityManager);
        $dispatcher->addRule($rule);

        // Test: the rule should be trigger 2 times
        $model->doAction();
        $model->doAction();
        $entityManager->persist($model);
        $entityManager->flush($model);

        $this->assertEquals(2, $model->getFoo());
        $this->dropDatabase();
    }

    private function setupDatabase(DomainEventDispatcher $dispatcher)
    {
        $this->dbPath = \sys_get_temp_dir() . '/testItInsertInBddAfterFlushing.' . \microtime() . '.sqlite';
        copy(__DIR__ . '/../fixtures/dbtest/fake_model.db', $this->dbPath);

        $config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . '/../fixtures/config'), true);
        $config->setClassMetadataFactoryName(ClassMetadataFactory::class);
        $conn = [
            'driver' => 'pdo_sqlite',
            'path' => $this->dbPath,
        ];

        $entityManager = EntityManager::create($conn, $config);
        $entityManager->getEventManager()->addEventSubscriber(new DoctrinePostPersistListener($dispatcher));

        $entityManager->getMetadataFactory()->setDispatcher($dispatcher);

        return $entityManager;
    }

    private function dropDatabase()
    {
        if (!$this->dbPath) {
            return;
        }

        @unlink($this->dbPath);
    }
}

class FakeDomainModel extends DomainModel
{
}

class CountAndInsertRule implements PostPersistDomainRuleInterface
{
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
        // Count times of execution
        $event->getSubject()->setFoo($event->getSubject()->getFoo() + 1);

        // Trigger flush
        $model = new \FakeModel();
        $model->setFoo('Something new to insert');
        $this->entityManager->persist($model);
        $this->entityManager->flush($model);
    }
}
