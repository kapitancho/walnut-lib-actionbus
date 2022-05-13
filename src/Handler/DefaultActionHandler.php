<?php

namespace Walnut\Lib\ActionBus\Handler;

use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\Handler\Loader\ActionHandlerLoader;
use Walnut\Lib\ActionBus\Handler\Mapper\ActionHandlerMapper;

/**
 * @implements ActionHandler<mixed, Action>
 */
final class DefaultActionHandler implements ActionHandler {
	/**
	 * @param ActionHandlerMapper $actionHandlerMapper
	 * @param ActionHandlerLoader $actionHandlerLoader
	 */
	public function __construct(
		private readonly ActionHandlerMapper $actionHandlerMapper,
		private readonly ActionHandlerLoader $actionHandlerLoader
	) { }

	/**
	 * @template RR of mixed
	 * @param Action<RR> $action
	 * @return ActionHandler<RR, Action>
	 * @throws ActionHandlerNotFound
	 */
	private function getActionHandler(Action $action): ActionHandler {
		return $this->actionHandlerLoader->loadActionHandler(
			$this->actionHandlerMapper->getActionHandler($action)
		);
	}

	/**
	 * @template RR of mixed
	 * @param Action<RR> $action
	 * @return RR
	 * @throws ActionHandlerNotFound
	 */
	public function execute(Action $action): mixed {
		return $this->getActionHandler($action)->execute(
			$action
		);
	}
}
