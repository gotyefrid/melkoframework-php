<?php

namespace src\models;

use core\Model;

class Click extends Model
{
    public $id = null;
    public $created_at = null;
    public $ban_reason = null;
    public $white_showed = null;
    public $user_agent = null;
    public $url = null;
    public $ip = null;
    public $hideclick_answer = null;

    public $attributes = [
        'id',
        'created_at',
        'ban_reason',
        'white_showed',
        'user_agent',
        'url',
        'ip',
        'hideclick_answer',
    ];

    public static function tableName(): string
    {
        return 'clicks';
    }

    public function validate(): bool
    {
       return true;
    }
}