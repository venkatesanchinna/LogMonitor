<?php

declare (strict_types = 1);

namespace Venkatesanchinna\LogMonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Venkatesanchinna\LogMonitor\Contracts\LogMonitor as LogMonitorContract;
use Venkatesanchinna\LogMonitor\Services\LaravelLogService;
use Venkatesanchinna\LogMonitor\Traits\LaravelLogTrait;

/**
 * Class     LogMonitorController
 *
 * @author   Venkatesan C <venkatesangee@gmail.com>
 */
class LogMonitorController extends Controller
{
    use LaravelLogTrait;
    /* -----------------------------------------------------------------
    |  Properties
    | -----------------------------------------------------------------
     */

    /**
     * The log viewer instance
     *
     * @var \Venkatesanchinna\LogMonitor\Contracts\LogMonitor
     */
    protected $logMonitor;

    /** @var int */
    protected $perPage = 30;

    /** @var string */
    protected $showRoute = 'log-monitor::logs.show';

    protected $laravelLogService;

    /* -----------------------------------------------------------------
    |  Constructor
    | -----------------------------------------------------------------
     */

    /**
     * LogMonitorController constructor.
     *
     * @param  \Venkatesanchinna\LogMonitor\Contracts\LogMonitor  $logMonitor
     */
    public function __construct(LogMonitorContract $logMonitor)
    {
        $this->logMonitor        = $logMonitor;
        $this->perPage           = config('log-monitor.per-page', $this->perPage);
        $this->laravelLogService = new LaravelLogService();
    }

    /* -----------------------------------------------------------------
    |  Main Methods
    | -----------------------------------------------------------------
     */

    /**
     * Show the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = $this->laravelLogService->laodDataForLog($this->logMonitor);
        return $this->view('dashboard', $data);
    }

    /**
     * List all logs.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\View\View
     */
    public function listLogs(Request $request)
    {
        $viewData = $this->laravelLogService->laodLogDetailsData($this->logMonitor, $this->perPage);
        return $this->view('logs', $viewData);
    }

    /**
     * Show the log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string                    $date
     *
     * @return \Illuminate\View\View
     */

    public function show(Request $request, $date)
    {
        $data = $this->laravelLogService->loadLogDetailsView($this->logMonitor, $this->perPage, $date);
        return $this->view('show', $data);
    }

    /**
     * Filter the log entries by level.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string                    $date
     * @param  string                    $level
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showByLevel(Request $request, $date, $level)
    {
        // Redirect if the level is 'all'
        if ($level === 'all') {
            return redirect()->route($this->showRoute, [$date]);
        }
        $data = $this->laravelLogService->loadLogDetailsByLevel($this->logMonitor, $this->perPage, $date, $level);
        return $this->view('show', $data);

    }

    /**
     * Show the log with the search query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string                    $date
     * @param  string                    $level
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function search(Request $request, $date, $level = 'all')
    {
        $query = request()->get('query');
        // Redirect if the query is null
        if (is_null($query)) {
            return redirect()->route($this->showRoute, [$date]);
        }
        $data = $this->laravelLogService->loadLogDetailsBySearch($this->logMonitor, $this->perPage, $date, $level);

        return $this->view('show', $data);
    }

    /**
     * Download the log
     *
     * @param  string  $date
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($date)
    {
        return $this->logMonitor->download($date);
    }

    /**
     * Delete a log.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        abort_unless($request->ajax(), 405, 'Method Not Allowed');

        $date = $request->input('date');

        $delete_status = $this->logMonitor->delete($date);
        \Session::flash($delete_status ? 'success' : 'error', __('log-monitor::log_monitor.laravel_log_deleted'));
        return response()->json([
            'result'  => $delete_status ? 'success' : 'error',
            'message' => __('log-monitor::log_monitor.laravel_log_deleted'),
        ]);
    }

    /* -----------------------------------------------------------------
    |  Other Methods
    | -----------------------------------------------------------------
     */

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     *
     * @return \Illuminate\View\View
     */
    protected function view($view, $data = [], $mergeData = [])
    {
        $theme = config('log-monitor.theme');

        return view()->make("log-monitor::{$theme}.{$view}", $data, $mergeData);
    }
}
