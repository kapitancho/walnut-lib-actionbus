<?php
use PHPUnit\Framework\TestCase;
use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;
use Walnut\Lib\ActionBus\Handler\DefaultActionHandler;
use Walnut\Lib\ActionBus\Handler\Loader\ActionHandlerLoader;
use Walnut\Lib\ActionBus\Handler\Mapper\ActionHandlerMapper;

final class DefaultActionHandlerAction implements Action {}
final class DefaultActionHandlerInvalidAction implements Action {}
final class DefaultActionHandlerActionHandler implements ActionHandler {
	public function execute(Action $action): string {
		return 'TEST-OK';
	}
}

final class DefaultActionHandlerTest extends TestCase {
	private function getDefaultActionHandler(): DefaultActionHandler {
		return new DefaultActionHandler(
			new class implements ActionHandlerMapper {
				public function getActionHandler(Action $action): string {
					return $action instanceof DefaultActionHandlerAction ?
						DefaultActionHandlerActionHandler::class :
						throw new ActionHandlerNotFound;
				}
			},
			new class implements ActionHandlerLoader {
				public function loadActionHandler(string $actionHandler): ActionHandler {
					return new DefaultActionHandlerActionHandler;
				}
			}
		);
	}

	public function testOk(): void {
		$this->assertEquals(
			'TEST-OK',
			$this->getDefaultActionHandler()->execute(
				new DefaultActionHandlerAction
			)
		);
	}

	public function testNotFound(): void {
		$this->expectException(ActionHandlerNotFound::class);
		$this->getDefaultActionHandler()->execute(
			new DefaultActionHandlerInvalidAction
		);
	}

}