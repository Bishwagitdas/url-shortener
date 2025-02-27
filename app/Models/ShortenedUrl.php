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

    const DEFAULT_CLICKS = 0;
    const EXPIRATION_DAYS = 30;

    public static function createOrRetrieveShortUrl($url)
    {
       return self::firstOrCreate(['original_url' => $url], self::prepareShortUrlData($url));
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

    public static function prepareShortUrlData($url)
    {
        return [
            'original_url' => $url,
            'short_code' => self::generateUniqueShortCode(),
            'clicks' => self::DEFAULT_CLICKS,
            'expires_at' => Carbon::now()->addDays(self::EXPIRATION_DAYS),
        ];
    }
}
