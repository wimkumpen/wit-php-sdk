<?php
namespace Wit\Url;

/**
 * Class WitUrlManipulator
 *
 * @package Wit
 */
class WitUrlManipulator
{
    /**
     * Gracefully appends params to the URL.
     *
     * @param string $url       The URL that will receive the params.
     * @param array  $newParams The params to append to the URL.
     *
     * @return string
     */
    public static function appendParamsToUrl($url, array $newParams = [])
    {
        if (empty($newParams)) {
            return $url;
        }

        if (strpos($url, '?') === false) {
            return $url . '?' . http_build_query($newParams, null, '&');
        }

        list($path, $query) = explode('?', $url, 2);
        $existingParams = [];
        parse_str($query, $existingParams);

        // Favor params from the original URL over $newParams
        $newParams = array_merge($newParams, $existingParams);

        // Sort for a predicable order
        ksort($newParams);

        return $path . '?' . http_build_query($newParams, null, '&');
    }

    /**
     * Check for a "/" prefix and prepend it if not exists.
     *
     * @param string|null $string
     *
     * @return string|null
     */
    public static function forceSlashPrefix($string)
    {
        if (!$string) {
            return $string;
        }

        return strpos($string, '/') === 0 ? $string : '/' . $string;
    }
}
