<?php

namespace Sember\System\Migrations;

use Sember\System\BaseMigration;

class AddTypeColumnToPostsTable extends BaseMigration
{
    public function up(): void
    {
        $this->db->exec('alter table posts add column type varchar(255) not null default "post"');
    }

    public function down(): void
    {
        $this->db->exec('alter table posts drop column type');
    }
}