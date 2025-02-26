<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortenedUrl;
use App\Models\ClickLog;
use App\Services\UrlValidationService;
use Illuminate\Support\Facades\RateLimiter;


class UrlShortenerController extends Controller
{
    protected $urlValidationService;

    // Injecting the service into the controller
    public function __construct(UrlValidationService $urlValidationService)
    {
        $this->urlValidationService = $urlValidationService;
    }

    public function index()
    {
        $urls = ShortenedUrl::orderBy('created_at', 'desc')->paginate(10);
        return view('home', compact('urls'));
    }

    public function store(Request $request)
    {
        $ip = $request->ip();
        $key = 'url_shorten:' . $ip;

        // Check if the rate limit is exceeded (2 requests per minute)
        if (RateLimiter::remaining($key, 2) === 0) {
            return redirect()->back()->with('error', 'Please try again in 1 minute.');
        }

        // Increment the rate limit counter
        RateLimiter::hit($key, 60);

        // Validate the input URL
        $request->validate([
            'url' => 'required|url|max:2048'
        ]);

        // Check if the URL is a valid URL format and has a valid domain
        $url = $request->url;

         // additional validation on the URL
         if (!$this->urlValidationService->isValidUrl($url)) {
             return redirect()->back()->with('error', 'Please enter a valid URL.');
         }

        // Fetch the blacklisted domains from the config file
        $blacklistedDomains = config('blacklist.domains');

        // Check if the URL is valid and if it's blacklisted
        $validationResult = $this->urlValidationService->validateUrl($request->url, $blacklistedDomains);

        if ($validationResult) {
            return redirect()->back()->with('error', $validationResult);
        }

        // Additional check: Ensure the URL is reachable
        if (!$this->urlValidationService->isReachableUrl($url)) {
            return redirect()->back()->with('error', 'The URL is unreachable. Please check the URL and try again.');
        }

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
        // Find the shortened URL by the short code
        $shortened = ShortenedUrl::where('short_code', $code)->first();

        if (!$shortened) {
            return redirect()->route('url.index')->with('error', 'Link not found.');
        }

        // Check if the shortened URL has expired
        if ($shortened->isExpired()) {
            $perPage = 10;
            $position = ShortenedUrl::orderBy('created_at', 'desc')->pluck('id')->search($shortened->id) + 1;

            $currentPage = ceil($position / $perPage);

            return redirect()->route('url.index', ['page' => $currentPage])->with('error', 'This link has expired.');
        }

        $shortened->incrementClicks();
        ClickLog::logClick($shortened->id);

        return redirect()->away($shortened->original_url);
    }

    public function analytics($code)
    {
        $shortened = ShortenedUrl::findByCode($code);

        if (empty($shortened)) {
            return redirect()->route('url.index')->with('error', 'Shortened URL not found.');
        }

        $urls = ClickLog::where('short_url_id', $shortened->id)->orderBy('created_at', 'desc')->paginate(10);
        $totalClicks = $urls->total();

        return view('analytics', compact('shortened', 'totalClicks', 'urls'));
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
