<?php

namespace Walnut\Lib\ActionBus;

use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

/**
 * @implements ActionHandler<mixed, Action>
 */
final class ActionBus implements ActionHandler {
	/**
	 * @var ActionHandlerMiddleware[]
	 */
	private readonly array $middlewares;
	/**
	 * @param ActionHandler $actionHandler
	 * @param ActionHandlerMiddleware[] $middlewares
	 */
	public function __construct(
		private readonly ActionHandler $actionHandler,
		array $middlewares = []
	) {
		$this->middlewares = array_values($middlewares);
	}

	/**
	 * @template RR of mixed
	 * @param Action<RR> $action
	 * @return RR
	 * @throws ActionHandlerNotFound
	 */
	public function execute(Action $action): mixed {
		if (!$this->middlewares) {
			return $this->actionHandler->execute($action);
		}
		$middleware = $this->middlewares[0];
		/**
		 * @var ActionHandler<RR, Action>
		 */
		$actionHandler = new self(
			$this->actionHandler,
			array_slice($this->middlewares, 1)
		);
		return $middleware->process($action, $actionHandler);
	}
}
