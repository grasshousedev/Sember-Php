<?php

namespace Asko\Nth\Helpers;

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
}