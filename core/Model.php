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

    /**
     * @param $conditions
     *
     * @return array|string
     */
    public static function findAll($conditions = []): array
    {
        $sql = 'SELECT * FROM ' . static::tableName();

        $params = [];
        if (!empty($conditions)) {
            if (is_array($conditions)) {
                $conditionsArray = [];

                foreach ($conditions as $column => $value) {
                    $conditionsArray[] = "$column = :$column";
                    $params[":$column"] = $value;
                }
                $sql .= ' WHERE ' . implode(' AND ', $conditionsArray);
            } else {
                $sql .= ' ' . $conditions;
            }
        }

        $stmt = Db::getConnection()->prepare($sql);
        $stmt->execute(is_array($conditions) ? $params : null);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $res = [];

        foreach ($results as $result) {
            $model = new static();

            foreach ($result as $column => $value) {
                $model->{$column} = $value;
            }

            $res[] = $model;
        }

        return $res;
    }

    /**
     * @param string|array $conditions
     *
     * @return Model|null
     */
    public static function findOne($conditions = []): ?Model
    {
        return self::findAll($conditions)[0] ?? null;
    }

    public static function findById(int $id): ?Model
    {
        return self::findAll(['id' => $id])[0] ?? null;
    }


    public function delete(): bool
    {
        $sql = 'DELETE FROM ' . static::tableName() . ' WHERE id = :id';
        $stmt = Db::getConnection()->prepare($sql);

        return $stmt->execute([':id' => $this->id]);
    }

    public function save(): bool
    {
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

        $stmt = Db::getConnection()->prepare($sql);

        // Привязка значений к подготовленному запросу
        foreach ($properties as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }

        if ($stmt->execute()) {
            if (!isset($properties['id']) || !$properties['id']) {
                $this->id = Db::getConnection()->lastInsertId(); // Присваивание ID, если это было создание новой записи
            }
            return true;
        }

        return false;
    }

    abstract public function validate(): bool;

    public function offsetExists($offset)
    {
        return property_exists($this, $offset) && $this->{$offset} !== null;
    }

    public function offsetGet($offset)
    {
        if (method_exists($this, 'get'. ucfirst($offset))) {
            $data = $this->{'get'. ucfirst($offset)}();
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