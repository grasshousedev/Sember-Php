<?php

namespace Sember\System;

use ArrayAccess;
use Override;

/**
 * A generic class for working with collections of items.
 * This class is heavily inspired by Laravel's Collection class.
 *
 * @template T
 * @package Asko\Nth
 * @since 0.1.0
 */
class Collection implements ArrayAccess
{
	/** @var array<T> */
	protected array $items;

	public function __construct(array $items)
	{
		$this->items = $items;
	}

	/**
	 * Create a new collection.
	 *
	 * @param callable $callback
	 * @return $this
	 */
	public function map(callable $callback): static
	{
		$this->items = array_map($callback, $this->items);

		return $this;
	}

	/**
	 * Filter the collection.
	 *
	 * @param callable $callback
	 * @return $this
	 */
	public function filter(callable $callback): static
	{
		$this->items = array_filter($this->items, $callback);

		return $this;
	}

	/**
	 * Filter the collection by a key/value pair.
	 *
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function where(string $key, $value): static
	{
		return $this->filter(function ($item) use ($key, $value) {
			// If `$item` has a getter, use that.
			if (!is_array($item) && method_exists($item, 'get')) {
				return $item->get($key) === $value;
			}

			return $item[$key] === $value;
		});
	}

	/**
	 * Filter the collection by a key/value pair.
	 *
	 * @param string $key
	 * @param $value
	 * @return $this
	 */
	public function whereNot(string $key, $value): static
	{
		return $this->filter(function ($item) use ($key, $value) {
			// If `$item` has a getter, use that.
			if (!is_array($item) && method_exists($item, 'get')) {
				return $item->get($key) === $value;
			}

			return $item[$key] !== $value;
		});
	}

	/**
	 * Sort the collection by a key and (optionally) direction.
	 *
	 * @param string $key
	 * @param string $direction
	 * @return $this
	 */
	public function orderBy(string $key, string $direction = 'asc'): static
	{
		usort($this->items, function ($a, $b) use ($key, $direction) {
			if (!is_array($a) && method_exists($a, 'get')) {
				$a = $a->get($key);
			} else {
				$a = $a[$key];
			}

			if (!is_array($b) && method_exists($b, 'get')) {
				$b = $b->get($key);
			} else {
				$b = $b[$key];
			}

			if ($direction === 'asc') {
				return $a <=> $b;
			}

			return $b <=> $a;
		});

		return $this;
	}

	/**
	 * Get the first item in the collection.
	 *
	 * @return T|null
	 */
	public function first(): mixed
	{
		return $this->items[0] ?? null;
	}

	/**
	 * Get the last item in the collection.
	 *
	 * @return T|null
	 */
	public function last(): mixed
	{
		return $this->items[count($this->items) - 1] ?? null;
	}

	public function limit(int $limit): static
	{
		$this->items = array_slice($this->items, 0, $limit);

		return $this;
	}

	public function offset(int $offset): static
	{
		$this->items = array_slice($this->items, $offset);

		return $this;
	}

	public function count(): int
	{
		return count($this->items);
	}

	/**
	 * Transform the collection into an array.
	 *
	 * @return array<T>
	 */
	public function toArray(): iterable
	{
		return $this->items;
	}

	#[Override]
	public function offsetExists(mixed $offset): bool
	{
		return isset($this->items[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return T|null
	 */
	#[Override]
	public function offsetGet(mixed $offset): mixed
	{
		return $this->items[$offset] ?? null;
	}

	#[Override]
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (is_null($offset)) {
			$this->items[] = $value;
		} else {
			$this->items[$offset] = $value;
		}
	}

	#[Override]
	public function offsetUnset(mixed $offset): void
	{
		unset($this->items[$offset]);
	}
}
