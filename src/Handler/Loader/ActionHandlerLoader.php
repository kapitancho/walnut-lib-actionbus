<?php

namespace Walnut\Lib\ActionBus\Handler\Loader;

use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

interface ActionHandlerLoader {
	/**
	 * @param class-string<ActionHandler> $actionHandler
	 * @return ActionHandler
	 * @throws ActionHandlerNotFound
	 */
	public function loadActionHandler(string $actionHandler): ActionHandler;
}
