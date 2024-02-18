<?php

namespace Asko\Sember;

abstract class BaseMigration
{
    public function __construct(protected Database $db)
    {
    }

    abstract public function up(): void;

    abstract public function down(): void;
}