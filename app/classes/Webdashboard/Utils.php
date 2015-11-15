<?php
namespace Webdashboard;

/**
 * Utils class
 *
 * Collection of static methods (query parameters, get population coverage)
 *
 *
 * @package Webdashboard
 */
class Utils
{
    /**
     * Read GET parameter if set, or fallback
     *
     * @param string $param    GET parameter to check
     * @param string $fallback Optional fallback value
     *
     * @return string Parameter value, or fallback
     */
    public static function getQueryParam($param, $fallback = '')
    {
        if (isset($_GET[$param])) {
            return is_bool($fallback)
                   ? true
                   : self::secureText($_GET[$param]);
        }

        return $fallback;
    }

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

    /**
     * Function sanitizing a string or an array of strings.
     *
     * @param mixed   $origin  String/Array of strings to sanitize
     * @param boolean $isarray If $origin must be treated as array
     *
     * @return mixed Sanitized string or array
     */
    public static function secureText($origin, $isarray = true)
    {
        if (! is_array($origin)) {
            // If $origin is a string, always return a string
            $origin  = [$origin];
            $isarray = false;
        }

        foreach ($origin as $item => $value) {
            // CRLF XSS
            $item  = str_replace('%0D', '', $item);
            $item  = str_replace('%0A', '', $item);
            $value = str_replace('%0D', '', $value);
            $value = str_replace('%0A', '', $value);

            $value = filter_var(
                $value,
                FILTER_SANITIZE_STRING,
                FILTER_FLAG_STRIP_LOW
            );

            $item  = htmlspecialchars(strip_tags($item), ENT_QUOTES);
            $value = htmlspecialchars(strip_tags($value), ENT_QUOTES);

            // Repopulate value
            $sanitized[$item] = $value;
        }

        return ($isarray == true) ? $sanitized : $sanitized[0];
    }

    /**
     * Return the English plural form for provided count+sentence
     *
     * @param int    $count    Number of items
     * @param string $sentence Sentence associated to count
     *
     * @return string String "count sentence" with proper plural form
     */
    public static function getPluralForm($count, $sentence)
    {
        return $count == 1
            ? "{$count} {$sentence}"
            : "{$count} {$sentence}s";
    }
}
