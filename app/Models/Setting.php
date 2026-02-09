<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value', 'type'];

    public static function getValue(string $key, $default = null)
    {
        $s = static::where('key', $key)->first();
        if (! $s) return $default;
        return $s->value;
    }

    public static function setValue(string $key, $value, string $type = 'string')
    {
        return static::updateOrCreate(['key' => $key], ['value' => (string)$value, 'type' => $type]);
    }
}
