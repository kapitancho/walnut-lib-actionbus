<?php
use PHPUnit\Framework\TestCase;
use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionBus;
use Walnut\Lib\ActionBus\ActionError;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\ActionHandlerMiddleware;
use Walnut\Lib\ActionBus\ActionResult;
use Walnut\Lib\ActionBus\ActionSuccess;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

final class ActionResultException extends \RuntimeException {}

final class ActionResultTest extends TestCase {
	public function testOk(): void {
		$this->assertEquals('value', (new ActionSuccess('value'))->toValue());
		$this->assertTrue((new ActionSuccess('value'))->isSuccessful());
		$this->assertFalse((new ActionSuccess('value'))->isError());
		$this->assertFalse((new ActionError(new ActionResultException))->isSuccessful());
		$this->assertTrue((new ActionError(new ActionResultException))->isError());
	}

	public function testError(): void {
		$this->expectException(ActionResultException::class);
		(new ActionError(new ActionResultException))->toValue();
	}
}