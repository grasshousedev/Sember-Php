<?php

namespace Asko\Sember\Helpers;

use Asko\Sember\Config;
use Asko\Sember\Models\Model;
use Ramsey\Uuid\Uuid;

/**
 * The block helper.
 *
 * @package Asko\Nth\Helpers
 * @since 0.1.0
 */
class BlockHelper
{
    /**
     * Returns a new block.
     *
     * @param string $key
     * @return array
     */
    public static function new(string $key): array
    {
        $class = Config::getBlock($key);

        var_dump($key);
        var_dump($class);

        return [
            'id' => Uuid::uuid4()->toString(),
            'key' => $key,
            'value' => '',
            ...call_user_func([$class, 'model']),
        ];
    }

    /**
     * Returns the editable blocks.
     *
     * @param Model $post
     * @return array
     */
    public static function editableBlocks(Model $post): array
    {
        return array_map(function ($block) use($post) {
            $class = Config::getBlock($block['key'], 'markdown');

            return [
                ...$block,
                'render' => call_user_func([$class, 'editable'], $post, $block),
                'options' => call_user_func([$class, 'options'], $post, $block),
            ];
        }, $post->get('content'));
    }

    /**
     * Returns the viewable blocks.
     *
     * @param Model $post
     * @param array $blocks
     * @return array
     */
    public static function viewableBlocks(Model $post, array $blocks): array
    {
        return array_map(function ($block) use($post) {
            $class = Config::getBlock($block['key'], 'markdown');

            return [
                ...$block,
                'render' => call_user_func([$class, 'viewable'], $post, $block),
            ];
        }, $blocks);
    }

    /**
     * Returns the block list.
     *
     * @return array
     */
    public static function list(): array
    {
        $opts = [];

        foreach(Config::getBlocks() as $key => $block) {
            $instance = new $block;

            $opts[] = [
                'key' => $key,
                'name' => $instance->name,
                'icon' => $instance->icon,
            ];
        }

        return $opts;
    }
}