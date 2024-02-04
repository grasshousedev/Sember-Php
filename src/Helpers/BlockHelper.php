<?php

namespace Asko\Nth\Helpers;

use Asko\Nth\Config;
use Asko\Nth\Models\Model;
use Ramsey\Uuid\Uuid;

class BlockHelper
{
    public static function new(string $type): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'type' => $type,
            'value' => '',
        ];
    }

    public static function editableBlocks(Model $post, array $blocks): array
    {
        return array_map(function ($block) use($post) {
            $class = Config::getBlock($block['type'], 'markdown');

            return [
                ...$block,
                'render' => call_user_func([$class, 'editable'], $post, $block),
            ];
        }, $blocks);
    }

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