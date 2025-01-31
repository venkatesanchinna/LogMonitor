<?php

namespace Venkatesanchinna\LogMonitor\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Venkatesanchinna\LogMonitor\Entities\LogEntry;
use Venkatesanchinna\LogMonitor\Entities\LogEntryCollection;
use Venkatesanchinna\LogMonitor\Exceptions\LogNotFoundException;
use Venkatesanchinna\LogMonitor\Services\LaravelLogService;
use Venkatesanchinna\LogMonitor\Tables\StatsTable;
use Venkatesanchinna\LogMonitor\Traits\LaravelLogTrait;

class LaravelLogService
{
    use LaravelLogTrait;
    protected $perPage;
    protected $showRoute = 'log-monitor::logs.show';
    /**
     * Load data for log viewing including log_folder_name-specific information if available.
     *
     * @param \LogMonitor\LogMonitor $logMonitor The log viewer instance used to fetch log statistics.
     * @return array Returns an array of data prepared for the log view.
     */
    public function laodDataForLog($logMonitor)
    {
        $log_folder_id = request()->get('log_folder_id');
        // Initialize variables
        $log_folder_name = request()->get('log_folder_name');

        $data = $this->prepareViewData($log_folder_id);
        // Prepare data for the view
        $stats             = $logMonitor->statsTable(false, $log_folder_name, true);
        $data['chartData'] = $this->prepareChartData($stats);
        $data['percents']  = $this->calcPercentages($stats->footer(), $stats->header());

        return $data;
    }

    /**
     * Format log_folder_name name for consistent use.
     *
     * @param string $name
     * @return string
     */
    private function formatFolderName($name)
    {

        $folderSlug = preg_replace('/\s+/', '_', strtolower($name));

        return ucfirst($folderSlug);
    }

    /**
     * Prepare data array for the view.
     *
     * @param int|null $log_folder_id
     * @return array
     */
    private function prepareViewData($log_folder_id)
    {
        return [
            'log_folder_id'   => $log_folder_id,
            'months'          => getFullMonths(),
            'years'           => range(gmdate('Y', strtotime('2016-12-01')), gmdate('Y', strtotime(date('Y-m-d')))),
            'tab'             => 'logs',
            'view'            => 'dashboard_log',
            'theme'           => config('log-monitor.theme'),
            'log_folder_name' => request()->get('log_folder_name'),
        ];
    }

    /**
     * Collect log folders' information.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getLogFolders()
    {
        return collect([]);
    }
    /**
     * Prepare chart data.
     *
     * @param  \Venkatesanchinna\LogMonitor\Tables\StatsTable  $stats
     *
     * @return string
     */
    protected function prepareChartData(StatsTable $stats)
    {
        $totals = $stats->totals()->all();

        return json_encode([
            'labels'   => Arr::pluck($totals, 'label'),
            'datasets' => [
                [
                    'data'                 => Arr::pluck($totals, 'value'),
                    'backgroundColor'      => Arr::pluck($totals, 'color'),
                    'hoverBackgroundColor' => Arr::pluck($totals, 'highlight'),
                ],
            ],
        ]);
    }

