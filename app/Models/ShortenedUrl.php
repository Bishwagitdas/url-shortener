<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShortenedUrl extends Model
{
    use HasFactory;

    protected $fillable = ['original_url', 'short_code', 'clicks', 'expires_at'];

    public static function generateOrRetrieve($url)
    {
        $existing = self::where('original_url', $url)->first();
        return $existing ?: self::create(self::prepareShortUrlData($url));
    }

    private static function generateUniqueShortCode()
    {
        do {
            $shortCode = Str::random(6);
        } while (self::where('short_code', $shortCode)->exists());

        return $shortCode;
    }


    public static function findByCode($code)
    {
        return self::where('short_code', $code)->first();
    }

    public function incrementClicks()
    {
        $this->increment('clicks');
    }

    public function isExpired()
    {

     $expiresAt = Carbon::parse($this->expires_at);
     return now()->greaterThan($expiresAt);

    }

    public static function deleteById($id)
    {
        $shortened = self::find($id);
        return $shortened ? $shortened->delete() : false;
    }

    public static function prepareShortUrlData($url)
    {
        return [
            'original_url' => $url,
            'short_code' => self::generateUniqueShortCode(),
            'clicks' => 0,
            'expires_at' => Carbon::now('UTC')->addDays(30),
        ];
    }
}
