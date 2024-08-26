<?php

namespace core;

use PDO;

abstract class Model implements \ArrayAccess
{
    /**
     * @var int
     */
    public $id;

    public $attributes = [
        'id'
    ];

    /**
     * @var array
     */
    public $errors;

    abstract public static function tableName(): string;

    public static function findById(int $id): ?Model
    {
        $sql = 'SELECT * FROM users WHERE id = :id LIMIT 1';
        return self::find($sql, [':id' => $id])[0] ?? null;
    }

    /**
     * @param string $column
     * @param $value
     *
     * @return Model[]
     */
    public static function findByCondition(string $column, $value): array
    {
        $sql = "SELECT * FROM users WHERE $column = :$column";
        return self::find($sql, [":$column" => $value]);
    }


    public function delete(): bool
    {
        $sql = 'DELETE FROM ' . static::tableName() . ' WHERE id = :id';
        $stmt = Application::$app->db->prepare($sql);

        return $stmt->execute([':id' => $this->id]);
    }

    public function save(bool $runValidation = true): bool
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }

        $properties = get_object_vars($this);
        $properties = array_intersect_key($properties, array_flip($this->attributes));

        // Разделение свойств на колонки и их значения
        $columns = array_keys($properties);

        // Если у модели есть ID, обновляем запись
        if (isset($properties['id']) && $properties['id']) {
            $setClause = implode(
                ', ',
                array_map(
                    function ($col) {
                        return "$col = :$col";
                    },
                    $columns
                )
            );
            $sql = 'UPDATE ' . static::tableName() . ' SET ' . $setClause . ' WHERE id = :id';
        } else {
            // Иначе создаём новую запись
            $placeholders = implode(', ', array_map(function ($col) {
                return ":$col";
            }, $columns));
            $columnsList = implode(', ', $columns);
            $sql = 'INSERT INTO ' . static::tableName() . " ($columnsList) VALUES ($placeholders)";
        }

        $stmt = Application::$app->db->prepare($sql);

        // Привязка значений к подготовленному запросу
        foreach ($properties as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }

        if ($stmt->execute()) {
            if (!isset($properties['id']) || !$properties['id']) {
                $this->id = Application::$app->db->lastInsertId(); // Присваивание ID, если это было создание новой записи
            }
            return true;
        }

        return false;
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return Model[]
     */
    public static function find(string $sql, array $params = []): array
    {
        $stmt = Application::$app->db->prepare($sql);

        if ($params) {
            // Привязка значений к подготовленному запросу
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    abstract public function validate(): bool;

    public function offsetExists($offset)
    {
        return property_exists($this, $offset) && $this->{$offset} !== null;
    }

    public function offsetGet($offset)
    {
        if (method_exists($this, 'get' . ucfirst($offset))) {
            $data = $this->{'get' . ucfirst($offset)}();
        } else {
            $data = $this->{$offset};
        }

        return $this->offsetExists($offset) ? $data : null;
    }

    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            $this->{$offset} = $value;
        }
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->{$offset} = null;
        }
    }
}