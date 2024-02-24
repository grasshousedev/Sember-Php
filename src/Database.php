<?php

namespace Asko\Sember;

use Asko\Sember\Collections\MigrationCollection;
use Asko\Sember\Collections\PostCollection;
use Asko\Sember\Models\Migration;
use Asko\Sember\Models\Model;
use Asko\Sember\Models\Post;
use Exception;
use PDO;

/**
 * Database class.
 *
 * @package Asko\Sember
 * @since 0.1.0
 */
class Database
{
    protected PDO $instance;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        match (Config::get('database_driver')) {
            'sqlite' => $this->init_sqlite(),
            'mysql' => $this->init_mysql(),
            'pgsql' => $this->init_pgsql(),
            default => throw new Exception('Unsupported database driver.'),
        };
    }

    /**
     * Initialize SQLite database.
     *
     * @return void
     */
    protected function init_sqlite(): void
    {
        if (!is_dir(dirname(Config::get('sqlite_path')))) {
            mkdir(dirname(Config::get('sqlite_path')), 0755, true);
        }

        $this->instance = new PDO('sqlite:' . Config::get('sqlite_path'));
        $this->maybeSetupTables();
    }

    /**
     * Initialize MySQL database.
     *
     * @return void
     */
    protected function init_mysql(): void
    {
        $this->instance = new PDO(
            'mysql:host=' . Config::get('mysql_hostname') . ';dbname=' . Config::get('mysql_database'),
            Config::get('mysql_username'),
            Config::get('mysql_password')
        );

        $this->maybeSetupTables();
    }

    /**
     * Initialize PostgreSQL database.
     *
     * @return void
     */
    protected function init_pgsql(): void
    {
        $this->instance = new PDO(
            'pgsql:host=' . Config::get('pgsql_hostname') . ';dbname=' . Config::get('pgsql_database'),
            Config::get('pgsql_username'),
            Config::get('pgsql_password'),
        );

        $this->maybeSetupTables();
    }

    /**
     * Create tables if they don't exist.
     *
     * @return void
     */
    protected function maybeSetupTables(): void
    {
        // create users table
        $this->instance->exec('CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL,
            auth_token TEXT,
            created_at INTEGER NOT NULL,
            updated_at INTEGER NOT NULL
        )');

        // create posts table
        $this->instance->exec('CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT NOT NULL,
            content TEXT NOT NULL,
            user_id INTEGER NOT NULL,
            status TEXT NOT NULL,
            created_at INTEGER NOT NULL,
            updated_at INTEGER NOT NULL,
            published_at INTEGER,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )');

        // create meta table
        $this->instance->exec('CREATE TABLE IF NOT EXISTS meta (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            meta_name TEXT NOT NULL,
            meta_value TEXT NOT NULL,
            created_at INTEGER NOT NULL,
            updated_at INTEGER NOT NULL
        )');

        // create migrations table
        $this->instance->exec('CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration TEXT NOT NULL,
            created_at INTEGER NOT NULL
        )');
    }

    /**
     * Find models.
     *
     * @param string $model
     * @param string $query
     * @param array $data
     * @return Collection|PostCollection|MigrationCollection
     */
    public function find(string $model, string $query = '', array $data = []): Collection|PostCollection|MigrationCollection
    {
        $storage_name = (new $model)->getStorageName();
        $stmt = $this->instance->prepare("SELECT * FROM {$storage_name} {$query}");
        $stmt->execute($data);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $items = array_map(fn($i) => new $model($i), $results);

        return match ($model) {
            Post::class => new PostCollection(...$items),
            Migration::class => new MigrationCollection(...$items),
            default => new Collection($items),
        };
    }

    /**
     * Find one model.
     *
     * @param string $model
     * @param string $query
     * @param array $data
     * @return ?Model
     */
    public function findOne(string $model, string $query, array $data = []): ?Model
    {
        $storage_name = (new $model)->getStorageName();
        $stmt = $this->instance->prepare("SELECT * FROM {$storage_name} {$query}");
        $stmt->execute($data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return new $model($result);
    }

    /**
     * Create a model.
     *
     * @param Model $model
     * @return string|int|false
     */
    public function create(Model $model): string|int|false
    {
        $storage_name = $model->getStorageName();
        $fields = implode(', ', array_keys($model->toArray()));
        $placeholders = implode(', ', array_fill(0, count($model->toArray()), '?'));
        $values = array_values($model->toArray());
        $stmt = $this->instance->prepare("INSERT INTO {$storage_name} ({$fields}) VALUES ({$placeholders})");
        $stmt->execute($values);
        $id = $this->instance->lastInsertId();

        return is_numeric($id) ? (int)$id : $id;
    }

    /**
     * Update a model.
     *
     * @param Model $model
     * @return void
     */
    public function update(Model $model): void
    {
        $storage_name = $model->getStorageName();
        $fields = implode(' = ?, ', array_keys($model->toArray())) . ' = ?';
        $values = array_values($model->toArray());
        $stmt = $this->instance->prepare("UPDATE {$storage_name} SET {$fields} WHERE id = ?");
        $stmt->execute([...$values, $model->get('id')]);
    }

    /**
     * Delete a model.
     *
     * @param Model $model
     * @return void
     */
    public function delete(Model $model): void
    {
        $storage_name = $model->getStorageName();
        $stmt = $this->instance->prepare("DELETE FROM {$storage_name} WHERE id = ?");
        $stmt->execute([$model->get('id')]);
    }

    /**
     * Run a raw fetch query.
     *
     * @param string $query
     * @param array $data
     * @return mixed
     */
    public function raw(string $query, array $data = []): mixed
    {
        $stmt = $this->instance->prepare($query);
        $stmt->execute($data);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Run a raw query.
     *
     * @param string $query
     * @return void
     */
    public function exec(string $query): void
    {
        $this->instance->exec($query);
    }
}