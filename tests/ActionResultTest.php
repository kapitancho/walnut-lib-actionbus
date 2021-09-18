<?php
use PHPUnit\Framework\TestCase;
use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionBus;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\ActionHandlerMiddleware;
use Walnut\Lib\ActionBus\ActionResult;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

final class ActionResultException extends \RuntimeException {}

final class ActionResultTest extends TestCase {
	public function testOk(): void {
		$this->assertEquals('value', ActionResult::ok('value')->toValue());
		$this->assertTrue(ActionResult::ok('value')->isSuccessful());
		$this->assertFalse(ActionResult::ok('value')->isError());
		$this->assertFalse(ActionResult::error(new ActionResultException)->isSuccessful());
		$this->assertTrue(ActionResult::error(new ActionResultException)->isError());
	}

	public function testError(): void {
		$this->expectException(ActionResultException::class);
		ActionResult::error(new ActionResultException)->toValue();
	}
}