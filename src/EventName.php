<?php

namespace Esports\DomainEvents;

use Doctrine\Common\EventArgs;

/**
 * Nazev udalosti
 */
class EventName {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param EventArgs $event
	 */
	public function __construct(EventArgs $event) {
		$this->name = $this->parseName(get_class($event));
	}

	public function __toString() {
		return $this->name;
	}

	/**
	 * @param string $class
	 * @return string
	 */
	private function parseName($class) {
		if (substr($class, -5) === "Event") {
			$class = substr($class, 0, -5);
		}

		if (strpos($class, "\\") === false) {
			return $class;
		}

		$parts = explode("\\", $class);
		return end($parts);
	}

}
