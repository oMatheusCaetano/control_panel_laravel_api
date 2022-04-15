<?php

namespace App\Models;

use App\Utils\Date;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function setCreatedAtAttribute($createdAt)
    {
        $this->attributes[self::CREATED_AT] = Date::enWithTime($createdAt);
    }

    public function setUpdatedAtAttribute($updatedAt)
    {
        $this->attributes[self::UPDATED_AT] = Date::enWithTime($updatedAt);
    }

    public function getCreatedAtAttribute()
    {
        return Date::enWithTime($this->attributes[self::CREATED_AT]);
    }

    public function getUpdatedAtAttribute()
    {
        return Date::enWithTime($this->attributes[self::UPDATED_AT]);
    }
}
