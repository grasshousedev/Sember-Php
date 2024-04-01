<?php

namespace Sember\System\Blocks;

use Sember\System\Models\Post;

interface Block
{
    public static function model(): array;

    public static function options(Post $post, array $block): array;

    public static function editable(Post $post, array $block): string;

    public static function viewable(Post $post, array $block): string;
}