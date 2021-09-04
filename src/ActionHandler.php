<?php

namespace Walnut\Lib\ActionBus;

/**
 * @template R of mixed
 * @template C of Action<R>
 */
interface ActionHandler {
	/**
	 * @param C $action
	 * @return R
	 */
	public function execute(Action $action): mixed;
}
