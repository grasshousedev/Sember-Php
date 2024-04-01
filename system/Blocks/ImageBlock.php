<?php

namespace Sember\System\Blocks;

use Sember\System\Database;
use Sember\System\Helpers\ArrayHelper;
use Sember\System\Helpers\BlockHelper;
use Sember\System\Models\Post;
use Sember\System\Request;
use Sember\System\Response;

/**
 * The image block.
 *
 * @since 0.1.0
 */
class ImageBlock implements Block
{
    // The name of the block.
    public string $name = 'Image';

    // The icon of the block.
    public string $icon = 'fa-solid fa-image';

    // Injected JS
    public array $js = [
        '/assets/admin/js/blocks/image.js'
    ];

    // Injected CSS
    public array $css = [
        '/assets/admin/css/blocks/image.css'
    ];

    /**
     * Returns the data model for the heading block.
     *
     * @return string[]
     */
    public static function model(): array
    {
        return [
            'src' => null,
            'alt' => ''
        ];
    }

    /**
     * Returns the options for the Markdown block.
     *
     * @param Post $post
     * @param array $block
     * @return array[]
     */
    public static function options(Post $post, array $block): array
    {
        return [];
    }

    /**
     * Uploads an image.
     *
     * @param Post $post
     * @param array $block
     * @return Response
     */
    public static function upload(Post $post, array $block): Response
    {
        $file = $_FILES['file'];
        $date = date('Y/m/d');
        $storage_dir = NTH_ROOT . "/storage/files/{$date}";

        try {
            if (!is_dir($storage_dir)) {
                mkdir($storage_dir, 0777, true);
            }

            move_uploaded_file($file['tmp_name'], "{$storage_dir}/{$file['name']}");
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return (new Response)->make($e->getMessage());
        }

        $blocks = json_decode($post->get('content'), true);
        $block_index = ArrayHelper::findIndex($blocks, fn($i) => $i['id'] === $block['id']);
        $block = $blocks[$block_index];
        $blocks[$block_index] = [...$block, 'src' => "{$date}/{$file['name']}"];
        $post->set('content', json_encode($blocks));

        (new Database)->update($post);

        return (new Response)->view('admin/editor/blocks', [
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
        ]);
    }

    /**
     * Returns the editable Markdown block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function editable(Post $post, array $block): string
    {
        return (new Response)->systemView('blocks/image_editable', [
            'post' => $post->toArray(),
            'block' => $block,
        ])->send();
    }

    /**
     * Returns the viewable Markdown block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function viewable(Post $post, array $block): string
    {
        $request = new Request;
        $url = $request->protocol() . '://' . $request->hostname() . '/files/' . $block['src'];

        return <<<HTML
            <picture style="--img-path: url('{$url}');" data-url="{$url}">
                <img src="/files/{$block['src']}" alt="{$block['alt']}">
            </picture>
        HTML;

    }
}