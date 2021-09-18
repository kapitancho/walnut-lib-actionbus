<?php

namespace Walnut\Lib\ActionBus;

use Throwable;

/**
 * @template S of mixed
 * @template E of Throwable
 */
final class ActionResult {
	/**
	 * @param S $value
	 * @param E|null $error
	 */
	private function __construct(
		public /*readonly*/ mixed $value,
		public /*readonly*/ ?Throwable $error
	) {}

	public function isSuccessful(): bool {
		return !isset($this->error);
	}
	public function isError(): bool {
		return isset($this->error);
	}

	/**
	 * @template SS of mixed
	 * @template EE of Throwable
	 * @param SS $result
	 * @return ActionResult<SS, EE>
	 */
	public static function ok(mixed $result = null): self {
		/**
		 * @var ActionResult<SS, EE>
		 */
		return new self($result, null);
	}
	/**
	 * @template SS of mixed
	 * @template EE of Throwable
	 * @param EE $error
	 * @return ActionResult<SS, EE>
	 */
	public static function error(Throwable $error): self {
		/**
		 * @var ActionResult<SS, EE>
		 */
		return new self(null, $error);
	}

	/**
	 * @return S
	 * @throws Throwable
	 */
	public function toValue(): mixed {
		return $this->error ? throw $this->error : $this->value;
	}
}
