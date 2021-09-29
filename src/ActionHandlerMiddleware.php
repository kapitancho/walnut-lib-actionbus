<?php

namespace Walnut\Lib\ActionBus;

interface ActionHandlerMiddleware {
	/**
	 * @template RZ
	 * @param Action<RZ> $action
	 * @param ActionHandler<RZ, Action> $actionHandler
	 * @return RZ
	 */
	public function process(Action $action, ActionHandler $actionHandler): mixed;
}
