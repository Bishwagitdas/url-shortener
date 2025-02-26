<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortenedUrl;
use App\Models\ClickLog;

class UrlShortenerController extends Controller
{
    public function index()
    {
        $urls = ShortenedUrl::orderBy('created_at', 'desc')->paginate(10);
        return view('home', compact('urls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:2048'
        ]);

        // Blacklist domains
        $blacklistedDomains = ['example.com', 'malicious-site.com'];
        $url = parse_url($request->url, PHP_URL_HOST);

        if (in_array($url, $blacklistedDomains)) {
            return redirect()->back()->with('error', 'This URL belongs to a blacklisted domain.');
        }

        // Check if the URL is already shortened
        $existingUrl = ShortenedUrl::where('original_url', $request->url)->first();

        if ($existingUrl) {
            return redirect()->back()->with('error', 'This URL has already been shortened.');
        }

        try {
            // Generate or retrieve the shortened URL
            $shortened = ShortenedUrl::generateOrRetrieve($request->url);
            return redirect()->back()->with('success', 'URL shortened successfully! Shortened URL: ' . url($shortened->short_code));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to shorten the URL. Please try again.');
        }
    }

    public function redirect($code)
    {

        $shortened = ShortenedUrl::where('short_code', $code)->first();

        if (!$shortened) {
            return redirect()->route('url.index')->with('error', 'Link not found.');
        }

        if ($shortened->isExpired()) {
            $perPage = 10;
            $position = ShortenedUrl::orderBy('created_at', 'desc')->pluck('id')->search($shortened->id) + 1;
            // Calculate the page number
            $currentPage = ceil($position / $perPage);

            return redirect()->route('url.index', ['page' => $currentPage])->with('error', 'This link has expired.');
        }

        // Increment clicks and log click
        $shortened->incrementClicks();
        ClickLog::logClick($shortened->id);

        return redirect()->away($shortened->original_url);
    }

    public function analytics($code)
    {
        $shortened = ShortenedUrl::findByCode($code);
        $clicks = ClickLog::getClicksByShortUrlId($shortened->id);
        return view('analytics', compact('shortened', 'clicks'));
    }

    public function destroy($id)
    {
        try {
            $shortenedUrl = ShortenedUrl::find($id);

            if (!$shortenedUrl) {
                return redirect()->back()->with('error', 'Shortened URL not found.');
            }

            // Delete the shortened URL
            $shortenedUrl->delete();

            return redirect()->back()->with('success', 'Shortened URL deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting shortened URL: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the URL.');
        }
    }
}
