<?php

declare (strict_types = 1);

namespace Venkatesanchinna\LogMonitor\Helpers;

use Venkatesanchinna\LogMonitor\Utilities\LogLevels;
use Illuminate\Support\Str;

/**
 * Class     LogParser
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class LogParser
{
    /* -----------------------------------------------------------------
    |  Constants
    | -----------------------------------------------------------------
     */

    const REGEX_DATE_PATTERN     = '\d{4}(-\d{2}){2}';
    const REGEX_TIME_PATTERN     = '\d{2}(:\d{2}){2}';
    const REGEX_DATETIME_PATTERN = self::REGEX_DATE_PATTERN . ' ' . self::REGEX_TIME_PATTERN;

    /* -----------------------------------------------------------------
    |  Properties
    | -----------------------------------------------------------------
     */

    /**
     * Parsed data.
     *
     * @var array
     */
    protected static $parsed = [];

    /* -----------------------------------------------------------------
    |  Main Methods
    | -----------------------------------------------------------------
     */

    /**
     * Parse file content.
     *
     * @param  string  $raw
     *
     * @return array
     */
    public static function parse($raw)
    {
        static::$parsed        = [];
        list($headings, $data) = static::parseRawData($raw);

        // @codeCoverageIgnoreStart
        if (!is_array($headings)) {
            return static::$parsed;
        }
        // @codeCoverageIgnoreEnd

        foreach ($headings as $heading) {
            for ($i = 0, $j = count($heading); $i < $j; $i++) {
                static::populateEntries($heading, $data, $i);
            }
        };

        unset($headings, $data);

        return array_reverse(static::$parsed);
    }

    /* -----------------------------------------------------------------
    |  Other Methods
    | -----------------------------------------------------------------
     */

    /**
     * Extract the date.
     *
     * @param  string  $string
     *
     * @return string
     */
    public static function extractDate(string $string): string
    {
        return preg_replace('/.*(' . self::REGEX_DATE_PATTERN . ').*/', '$1', $string);
    }

    /**
     * Parse raw data.
     *
     * @param  string  $raw
     *
     * @return array
     */
    private static function parseRawData($raw)
    {

        $pattern = '/Date: ' . self::REGEX_DATETIME_PATTERN . '/';

        $cutom_pattern = '/\[\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}\+\d{2}:\d{2}\].*/';

        // Use preg_match to check if the pattern exists in the string
        if (preg_match($pattern, $raw)) {
            return self::parseCustomRawData($raw); // Pattern found
        }
        // Use preg_match to check if the pattern exists in the string
        if (preg_match($cutom_pattern, $raw)) {
            return self::parseCustomLogRawData($raw); // Pattern found
        }

        $pattern = '/\[' . self::REGEX_DATETIME_PATTERN . '\].*/';

        preg_match_all($pattern, $raw, $headings);
        $data = preg_split($pattern, $raw);

        if ($data[0] < 1) {
            $trash = array_shift($data);
            unset($trash);
        }

        return [$headings, $data];
    }
    /**
     * [parseCustomRawData description]
     * @param  [type] $raw [description]
     * @return [type]      [description]
     */
    private static function parseCustomRawData($raw)
    {
        // Adjust the pattern to match the given format
        $pattern = '/Date: ' . self::REGEX_DATETIME_PATTERN . '/';

        // Perform matching to both capture and split
        preg_match_all($pattern, $raw, $headings);

        // Split to get the remaining data, ignoring the first element if it's empty
        $data = preg_split($pattern, $raw);
        if ($data[0] === '') {
            array_shift($data);
        }

        // Initialize an array to store formatted headers
        foreach ($headings[0] as $index => $datetime) {
            // Construct the expected heading format
            $headings[0][$index] = '[' . str_replace("Date: ", "", $datetime) . '] local.ERROR:  ' . str_replace("\n", '', $data[$index]);
        }

        return [$headings, $data];
    }

    /**
     * Populate entries.
     *
     * @param  array  $heading
     * @param  array  $data
     * @param  int    $key
     */
    private static function populateEntries($heading, $data, $key)
    {
        foreach (LogLevels::all() as $level) {
            if (static::hasLogLevel($heading[$key], $level)) {
                static::$parsed[] = [
                    'level'  => $level,
                    'header' => $heading[$key],
                    'stack'  => $data[$key],
                ];
            }
        }
    }

    /**
     * Check if header has a log level.
     *
     * @param  string  $heading
     * @param  string  $level
     *
     * @return bool
     */
    private static function hasLogLevel($heading, $level)
    {
        return Str::contains($heading, strtoupper(".{$level}:"));
    }
    private static function parseCustomLogRawData($raw)
    {
        // Regex pattern to match the timestamp at the start of each log entry
        $timestampPattern = '\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}\+\d{2}:\d{2}';
        $pattern          = '/\[' . $timestampPattern . '\] */';

        // Match all log entry headings
        preg_match_all($pattern, $raw, $headings);

        // Split log entries by the timestamp pattern
        $data = preg_split($pattern, $raw);

        // Clean up the data array by removing empty or unnecessary elements
        if (isset($data[0]) && trim($data[0]) === '') {
            array_shift($data);
        }

        //  foreach ($headings[0] as $index => $datetime) {
        //     // Construct the expected heading format
        //     $headings[0][$index] = $datetime.' '.trim($data[$index]);
        // }

        // dd($headings, $data);

        // Return headings and corresponding log entries
        return [$headings, $data];
    }
}
