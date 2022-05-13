<?php

namespace Walnut\Lib\ActionBus\Handler\Mapper;

use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

final class NameActionHandlerMapper implements ActionHandlerMapper {
	public function __construct(
		private readonly string $suffix = 'Handler'
	) {}

	/**
	 * @template RR of mixed
	 * @param Action<RR> $action
	 * @return class-string<ActionHandler<RR, Action>>
	 * @throws ActionHandlerNotFound
	 */
	public function getActionHandler(Action $action): string {
		/**
		 * @var class-string<ActionHandler<RR, Action>>
		 */
		return $action::class . $this->suffix;
	}
}