    /**
     * Calculate the percentage.
     *
     * @param  array  $total
     * @param  array  $names
     *
     * @return array
     */
    protected function calcPercentages(array $total, array $names)
    {
        $percents = [];
        $all      = Arr::get($total, 'all');

        foreach ($total as $level => $count) {
            $percents[$level] = [
                'name'    => $names[$level],
                'count'   => $count,
                'percent' => $all ? round(($count / $all) * 100, 2) : 0,
            ];
        }

        return $percents;
    }
    /**
     * Load detailed log data for viewing.
     *
     * @param \LogMonitor\LogMonitor $logMonitor The log viewer instance used to interact with logs.
     * @param int $per_page Number of entries to display per page.
     * @return array Returns an array of data prepared for log viewing.
     */
    public function laodLogDetailsData($logMonitor, $per_page = 30)
    {
        $this->perPage    = $per_page;
        $log_folder_id    = request()->get('log_folder_id');
        $log_folder_name  = request()->get('log_folder_name');
        $log_file_results = ['log_folder_id' => $log_folder_id, 'log_folder_name' => $log_folder_name];

        // Prepare data needed for the logs view
        $stats   = $logMonitor->statsTable(false, $log_folder_name);
        $headers = $stats->header();
        $rows    = $this->paginate($stats->rows());

        $viewData = $this->prepareLogViewData($log_folder_id, $stats, $headers, $rows, $log_file_results);
        return $viewData;
    }

/**
 * Prepare the data array for the views, containing various stats and configuration.
 *
 * @param int|null $log_folder_id
 * @param mixed $stats
 * @param array $headers
 * @param \Illuminate\Pagination\LengthAwarePaginator $rows
 * @param array $log_file_results
 * @return array
 */
    private function prepareLogViewData($log_folder_id, $stats, $headers, $rows, $log_file_results)
    {
        return [
            'log_folder_id'    => $log_folder_id,
            'chartData'        => $this->prepareChartData($stats),
            'percents'         => $this->calcPercentages($stats->footer(), $stats->header()),
            'theme'            => config('log-monitor.theme'),
            'headers'          => $headers,
            'rows'             => $rows,
            'months'           => getFullMonths(),
            'years'            => range(gmdate('Y', strtotime('2016-12-01')), gmdate('Y', strtotime(date('Y-m-d')))),
            'tab'              => 'logs',
            'view'             => 'dashboard_logs',
            'log_folders'      => $this->getLogFolders(),
            'log_file_results' => $log_file_results,
            'log_folder_name'  => request()->get('log_folder_name'),
        ];
    }
    /**
     * Paginate logs.
     *
     * @param  array                     $data
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(array $data, $per_page = false)
    {
        $per_page = $per_page ? $per_page : $this->perPage;
        $data     = new Collection($data);
        $page     = request()->get('page', 1);
        $path     = request()->url();

        return new LengthAwarePaginator(
            $data->forPage($page, $per_page),
            $data->count(),
            $per_page,
            $page,
            compact('path')
        );
    }

    /**
     * Get a log or fail
     *
     * @param  string  $date
     *
     * @return \Venkatesanchinna\LogMonitor\Entities\Log|null
     */
    public function getLogOrFail($date, $logMonitor)
    {
        $log = null;

        try {
            $log = $logMonitor->get($date);
        } catch (LogNotFoundException $e) {
            abort(404, $e->getMessage());
        }

        return $log;
    }
    /**
     * Load log details based on search parameters.
     *
     * @param \LogMonitor\LogMonitor $logMonitor The log viewer instance used to interact with logs.
     * @param int $per_page Number of entries to display per page.
     * @param string $date The date of the logs to retrieve.
     * @param string $level The severity level of logs to filter.
     * @return array Returns an array of data prepared for view rendering.
     */
    public function loadLogDetailsView($logMonitor, $per_page = 30, $date = false)
    {
        $this->perPage   = $per_page;
        $level           = 'all';
        $log_folder_id   = request()->get('log_folder_id');
        $log_folder_name = request()->get('log_folder_name');
        $data            = [];

        $log                     = $this->getLogOrFail($date, $logMonitor);
        $data['query']           = request()->get('query');
        $data['levels']          = $logMonitor->levelsNames();
        $data['entries']         = $log->entries($level)->paginate($this->perPage);
        $data['log']             = $log;
        $data['theme']           = config('log-monitor.theme');
        $data['level']           = $level;
        $data['log_folder_id']   = $log_folder_id;
        $data['log_folder_name'] = request()->get('log_folder_name');

        if ($log_folder_id) {
            $data = array_merge($data, $this->prepareFolderViewData($log_folder_name, $logMonitor));
        }

        $log_folders              = $this->getLogFolders();
        $data['log_file_results'] = $this->getLogFilesByFolder($date, $data, $log_folders, $log_folder_name);
        return $data;
    }

/**
 * Prepare view data specific to a log_folder_name context.
 *
 * @param Request $request
 * @param string $log_folder_name
 * @return array
 */
    private function prepareFolderViewData($log_folder_name, $logMonitor)
    {
        $stats   = $logMonitor->statsTable(false, $log_folder_name);
        $headers = $stats->header();
        $rows    = $this->paginate($stats->rows());

        return [
            'months'    => getFullMonths(),
            'years'     => range(gmdate('Y', strtotime('2016-12-01')), gmdate('Y', strtotime(date('Y-m-d')))),
            'tab'       => 'logs',
            'view'      => 'log_view',
            'theme'     => config('log-monitor.theme'),
            'chartData' => $this->prepareChartData($stats),
            'percents'  => $this->calcPercentages($stats->footer(), $stats->header()),
            'headers'   => $headers,
            'rows'      => $rows,
        ];
    }
    /**
     * Load log details based on level parameters.
     *
     * @param \LogMonitor\LogMonitor $logMonitor The log viewer instance used to interact with logs.
     * @param int $per_page Number of entries to display per page.
     * @param string $date The date of the logs to retrieve.
     * @param string $level The severity level of logs to filter.
     * @return array Returns an array of data prepared for view rendering.
     */
    public function loadLogDetailsByLevel($logMonitor, $per_page = 30, $date, $level)
    {
        $this->perPage   = $per_page;
        $log_folder_id   = request()->get('log_folder_id');
        $log_folder_name = request()->get('log_folder_name');

        // Get the log and log entries
        $log     = $this->getLogOrFail($date, $logMonitor);
        $query   = request()->get('query');
        $levels  = $logMonitor->levelsNames();
        $entries = $logMonitor->entries($date, $level)->paginate($this->perPage);

        $viewData = [
            'level'            => $level,
            'log'              => $log,
            'query'            => $query,
            'levels'           => $levels,
            'entries'          => $entries,
            'theme'            => config('log-monitor.theme'),
            'log_file_results' => $this->getLogFilesByFolder($date, compact('log_folder_id'), $this->getLogFolders(), $log_folder_name),
            'log_folder_id'    => $log_folder_id,
            'log_folder_name'  => request()->get('log_folder_name'),
        ];

        if ($log_folder_id) {
            // Get additional data for log_folder_name-specific views
            $folderViewData = $this->getFolderViewData($logMonitor, $log_folder_id, $log_folder_name);
            $viewData       = array_merge($viewData, $folderViewData);
        }

        return $viewData;
    }

/**
 * Prepare log_folder_name-specific data for the view.
 *
 * @param Request $request
 * @param int $log_folder_id
 * @param string $log_folder_name
 * @return array
 */
    private function getFolderViewData($logMonitor, $log_folder_id, $log_folder_name)
    {

        $stats   = $logMonitor->statsTable(false, $log_folder_name);
        $headers = $stats->header();
        $rows    = $this->paginate($stats->rows());

        return [
            'chartData'       => $this->prepareChartData($stats),
            'percents'        => $this->calcPercentages($stats->footer(), $stats->header()),
            'months'          => getFullMonths(),
            'years'           => range(gmdate('Y', strtotime('2016-12-01')), gmdate('Y', strtotime(date('Y-m-d')))),
            'tab'             => 'logs',
            'view'            => 'log_view',
            'theme'           => config('log-monitor.theme'),
            'headers'         => $headers,
            'rows'            => $rows,
            'log_folder_id'   => $log_folder_id,
            'log_folder_name' => request()->get('log_folder_name'),
        ];
    }
    /**
     * Load log details based on search parameters.
     *
     * @param \LogMonitor\LogMonitor $logMonitor The log viewer instance used to interact with logs.
     * @param int $per_page Number of entries to display per page.
     * @param string $date The date of the logs to retrieve.
     * @param string $level The severity level of logs to filter.
     * @return array Returns an array of data prepared for view rendering.
     */
    public function loadLogDetailsBySearch($logMonitor, $per_page = 30, $date, $level)
    {
        $this->perPage = $per_page;

        $log_folder_id = request()->get('log_folder_id');
        $query         = request()->get('query');

        $data = $this->initializeData($log_folder_id, $logMonitor, $date, $level, $query);

        if ($log_folder_id) {
            $folderViewData = $this->getFolderViewData($logMonitor, $log_folder_id, $data['log_folder_name']);
            $data           = array_merge($data, $folderViewData);
        }

        return $data;
    }

