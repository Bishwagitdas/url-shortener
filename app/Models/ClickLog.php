<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickLog extends Model
{
    use HasFactory;

    protected $fillable = ['short_url_id', 'ip_address', 'user_agent'];

    public static function logClick($shortUrlId)
    {
        return self::create(self::prepareClickData($shortUrlId));
    }

    public static function getClicksByShortUrlId($shortUrlId)
    {
        return self::where('short_url_id', $shortUrlId)->latest()->get();
    }

    public static function prepareClickData($shortUrlId)
    {
        return [
            'short_url_id' => $shortUrlId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
    }
}
