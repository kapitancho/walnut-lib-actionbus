<?php

namespace Walnut\Lib\ActionBus;

use Throwable;

interface ActionResult {
	public function isSuccessful(): bool;
	public function isError(): bool;
	/**
	 * @return mixed
	 * @throws Throwable
	 */
	public function toValue(): mixed;
}
