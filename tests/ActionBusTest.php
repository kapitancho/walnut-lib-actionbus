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

/**
 * @implements Action<bool>
 */
final class AddProductAction implements Action {
	public function __construct(
		public /*readonly*/ string $productId,
		public /*readonly*/ int $amount
	) {}
}

/**
 * @implements ActionHandler<bool, AddProductAction>
 */
final class AddProductActionHandler implements ActionHandler {
	/**
	 * @param AddProductAction $action
	 * @return bool
	 */
	public function execute(Action $action): bool {
		return true; //@TODO - implement
	}
}

final class ProductData {
	public function __construct(
		public /*readonly*/ string $productId,
		public /*readonly*/ string $productName,
		public /*readonly*/ int $amount
	) {}
}

/**
 * @implements Action<ProductData>
 */
final class GetProductAction implements Action {
	public function __construct(
		public /*readonly*/ string $productId
	) {}
}

/**
 * @implements ActionHandler<ProductData, GetProductAction>
 */
final class GetProductActionHandler implements ActionHandler {
	/**
	 * @param GetProductAction $action
	 * @return bool
	 */
	public function execute(Action $action): ProductData {
		return new ProductData('p1', 'Test Product', 10); //@TODO - implement
	}
}

$defaultActionHandler = new \Walnut\Lib\ActionBus\Handler\DefaultActionHandler(
    new \Walnut\Lib\ActionBus\Handler\Mapper\ArrayActionHandlerMapper([
		AddProductAction::class => AddProductActionHandler::class,
		GetProductAction::class => GetProductActionHandler::class,
    ]),
	new \Walnut\Lib\ActionBus\Handler\Loader\ContainerActionHandlerLoader(getContainer())
);
$actionBus = new ActionBus($defaultActionHandler);
$result = $actionBus->execute(new AddProductAction('p2', 5));

$productName = $actionBus->execute(new GetProductAction('p2'))->productName;


$defaultActionHandler = new \Walnut\Lib\ActionBus\Handler\DefaultActionHandler(
    new \Walnut\Lib\ActionBus\Handler\Mapper\NameActionHandlerMapper(),
    new \Walnut\Lib\ActionBus\Handler\Loader\ContainerActionHandlerLoader(getContainer())
);

$actionBus = new ActionBus($defaultActionHandler);
$result = $actionBus->execute(new AddProductAction('p2', 5));

$productName = $actionBus->execute(new GetProductAction('p2'))->productName;


use Walnut\Lib\ActionBus\Handler\DefaultActionHandler;
use Walnut\Lib\ActionBus\Handler\Mapper\NameActionHandlerMapper;
use Walnut\Lib\ActionBus\Handler\Loader\ContainerActionHandlerLoader;

$defaultActionHandler = new DefaultActionHandler(
    new NameActionHandlerMapper(),
    new ContainerActionHandlerLoader(getPsrContainer())
);

final class TransactionMiddleware implements ActionHandlerMiddleware {

	public function __construct(
		private /*readonly*/ TransactionContext $transactionContext
	) {}

	/**
	 * @throws Exception
	 */
	public function process(Action $action, ActionHandler $actionHandler): mixed {
		try {
			$result = $actionHandler->execute($action);
			$this->transactionContext->saveChanges();
			return $result;
		} catch (Exception $e) {
			$this->transactionContext->revertChanges();
			throw $e;
		}
	}
}

final class ErrorLoggerMiddleware implements ActionHandlerMiddleware {

	public function __construct(
		private /*readonly*/ LoggerInterface $logger
	) {}

	/**
	 * @throws Exception
	 */
	public function process(Action $action, ActionHandler $actionHandler): mixed {
		try {
			return $actionHandler->execute($action);
		} catch (Exception $e) {
			$this->logger->error($e);
			throw $e;
		}
	}
}

$actionBus = new ActionBus($defaultActionHandler, [
	new TransactionMiddleware(getTransactionContext()),
	new ErrorLoggerMiddleware(getPsrLogger())
]);
$result = $actionBus->execute(new AddProductAction('p2', 5));

$productName = $actionBus->execute(new GetProductAction('p2'))->productName;