    /**
     * Initialize the basic data for log details.
     *
     * @param int|null $log_folder_id
     * @param \LogMonitor\LogMonitor $logMonitor
     * @param string $date
     * @param string $level
     * @param string|null $query
     * @return array
     */
    private function initializeData($log_folder_id, $logMonitor, $date, $level, $query)
    {
        $folderName = request()->get('log_folder_name') ? $this->formatFolderName(request()->get('log_folder_name')) : '';

        return [
            'log_folder_id'    => $log_folder_id,
            'log_folder_name'  => $folderName,
            'level'            => $level,
            'log'              => $this->getLogOrFail($date, $logMonitor),
            'query'            => $query,
            'levels'           => $logMonitor->levelsNames(),
            'entries'          => $this->getFilteredLogEntries($logMonitor, $date, $level, $query),
            'theme'            => config('log-monitor.theme'),
            'log_file_results' => $this->getLogFilesByFolder($date, compact('log_folder_id'), $this->getLogFolders(), $folderName),
        ];
    }

    /**
     * Get and format the log_folder_name's name.
     *
     * @param int $log_folder_id
     * @return string
     */
    private function getFormattedFolderName()
    {
        return $this->formatFolderName(request()->get('log_folder_name'));
    }

    /**
     * Filter log entries based on the level and query.
     *
     * @param \LogMonitor\LogMonitor $logMonitor
     * @param string $date
     * @param string $level
     * @param string|null $query
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getFilteredLogEntries($logMonitor, $date, $level, $query)
    {
        $log = $logMonitor->get($date);
        return $this->filterLogEntries($log, $level, $query);
    }

    /**
     * Filter log entries based on the search query.
     *
     * @param \LogMonitor\Log $log
     * @param string $level
     * @param string|null $query
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function filterLogEntries($log, $level, $query)
    {
        $needles = array_map(function ($needle) {
            return Str::lower($needle);
        }, array_filter(explode(' ', $query)));

        return $log->entries($level)
            ->unless(empty($needles), function (LogEntryCollection $entries) use ($needles) {
                return $entries->filter(function (LogEntry $entry) use ($needles) {
                    return Str::containsAll(Str::lower($entry->header), $needles);
                });
            })
            ->paginate($this->perPage);
    }

}
