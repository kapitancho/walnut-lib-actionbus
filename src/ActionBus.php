<?php

namespace Walnut\Lib\ActionBus;

use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

final class ActionBus implements ActionHandler {
	/**
	 * @param ActionHandler $actionHandler
	 * @param ActionHandlerMiddleware[] $middlewares
	 */
	public function __construct(
		private /*readonly*/ ActionHandler $actionHandler,
		private /*readonly*/ array $middlewares = []
	) {
		$this->middlewares = array_values($this->middlewares);
	}

	/**
	 * @psalm-suppress ImplementedParamTypeMismatch
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
		return $middleware->process($action, new self(
			$this->actionHandler,
			array_slice($this->middlewares, 1)
		));
	}
}
