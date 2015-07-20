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
class Utils
{
    /**
     * Given a set of locales, determine l10n user population coverage
     *
     * @param array  $locales         Array of locale codes
     * @param string $langchecker_url Base URL for langchecker
     *
     * @return string A percent value of our coverage for the user base
     */
    public static function getUserBaseCoverage($locales, $langchecker_url)
    {
        $url = "{$langchecker_url}?action=coverage&json&locales[]=" .
               implode('&locales[]=', array_map('urlencode', $locales));

        return file_get_contents($url);
    }
}
