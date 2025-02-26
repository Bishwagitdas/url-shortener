<?php

namespace App\Services;

class UrlValidationService
{
    /**
     * Validate and check if the URL is blacklisted.
     *
     * @param string $url
     * @param array $blacklistedDomains
     * @return string|null
     */
    public function validateUrl(string $url, array $blacklistedDomains): ?string
    {
        // Validate the URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return 'The URL is not valid.';
        }

        // Get the domain from the URL
        $domain = parse_url($url, PHP_URL_HOST);

        // Check if the domain is in the blacklist
        if (in_array($domain, $blacklistedDomains)) {
            return 'This URL belongs to a blacklisted domain.';
        }

        return null;
    }

    /**
     * Validate if the provided URL is valid.
     * Ensures it has a valid domain and TLD.
     *
     * @param string $url
     * @return bool
     */
    public function isValidUrl($url): bool
    {
        // Ensure the URL is valid
        if (!preg_match('/^https?:\/\//', $url)) {
            return false;
        }

        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['host']) || !filter_var($parsedUrl['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return false;
        }

        if (!preg_match('/\.[a-zA-Z]{2,}$/', $parsedUrl['host'])) {
            return false;
        }

        return true;
    }

    /**
     * Check if a URL is reachable (valid and alive).
     *
     * @param string $url
     * @return bool
     */
    public function isReachableUrl($url): bool
    {
        $headers = @get_headers($url);

        if (!$headers) {
            return false;
        }

        // Check if the response code is 200 OK
        $statusCode = substr($headers[0], 9, 3);

        return $statusCode === '200';
    }
}
