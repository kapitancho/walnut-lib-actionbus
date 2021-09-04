<?php
use PHPUnit\Framework\TestCase;
use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;
use Walnut\Lib\ActionBus\Handler\Mapper\ArrayActionHandlerMapper;

final class ArrayActionHandlerMapperAction implements Action {}
final class ArrayActionHandlerMapperUnmappedAction implements Action {}
final class ArrayActionHandlerMapperActionHandler {}

final class ArrayActionHandlerMapperTest extends TestCase {
	private function getActionHandlerMapper(): ArrayActionHandlerMapper {
		return new ArrayActionHandlerMapper([
			ArrayActionHandlerMapperAction::class =>
			ArrayActionHandlerMapperActionHandler::class
		]);
	}

	public function testOk(): void {
		$this->assertEquals(
			ArrayActionHandlerMapperActionHandler::class,
			$this->getActionHandlerMapper()->getActionHandler(
				new ArrayActionHandlerMapperAction
			)
		);
	}

	public function testNotFound(): void {
		$this->expectException(ActionHandlerNotFound::class);
		$this->getActionHandlerMapper()->getActionHandler(
			new ArrayActionHandlerMapperUnmappedAction
		);
	}

}