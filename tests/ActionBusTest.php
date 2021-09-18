<?php
use PHPUnit\Framework\TestCase;
use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionBus;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\ActionHandlerMiddleware;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

final class ActionBusAction implements Action {}
final class ActionBusInvalidAction implements Action {}
final class ActionBusActionHandler implements ActionHandler {
	public function execute(Action $action): string {
		return 'TEST-OK';
	}
}

final class ActionBusTest extends TestCase {
	private function getDefaultActionHandler(): ActionHandler {
		return new class implements ActionHandler {
			public function execute(Action $action): string {
				return $action instanceof ActionBusAction ? 'TEST-OK' :
					throw new ActionHandlerNotFound;
			}
		};
	}
	private function getActionBus(): ActionBus {
		return new ActionBus(
			$this->getDefaultActionHandler(),
			[]
		);
	}

	public function testOk(): void {
		$this->assertEquals(
			'TEST-OK',
			$this->getActionBus()->execute(
				new ActionBusAction
			)
		);
	}

	public function testNotFound(): void {
		$this->expectException(ActionHandlerNotFound::class);
		$this->getActionBus()->execute(
			new ActionBusInvalidAction
		);
	}

	public function testMiddleware(): void {
		$this->assertEquals(
			'TEST-OK-MW2-MW1',
			(new ActionBus(
				$this->getDefaultActionHandler(),
				[
					new class implements ActionHandlerMiddleware {
						public function process(Action $action, ActionHandler $actionHandler): string {
							return $actionHandler->execute($action) . '-MW1';
						}
					},
					new class implements ActionHandlerMiddleware {
						public function process(Action $action, ActionHandler $actionHandler): string {
							return $actionHandler->execute($action) . '-MW2';
						}
					}
				]
			))->execute(
				new ActionBusAction
			)
		);
	}

}
