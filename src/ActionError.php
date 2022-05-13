<?php

namespace Walnut\Lib\ActionBus;

use Throwable;

/**
 * @template E of Throwable
 */
final class ActionError implements ActionResult {
	/**
	 * @param E $error
	 */
	public function __construct(
		public readonly Throwable $error
	) {}

	public function isSuccessful(): bool {
		return false;
	}
	public function isError(): bool {
		return true;
	}

	/**
	 * @throws Throwable
	 */
	public function toValue(): mixed {
		throw $this->error;
	}
}
