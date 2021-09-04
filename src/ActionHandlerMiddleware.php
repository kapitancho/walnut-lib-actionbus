<?php

namespace Walnut\Lib\ActionBus;

interface ActionHandlerMiddleware {
	public function process(Action $action, ActionHandler $actionHandler): mixed;
}
