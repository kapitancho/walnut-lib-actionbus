<?php

namespace Walnut\Lib\ActionBus;

/**
 * @template S of mixed
 */
final class ActionSuccess implements ActionResult {
	/**
	 * @param S $value
	 */
	public function __construct(
		public readonly mixed $value = null,
		public readonly array|object|null $actionEvents = null,
	) {}

	public function isSuccessful(): bool {
		return true;
	}
	public function isError(): bool {
		return false;
	}

	/**
	 * @return S
	 */
	public function toValue(): mixed {
		return $this->value;
	}
}
