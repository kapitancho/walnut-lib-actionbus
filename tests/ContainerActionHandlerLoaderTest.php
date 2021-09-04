<?php

use PHPUnit\Framework\TestCase;
use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;
use Walnut\Lib\ActionBus\Handler\Loader\ContainerActionHandlerLoader;

final class ContainerActionHandlerLoaderMissingActionHandler {}
final class ContainerActionHandlerLoaderInvalidActionHandler {}
final class ContainerActionHandlerLoaderActionHandler implements ActionHandler {
	public function execute(Action $action): string {
		return 'TEST';
	}
}

final class ContainerActionHandlerLoaderTest extends TestCase {
	private function getActionHandlerLoader(): ContainerActionHandlerLoader {
		return new ContainerActionHandlerLoader(new class implements Psr\Container\ContainerInterface {
			public function get(string $id): ContainerActionHandlerLoaderActionHandler|ContainerActionHandlerLoaderInvalidActionHandler|null {
				if ($id === ContainerActionHandlerLoaderActionHandler::class) {
					return new ContainerActionHandlerLoaderActionHandler;
				}
				if ($id === ContainerActionHandlerLoaderInvalidActionHandler::class) {
					return new ContainerActionHandlerLoaderInvalidActionHandler;
				}
				return null;
			}
			public function has(string $id): bool {
				return
					$id === ContainerActionHandlerLoaderActionHandler::class ||
					$id === ContainerActionHandlerLoaderInvalidActionHandler::class;
			}
		});
	}

	public function testOk(): void {
		$this->assertInstanceOf(
			ContainerActionHandlerLoaderActionHandler::class,
			$this->getActionHandlerLoader()->loadActionHandler(
				ContainerActionHandlerLoaderActionHandler::class
			)
		);
	}

	public function testNotFound(): void {
		$this->expectException(ActionHandlerNotFound::class);
		$this->getActionHandlerLoader()->loadActionHandler(
			ContainerActionHandlerLoaderInvalidActionHandler::class
		);
	}

	public function testNotValid(): void {
		$this->expectException(ActionHandlerNotFound::class);
		$this->getActionHandlerLoader()->loadActionHandler(
			ContainerActionHandlerLoaderMissingActionHandler::class
		);
	}

}