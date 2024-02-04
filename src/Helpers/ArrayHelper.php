<?php

namespace Asko\Nth\Helpers;

/**
 * The array helper.
 *
 * @package Asko\Nth\Helpers
 * @since 0.1.0
 */
class ArrayHelper
{
    /**
     * Find the first item in an array that passes a truth test.
     *
     * @param array $items
     * @param callable $predicate_fn
     * @return int|false
     */
    public static function findIndex(array $items, callable $predicate_fn): int|false
    {
        foreach ($items as $i => $item) {
            if (call_user_func($predicate_fn, $item)) {
                return $i;
            }
        }

        return false;
    }

    /**
     * Insert an item before the first item in an array that passes a truth test.
     * If no item passes the test, the item will not be inserted.
     *
     * @param array $items
     * @param callable $predicate_fn
     * @param mixed $item
     * @return array
     */
    public static function insertBefore(array $items, callable $predicate_fn, mixed $item): array
    {
        $index = self::findIndex($items, $predicate_fn);

        if ($index === false) {
            return $items;
        }

        array_splice($items, $index, 0, [$item]);

        return $items;
    }

    /**
     * Insert an item after the first item in an array that passes a truth test.
     * If no item passes the test, the item will not be inserted.
     *
     * @param array $items
     * @param callable $predicate_fn
     * @param mixed $item
     * @return array
     */
    public static function insertAfter(array $items, callable $predicate_fn, mixed $item): array
    {
        $index = self::findIndex($items, $predicate_fn);

        if ($index === false) {
            return $items;
        }

        array_splice($items, $index + 1, 0, [$item]);

        return $items;
    }
}