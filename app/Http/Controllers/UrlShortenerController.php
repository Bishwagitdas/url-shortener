<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ShortenedUrl;
use App\Models\ClickLog;
use Carbon\Carbon;

class UrlShortenerController extends Controller
{
    public function index()
    {
        $urls = ShortenedUrl::orderBy('created_at', 'desc')->get();
        return view('home', compact('urls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:2048',
        ]);

        // Check if the URL already exists
        $existing = ShortenedUrl::where('original_url', $request->url)->first();
        if ($existing) {
            return redirect()->back()->with('short_url', url($existing->short_code));
        }

        // Generate unique short code
        do {
            $shortCode = Str::random(6);
        } while (ShortenedUrl::where('short_code', $shortCode)->exists());

        // Create shortened URL entry
        $shortened = ShortenedUrl::create([
            'original_url' => $request->url,
            'short_code' => $shortCode,
            'clicks' => 0,
            'expires_at' => Carbon::now()->addDays(30), // 30 days expiration
        ]);

        return redirect()->back()->with('short_url', url($shortened->short_code));
    }

    public function redirect($code)
    {
        $shortened = ShortenedUrl::where('short_code', $code)->firstOrFail();

        // Handle expired URL
        if ($shortened->expires_at && now()->greaterThan($shortened->expires_at)) {
            return response()->view('expired', ['code' => $code], 410);
        }

        // Increment click count
        $shortened->increment('clicks');

        // Store analytics
        ClickLog::create([
            'short_url_id' => $shortened->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->away($shortened->original_url);
    }

    public function analytics($code)
    {
        $shortened = ShortenedUrl::where('short_code', $code)->firstOrFail();
        $clicks = ClickLog::where('short_url_id', $shortened->id)->latest()->get();

        return view('analytics', compact('shortened', 'clicks'));
    }

    public function destroy($id)
    {
    $shortened = ShortenedUrl::findOrFail($id);
    $shortened->delete();

    return redirect()->back()->with('success', 'Shortened URL deleted successfully.');
    }
}

