<?php

namespace Walnut\Lib\ActionBus\Handler\Mapper;

use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

interface ActionHandlerMapper {
	/**
	 * @template RR of mixed
	 * @param Action<RR> $action
	 * @return class-string<ActionHandler<RR, Action>>
	 * @throws ActionHandlerNotFound
	 */
	public function getActionHandler(Action $action): string;
}
