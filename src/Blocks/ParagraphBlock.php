<?php

namespace Asko\Sember\Blocks;

use Asko\Sember\Database;
use Asko\Sember\Helpers\ArrayHelper;
use Asko\Sember\Models\Post;
use Asko\Sember\Request;
use Asko\Sember\Response;

class ParagraphBlock implements Block
{
    // The name of the block.
    public string $name = 'Paragraph';

    // Is the block in beta?
    public bool $beta = true;

    // The icon of the block.
    public string $icon = 'fa-solid fa-paragraph';

    // Injected JS
    public array $js = [
        '/assets/admin/js/components/paragraph.js',
        '/assets/admin/js/blocks/paragraph.js'
    ];

    // Injected CSS
    public array $css = [
        '/assets/admin/css/blocks/paragraph.css'
    ];

    public static function editable(Post $post, array $block): string
    {
        ray($block);

        return (new Response)->view('admin/editor/blocks/paragraph', [
            'post' => $post->toArray(),
            'block' => $block,
        ])->send();
    }

    public static function update(Post $post, array $block): Response
    {
        $content = (new Request)->input('content');
        $blocks = json_decode($post->get('content'), true);
        $block_index = ArrayHelper::findIndex($blocks, fn($i) => $i['id'] === $block['id']);
        $block = $blocks[$block_index];
        $blocks[$block_index] = [...$block, 'value' => $content];
        $post->set('content', json_encode($blocks));

        (new Database)->update($post);

        return (new Response)->json([
            'status' => 'success',
            'message' => 'Block updated successfully.'
        ]);
    }

    private static function composeHtml(array $nodes, ?string $groupType = null): string
    {
        $html = "";

        foreach($nodes as $node) {
            $html .= match($node['type']) {
                'char' => $node['value'],
                'group' => self::composeHtml($node['content'], $node['groupType']),
            };
        }

        return match ($groupType) {
            'bold' => "<strong>$html</strong>",
            'italic' => "<em>$html</em>",
            'underline' => "<u>$html</u>",
            'strikethrough' => "<s>$html</s>",
            default => $html,
        };
    }

    public static function viewable(Post $post, array $block): string
    {
        $nodes = json_decode($block['value'], true) ?? [];
        $html = self::composeHtml($nodes);

        return <<<HTML
            <p>
                $html
            </p>
        HTML;
    }

    public static function model(): array
    {
        return [];
    }

    public static function options(Post $post, array $block): array
    {
        return [];
    }
}