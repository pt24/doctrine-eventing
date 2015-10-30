<?php

namespace Test;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Esports\DomainEvents\DomainEventing;
use Esports\DomainEvents\DomainEventListener;
use Mockery;
use Tester;
use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class EventListenerTest extends Tester\TestCase {

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var EventManager
	 */
	private $eventManager;

	/**
	 * @var string
	 */
	private $eventMessage;

	/**
	 * @var bool
	 */
	private $changeEventCalled = false;

	public function setUp() {
		$eventManager = new EventManager;
		$eventManager->addEventListener(['onObjectCreated', 'onObjectChanged'], $this);

		$entityManager = Mockery::mock('\Doctrine\ORM\EntityManager');

		$this->eventManager = $eventManager;
		$this->entityManager = $entityManager;
		$this->eventMessage = NULL;
		$this->changeEventCalled = false;
	}

	public function testOnPersistEvents() {
		$expectedValue = "Test object created";

		$domainObject = new DomainObject($expectedValue);
		$domainObject->setSomething();

		$lifecycleEventArgs = $this->createLifecycleEventArgs($domainObject);
		$postFlushEventArgs = $this->createPostFlushEventArgs();

		$eventListener = $this->createDomainEventListener();
		$eventListener->postPersist($lifecycleEventArgs);
		$eventListener->postFlush($postFlushEventArgs);

		Assert::same($expectedValue, $this->eventMessage);
		Assert::true($this->changeEventCalled);
	}

	public function testOnUpdateEvents() {
		$expectedValue = "Test object updated";

		$domainObject = new DomainObject($expectedValue);

		$lifecycleEventArgs = $this->createLifecycleEventArgs($domainObject);
		$postFlushEventArgs = $this->createPostFlushEventArgs();

		$eventListener = $this->createDomainEventListener();
		$eventListener->postUpdate($lifecycleEventArgs);
		$eventListener->postFlush($postFlushEventArgs);

		Assert::same($expectedValue, $this->eventMessage);
		Assert::false($this->changeEventCalled);
	}

	public function testOnRemoveEvents() {
		$expectedValue = "Test object removed";

		$domainObject = new DomainObject($expectedValue);

		$lifecycleEventArgs = $this->createLifecycleEventArgs($domainObject);
		$postFlushEventArgs = $this->createPostFlushEventArgs();

		$eventListener = $this->createDomainEventListener();
		$eventListener->postRemove($lifecycleEventArgs);
		$eventListener->postFlush($postFlushEventArgs);

		Assert::same($expectedValue, $this->eventMessage);
		Assert::false($this->changeEventCalled);
	}

	public function onObjectCreated(ObjectCreatedEvent $event) {
		$this->eventMessage = $event->getMessage();
	}

	public function onObjectChanged(ObjectChanged $event) {
		$this->changeEventCalled = true;
	}

	/**
	 * @param DomainObject $domainObject
	 * @return LifecycleEventArgs
	 */
	private function createLifecycleEventArgs(DomainObject $domainObject) {
		$lifecycleEventArgs = Mockery::mock('\Doctrine\Common\Persistence\Event\LifecycleEventArgs');
		$lifecycleEventArgs->shouldReceive('getObject')
				->andReturn($domainObject);

		return $lifecycleEventArgs;
	}

	/**
	 * @return PostFlushEventArgs
	 */
	private function createPostFlushEventArgs() {
		return new PostFlushEventArgs($this->entityManager);
	}

	/**
	 * @return DomainEventListener
	 */
	private function createDomainEventListener() {
		return new DomainEventListener($this->eventManager);
	}

}

/**
 * Domenovy objekt
 */
class DomainObject implements DomainEventing {

	use \Esports\DomainEvents\EventProvider;

	public function __construct($message) {
		$this->raise(new ObjectCreatedEvent($message));
	}

	public function setSomething() {
		$this->raise(new ObjectChanged);
	}

}

/**
 * Domenovy event signalizujici vytvoreni objektu
 */
class ObjectCreatedEvent extends EventArgs {

	/** @var string */
	private $message;

	function __construct($message) {
		$this->message = $message;
	}

	function getMessage() {
		return $this->message;
	}

}

/**
 * Domenovy event signalizujici zmenu
 */
class ObjectChanged extends EventArgs {

}

$test = new EventListenerTest();
$test->run();
