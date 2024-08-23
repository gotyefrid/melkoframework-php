<?php

namespace core;

use PDO;

abstract class Model
{
    public $id;

    abstract public static function tableName(): string;

    public static function findAll(array $conditions = []): array
    {
        $sql = 'SELECT * FROM ' . static::tableName();

        $params = [];
        if (!empty($conditions)) {
            $conditionsArray = [];

            foreach ($conditions as $column => $value) {
                $conditionsArray[] = "$column = :$column";
                $params[":$column"] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $conditionsArray);
        }

        $stmt = Db::getConnection()->prepare($sql);
        $stmt->execute($params);
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

    public static function findOne(array $conditions = []): ?Model
    {
        return self::findAll($conditions)[0] ?? null;
    }

    public static function findById(int $id): ?Model
    {
        return self::findAll(['id' => $id])[0] ?? null;
    }

    public function save(): bool
    {
        $properties = get_object_vars($this);

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

}