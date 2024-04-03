<?php

namespace Sember\System\Models;

use Sember\System\Config;
use Sember\System\Request;

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

    public static function findOne(string $query, array $params = []): ?static
    {
        $post = self::findOne($query, $params);

        // Increment view count for every query to this post for non-authenticated users
        if ($post && !User::current()) {
            $cookie_k = "has_read_" . $post->get("id");

            if (!(new Request)->cookie()->has($cookie_k)) {
                (new Request)->cookie()->set($cookie_k, "yes", time() + 60 * 60 * 24 * 30);
                $post->set("views", ($post->get("views") ?? 0) + 1);
                $post->update();
            }
        }

        return $post;
    }
}