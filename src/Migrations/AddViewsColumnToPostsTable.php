<?php

namespace Asko\Sember\Migrations;

use Asko\Sember\BaseMigration;

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