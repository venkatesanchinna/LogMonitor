<?php

declare(strict_types=1);

namespace Venkatesanchinna\LogMonitor\Entities;

use Venkatesanchinna\LogMonitor\Helpers\LogParser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

/**
 * Class     LogEntryCollection
 *
 * @author   Venkatesan <venkatesangee@gmail.com>
 */
class LogEntryCollection extends LazyCollection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Load raw log entries.
     *
     * @param  string  $raw
     *
     * @return self
     */
    public static function load($raw)
    {
        return new static(function () use ($raw) {
            foreach (LogParser::parse($raw) as $entry) {
                list($level, $header, $stack) = array_values($entry);

                yield new LogEntry($level, $header, $stack);
            }
        });
    }

    /**
     * Paginate log entries.
     *
     * @param  int  $perPage
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 20)
    {
        $page = request()->get('page', 1);
        $path = request()->url();

        return new LengthAwarePaginator(
            $this->forPage($page, $perPage),
            $this->count(),
            $perPage,
            $page,
            compact('path')
        );
    }

    /**
     * Get filtered log entries by level.
     *
     * @param  string  $level
     *
     * @return self
     */
    public function filterByLevel($level)
    {
        return $this->filter(function(LogEntry $entry) use ($level) {
            return $entry->isSameLevel($level);
        });
    }

    /**
     * Get log entries stats.
     *
     * @return array
     */
    public function stats()
    {
        $counters = $this->initStats();

        foreach ($this->groupBy('level') as $level => $entries) {
            $counters[$level] = $count = count($entries);
            $counters['all'] += $count;
        }


        return $counters;
    }

    /**
     * Get the log entries navigation tree.
     *
     * @param  bool|false  $trans
     *
     * @return array
     */
    public function tree($trans = false)
    {
        $tree = $this->stats();

        array_walk($tree, function(&$count, $level) use ($trans) {
            $count = [
                'name'  => $trans ? log_levels()->get($level) : $level,
                'count' => $count,
            ];
        });

        return $tree;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Init stats counters.
     *
     * @return array
     */
    private function initStats()
    {
        $levels = array_merge_recursive(
            ['all'],
            array_keys(log_monitor()->levels(true))
        );

        return array_map(function () {
            return 0;
        }, array_flip($levels));
    }
}
