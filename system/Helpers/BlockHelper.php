<?php

namespace Sember\System\Helpers;

use Sember\System\Config;
use Sember\System\Models\Model;
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
    public static function new(string $key, array $opts = []): array
    {
        $class = Config::getBlock($key);

        return [
            'id' => Uuid::uuid4()->toString(),
            'key' => $key,
            'value' => $opts['value'] ?? '',
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
        return array_map(function ($block) use ($post) {
            $class = Config::getBlock($block['key'], 'markdown');

            return [
                ...$block,
                'render' => call_user_func([$class, 'editable'], $post, $block),
                'options' => call_user_func([$class, 'options'], $post, $block),
            ];
        }, json_decode($post->get('content'), true));
    }

    /**
     * Returns the injected JS.
     *
     * @return array
     */
    public static function injectedJs(): array
    {
        $js = [];

        foreach (Config::getBlocks() as $block) {
            $js = array_merge($js, (new $block)->js);
        }

        return $js;
    }

    /**
     * Returns the injected CSS.
     *
     * @return array
     */
    public static function injectedCss(): array
    {
        $css = [];

        foreach (Config::getBlocks() as $block) {
            $css = array_merge($css, (new $block)->css);
        }

        return $css;
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
        return array_map(function ($block) use ($post) {
            $class = Config::getBlock($block['key']);

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

        foreach (Config::getBlocks() as $key => $block) {
            $instance = new $block;

            $opts[] = [
                'key' => $key,
                'name' => $instance->name,
                'icon' => $instance->icon,
                'beta' => $instance->beta ?? false,
            ];
        }

        return $opts;
    }
}