<?php
namespace Webdashboard;

/**
 * Utils class
 *
 * Collection of static methods (cache, get population coverage)
 *
 *
 * @package Webdashboard
 */
class Utils {
    /**
     * Cache a remote resource in the cache folder for 120 seconds
     *
     * @param   string  $url         Content to fetch
     * @param   string  $time        Seconds to cache (default 120)
     * @param   string  $cache_path  Path to cache, default set to global
     *                               constant CACHE
     *
     * @return  string               URI of the cached resource, or original
     *                               source if not available
     */
    public static function cacheUrl($url, $time = 120, $cache_path = CACHE)
    {
        $cached_file = $cache_path . sha1($url) . '.cache';

        if (is_file($cached_file)) {
            $age = $_SERVER['REQUEST_TIME'] - filemtime($cached_file);

            if ($age < $time) {

                return $cached_file;
            }
        }

        // Only fetch external data if we can write to Cache folder
        if (is_dir($cache_path)) {
            $file = file_get_contents($url);
            file_put_contents($cached_file, $file);

            return $cached_file;
        }

        // No caching possible, return $url
        return $url;
    }

    /**
     * Given a set of locales, determine l10n user population coverage
     *
     * @param  array   $locales          Array of locale codes
     * @param  string  $langchecker_url  Base URL for langchecker
     *
     * @return string                    A percent value of our coverage for
     *                                   the user base
     */

    public static function getUserBaseCoverage($locales, $langchecker_url)
    {
        $url = "{$langchecker_url}?action=coverage&json&locales[]=" .
               implode('&locales[]=', array_map('urlencode', $locales));

        return file_get_contents($url);
    }
}
