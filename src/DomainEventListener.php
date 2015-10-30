<?php

namespace Esports\DomainEvents;

use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

class DomainEventListener {

	/**
	 * @var EventManager
	 */
	private $eventManager;

	/**
	 * @var LifecycleEventArgs[]
	 */
	private $entities = [];

	/**
	 * @param EventManager $eventManager
	 */
	function __construct(EventManager $eventManager) {
		$this->eventManager = $eventManager;
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function postPersist(LifecycleEventArgs $event) {
		$this->keepDomainEvents($event);
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function postUpdate(LifecycleEventArgs $event) {
		$this->keepDomainEvents($event);
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function postRemove(LifecycleEventArgs $event) {
		$this->keepDomainEvents($event);
	}

	/**
	 * @param PostFlushEventArgs $event
	 */
	public function postFlush(PostFlushEventArgs $event) {
		foreach ($this->entities as $entity) {
			foreach ($entity->popEvents() as $entityEvent) {
				$name = new EventName($entityEvent);
				$this->eventManager->dispatchEvent("on" . (string) $name, $entityEvent);
			}
		}

		$this->entities = [];
	}

	/**
	 * @param LifecycleEventArgs $event
	 * @return void
	 */
	private function keepDomainEvents(LifecycleEventArgs $event) {
		$entity = $event->getObject();

		if (!($entity instanceof DomainEventing)) {
			return;
		}

		$this->entities[] = $entity;
	}

}
