<?php

namespace Sember\System\Migrations;

use Sember\System\BaseMigration;

class AddViewsColumnToPostsTable extends BaseMigration
{
    public function up(): void
    {
        $this->db->exec('alter table posts add column views integer not null default 0');
    }

    public function down(): void
    {
        $this->db->exec('alter table posts drop column views');
    }
}