<?php

namespace Esports\DomainEvents;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;

class DomainEventSubscriber extends DomainEventListener implements EventSubscriber {

	/**
	 * @inheritdoc
	 */
	public function getSubscribedEvents() {
		return [
			Events::postUpdate,
			Events::postPersist,
			Events::postRemove,
			Events::postFlush
		];
	}

}
