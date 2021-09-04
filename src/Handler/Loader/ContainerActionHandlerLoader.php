<?php

namespace Walnut\Lib\ActionBus\Handler\Loader;

use Psr\Container\ContainerInterface;
use Walnut\Lib\ActionBus\ActionHandler;
use Walnut\Lib\ActionBus\Handler\ActionHandlerNotFound;

final class ContainerActionHandlerLoader implements ActionHandlerLoader {
	public function __construct(
		private /*readonly*/ ContainerInterface $container
	) { }

	/**
	 * @param class-string<ActionHandler> $actionHandler
	 * @return ActionHandler
	 * @throws ActionHandlerNotFound
	 */
	public function loadActionHandler(string $actionHandler): ActionHandler {
		if ($this->container->has($actionHandler)) {
			$result = $this->container->get($actionHandler);
			if ($result instanceof ActionHandler) {
				return $result;
			}
			throw new ActionHandlerNotFound(
				sprintf("Unable to load invalid action handler %s", $actionHandler));
		}
		throw new ActionHandlerNotFound(
			sprintf("Unable to load action handler %s", $actionHandler));
	}
}
