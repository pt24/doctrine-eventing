<?php

namespace {
	use Doctrine\Common\EventArgs;

	require __DIR__ . '/../bootstrap.php';

	/**
	 * Event v korenovem namespace
	 */
	class RootNamespace extends EventArgs {
	}
}

namespace Test {
	use Doctrine\Common\EventArgs;
	use Esports\DomainEvents\EventName;
	use Tester\TestCase;
	use Tester\Assert;

	require __DIR__ . '/../bootstrap.php';

	class EventNameTest extends TestCase {

		function setUp() {

		}

		function testRootNamespace() {
			$eventName = new EventName(new \RootNamespace());
			Assert::same('RootNamespace', (string) $eventName);
		}

		function testCutNamespace() {
			$eventName = new EventName(new DomainRemove());
			Assert::same('DomainRemove', (string) $eventName);
		}

		function testCutEventSuffix() {
			$eventName = new EventName(new DomainUpdateEvent());
			Assert::same('DomainUpdate', (string) $eventName);
		}

	}

	class DomainUpdateEvent extends EventArgs {

	}

	class DomainRemove extends EventArgs {

	}

	$test = new EventNameTest();
	$test->run();
}

