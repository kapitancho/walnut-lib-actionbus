<?php

namespace Walnut\Lib\ActionBus\Handler\Mapper;

use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

final class ArrayActionHandlerMapper implements ActionHandlerMapper {
	/**
	 * @template RR of mixed
	 * @param array<class-string<Action<RR>>, class-string<ActionHandler<RR, Action>>> $actionMapping
	 */
	public function __construct(
		private /*readonly*/ array $actionMapping
	) {}

	public function getActionHandler(Action $action): string {
		return $this->actionMapping[$action::class] ??
			throw new ActionHandlerNotFound(
				sprintf(
					"No matching action handler found for action %s",
					$action::class
				)
			);
	}
}
