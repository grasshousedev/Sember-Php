<?php

namespace Asko\Nth\Helpers;

use Asko\Nth\Config;
use Asko\Nth\Models\Model;
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
     * @param string $type
     * @return array
     */
    public static function new(string $type): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'type' => $type,
            'value' => '',
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
            $class = Config::getBlock($block['type'], 'markdown');

            return [
                ...$block,
                'render' => call_user_func([$class, 'editable'], $post, $block),
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
            $class = Config::getBlock($block['type'], 'markdown');

            return [
                ...$block,
                'render' => call_user_func([$class, 'viewable'], $post, $block),
            ];
        }, $blocks);
    }

    /**
     * Returns the block options.
     *
     * @return array
     */
    public static function opts(): array
    {
        $opts = [];

        foreach(Config::getBlocks() as $key => $block) {
            $opts[] = [
                'key' => $key,
                'name' => (new $block)->name,
            ];
        }

        return $opts;
    }
}