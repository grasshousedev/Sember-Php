<?php

namespace Sember\System\Models;

use Sember\System\Config;

/**
 * @extends Model<Post>
 */
class Post extends Model
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->storage_name = 'posts';
    }

    /**
     * Renders the HTML for the post.
     *
     * @return string
     */
    public function renderHtml(): string
    {
        $html = '';

        foreach (json_decode($this->data['content'], true) as $block) {
            $class = Config::getBlock($block['key']);
            $html .= call_user_func([$class, 'viewable'], $this, $block);
        }

        return $html;
    }
}