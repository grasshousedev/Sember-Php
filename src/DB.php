<?php

namespace Asko\Sember;

use Asko\Sember\Helpers\ArrayHelper;
use Asko\Sember\Models\Model;
use Crell\Serde\SerdeCommon;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * DB class handles all database operations such as
 * creating, querying, updating and deleting records. It
 * also manages the cache to achieve better performance which is
 * crucial for scaling a flat-file database.
 *
 * All data entries correspond to Model classes. This means that
 * you can only create, query, update and delete records that
 * correspond to a Model class. This is to ensure that all data
 * entries are valid and that the database is consistent.
 *
 * Model classes are serialized and deserialized using the
 * `Crell\Serde` library into and from JSON. This means that
 * all Model classes must have a `storage_name` property
 * that corresponds to the storage directory name that the
 * records are kept in.
 *
 * @package Asko\Nth
 * @since 0.1.0
 */
class DB
{
    /**
     * Creates a record from given `$model`.
     * Returns a unique identifier.
     *
     * @param Model $model
     * @return string|null
     */
    public static function create(Model $model): ?string
    {
        try {
            $id = Uuid::uuid4()->toString();
            $model->data['id'] = $id;
            $storage_name = $model->getStorageName();
            $dir_path = NTH_ROOT . "/storage/data/{$storage_name}";
            $storage_path = NTH_ROOT . "/storage/data/{$storage_name}/{$id}.json";

            if (!is_dir($dir_path) && !mkdir($dir_path, recursive: true)) {
                throw new Exception("Could not create storage directory {$dir_path}.");
            }

            self::validatePermissions($dir_path);

            $serde = new SerdeCommon();
            $data = $serde->serialize($model, format: 'json');

            file_put_contents($storage_path, $data);
            self::updateCacheForModel($model);

            return $id;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Updates a record corresponding to `$model`.
     * Requires the `$model` to have an `id` that matches
     * an ID in storage.
     *
     * @param Model $model
     * @return void
     */
    public static function update(Model $model): void
    {
        try {
            $id = $model->get('id');
            $storage_name = $model->getStorageName();
            $storage_path = NTH_ROOT . "/storage/data/{$storage_name}/{$id}.json";

            self::validatePermissions($storage_path);

            $serde = new SerdeCommon();
            $data = $serde->serialize($model, format: 'json');

            file_put_contents($storage_path, $data);

            self::updateCacheForModel($model);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Deletes a record corresponding to `$model`.
     *
     * @param Model $model
     * @return void
     */
    public static function delete(Model $model): void
    {
        try {
            $id = $model->get('id');
            $storage_name = $model->getStorageName();
            $storage_path = NTH_ROOT . "/storage/data/{$storage_name}/{$id}.json";

            self::validatePermissions($storage_path);

            unlink($storage_path);

            self::updateCacheForModel($model, action: 'delete');
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Attempts to find the `$model` corresponding to `$query`.
     *
     * @param string $model
     * @param array $query
     * @return Model|null
     */
    public static function find(string $model, array $query): ?Model
    {
        try {
            if ($data = self::findInCache($model, $query)) {
                return $data;
            }

            if ($data = self::findInStorage($model, $query)) {
                return $data;
            }

            return null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Attempts to find all `$model`s corresponding to `$query`.
     *
     * @param string $model
     * @param array $query
     * @return Collection|null
     */
    public static function findAll(string $model, array $query = []): ?Collection
    {
        try {
            if ($data = self::findAllInCache($model, $query)) {
                return $data;
            }

            if ($data = self::findAllInStorage($model, $query)) {
                return $data;
            }

            return new Collection([]);
        } catch(Exception $e) {
            error_log($e->getMessage());
            return new Collection([]);
        }
    }

    /**
     * Attempts to find all `$model`s corresponding to `$query` in cache.
     *
     * @param string $model
     * @param array $query
     * @return array|null
     * @throws Exception
     */
    private static function findAllInCache(string $model, array $query = []): ?Collection
    {
        $storage_name = (new $model)->getStorageName();
        $storage_path = NTH_ROOT . "/storage/cache/{$storage_name}.json";

        if (!file_exists($storage_path)) {
            return null;
        }

        self::validatePermissions($storage_path);

        if ($raw_cache = file_get_contents($storage_path)) {
            $cache = json_decode($raw_cache, true);
            $found_items = self::findItemsByQuery($cache, $query);

            if (empty($found_items)) {
                return null;
            }

            return new Collection(array_map(function($item) use ($model, $storage_name) {
                $serde = new SerdeCommon();
                $deserialized_model = $serde->deserialize($item, from: 'array', to: $model);
                $deserialized_model->setStorageName($storage_name);
                $deserialized_model->set('id', $item['id']);

                return $deserialized_model;
            }, $found_items));
        }

        return null;
    }

    /**
     * Attempts to find all `$model`s corresponding to `$query` in storage.
     *
     * @param string $model
     * @param array $query
     * @return array|null
     * @throws Exception
     */
    private static function findAllInStorage(string $model, array $query = []): ?Collection
    {
        $storage_name = (new $model)->getStorageName();
        $storage_path = NTH_ROOT ."/storage/data/{$storage_name}";

        if (!is_dir($storage_path)) {
            return null;
        }

        $files = scandir($storage_path);
        $deserialized_models = [];

        foreach ($files as $file) {
            if ($m = self::getModelFromFile($storage_name, $storage_path, $file, $model, $query)) {
                $deserialized_models[] = $m;
            }
        }

        self::updateCacheForModels($deserialized_models);

        return new Collection($deserialized_models);
    }

    /**
     * Attempts to find the `$model` corresponding to `$query` in cache.
     *
     * @param string $model
     * @param array $query
     * @return Model|null
     * @throws Exception
     */
    private static function findInCache(string $model, array $query): ?Model
    {
        $storage_name = (new $model)->getStorageName();
        $storage_path = NTH_ROOT . "/storage/cache/{$storage_name}.json";

        if (!file_exists($storage_path)) {
            return null;
        }

        self::validatePermissions($storage_path);

        if ($raw_cache = file_get_contents($storage_path)) {
            $cache = json_decode($raw_cache, true);
            $found_items = self::findItemsByQuery($cache, $query);

            if (empty($found_items)) {
                return null;
            }

            $serde = new SerdeCommon();
            $first_key = array_key_first($found_items);
            $deserialized_model = $serde->deserialize($found_items[$first_key], from: 'array', to: $model);
            $deserialized_model->setStorageName($storage_name);

            return $deserialized_model;
        }

        return null;
    }

    /**
     * Attempts to find the `$model` corresponding to `$query` in storage.
     * If found, updates cache, because for us to have to find something in
     * storage means it does not exist in cache.
     *
     * @param string $model
     * @param array $query
     * @return Model|null
     * @throws Exception
     */
    private static function findInStorage(string $model, array $query): ?Model
    {
        $storage_name = (new $model)->getStorageName();
        $storage_path = NTH_ROOT ."/storage/data/{$storage_name}";

        if (!is_dir($storage_path)) {
            return null;
        }

        $files = scandir($storage_path);

        foreach ($files as $file) {
            if ($m = self::getModelFromFile($storage_name, $storage_path, $file, $model, $query)) {
                self::updateCacheForModel($m);

                return $m;
            }
        }

        return null;
    }

    /**
     * Gets `$model` from `$file` if it matches `$query`.
     *
     * @param string $storage_name
     * @param string $storage_path
     * @param string $file
     * @param string $model
     * @param array $query
     * @return Model|null
     */
    private static function getModelFromFile(
        string $storage_name,
        string $storage_path,
        string $file,
        string $model,
        array $query
    ): ?Model
    {
        // Skip if not a JSON file.
        if (!str_ends_with($file, '.json')) {
            return null;
        }

        // If we're searching by ID and the file name does not match the ID, skip.
        if (array_key_exists('id', $query) && $file !== "{$query['id']}.json") {
            return null;
        }

        $raw_data = file_get_contents( "{$storage_path}/{$file}");
        $data = json_decode($raw_data, true);

        if (!self::itemMatchesQuery($data, $query)) {
            return null;
        }

        $serde = new SerdeCommon();
        $deserialized_model = $serde->deserialize($data, from: 'array', to: $model);
        $deserialized_model->setStorageName($storage_name);
        $deserialized_model->set('id', $data['id']);

        return $deserialized_model;
    }

    /**
     * Checks if `$item` matches `$query`.
     *
     * @param array $item
     * @param array $query
     * @return bool
     */
    private static function itemMatchesQuery(array $item, array $query): bool
    {
        foreach ($query as $key => $value) {
            // Fn predicate check
            if (is_callable($value)) {
                if (!$value($item[$key] ?? null)) {
                    return false;
                }

                continue;
            }

            // Equality check
            if (isset($item[$key]) && $item[$key] !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Finds items in `$items` that match `$query`.
     *
     * @param array $items
     * @param array $query
     * @return array
     */
    private static function findItemsByQuery(array $items, array $query): array
    {
        return array_filter($items, function ($item) use ($query) {
            return self::itemMatchesQuery($item, $query);
        });
    }

    /**
     * Updates cache for `$model`.
     *
     * @param Model $model
     * @return void
     * @throws Exception
     */
    private static function updateCacheForModel(Model $model, string $action = 'update'): void
    {
        $storage_name = $model->getStorageName();
        $storage_dir = NTH_ROOT . "/storage/cache";
        $storage_path = NTH_ROOT . "/storage/cache/{$storage_name}.json";

        if (!is_dir($storage_dir) && !mkdir($storage_dir, recursive: true)) {
            throw new Exception("Could not create storage directory {$storage_dir}.");
        }

        self::validatePermissions($storage_dir);

        $serde = new SerdeCommon();
        $data = $serde->serialize($model, format: 'array');

        // If cache does not exist and we're deleting, we don't need to do anything.
        if (!file_exists($storage_path) && $action === 'delete') {
            return;
        }

        // No cache yet, let's create it.
        if (!file_exists($storage_path)) {
            file_put_contents($storage_path, json_encode([$data]));

            return;
        }

        // Cache exists, let's update it.
        $raw_cache = file_get_contents($storage_path);
        $cache = json_decode($raw_cache, true);

        // If the item is not in cache, and we're updating, let's add it.
        if ($action === 'update' && empty(self::findItemsByQuery($cache, ['id' => $data['id']]))) {
            file_put_contents($storage_path, json_encode([...$cache, $data]));

            return;
        }

        // If the item is in cache, and we're updating, let's update it.
        $index = ArrayHelper::findIndex($cache, fn ($item) => $item['id'] === $data['id']);

        if ($index !== false && $action === 'update') {
            $cache[$index] = $data;
        }

        // If the item is in cache, and we're deleting, let's remove it.
        if ($index !== false && $action === 'delete') {
            array_splice($cache, $index, 1);
        }

        file_put_contents($storage_path, json_encode($cache));
    }

    /**
     * Updates cache for `$models`.
     *
     * @param array $models
     * @return void
     * @throws Exception
     */
    private static function updateCacheForModels(array $models): void
    {
        if (empty($models)) {
            return;
        }

        $storage_name = $models[0]->getStorageName();
        $storage_dir = NTH_ROOT . "/storage/cache";
        $storage_path = NTH_ROOT . "/storage/cache/{$storage_name}.json";

        self::validatePermissions($storage_dir);

        $serde = new SerdeCommon();
        $data = array_map(fn ($model) => $serde->serialize($model, format: 'array'), $models);

        file_put_contents($storage_path, json_encode($data));
    }

    /**
     * Validates that `$path` has the correct permissions.
     *
     * @throws Exception
     */
    private static function validatePermissions(string $path): void
    {
        if (!is_writable($path) || !is_readable($path)) {
            throw new Exception("Storage path {$path} does not have the correct permissions.");
        }
    }
}