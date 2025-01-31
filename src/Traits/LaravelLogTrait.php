<?php

namespace Venkatesanchinna\LogMonitor\Traits;

use App\Models\Folder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait LaravelLogTrait
{

    
    /**
     * Fetch log files and related information for a log_folder_name by date.
     *
     * @param string $input_file_name          The date to derive the log file name.
     * @param array  $data          Data array containing log_folder_name-related details.
     * @param array  $log_folders List of log folders.
     * @param string $log_folder_name Optional log_folder_name name.
     * @return array Returns an array with log_folder_name log details.
     */
    public function getLogFilesByFolder($input_file_name, $data, $log_folders, $log_folder_name = '')
    {
        $log_folder_id_sync = '';

        // Parse filename from date string
        $filename = $this->extractFilenameFromDate($input_file_name);

        // Determine log_folder_name ID and name if not provided
        if (empty($data['log_folder_id']) && !empty($log_folders)) {
            list($log_folder_id_sync, $log_folder_name) = $this->resolveFolderFromLog($input_file_name, $log_folders);
        }

        // Fetch and prepare log files for the log_folder_name
        $files              = $this->fetchLogFiles($log_folder_name);
        $log_folder_log_files = $this->prepareAndSortLogFiles($files, $log_folder_name);

        // Reorder the log files to ensure 'laravel.log' comes first
        $log_folder_log_files = $this->prioritizeLogFile($log_folder_log_files, 'Laravel.log');

        return [
            'log_folder_id_sync'   => $log_folder_id_sync,
            'log_folder_log_files' => $log_folder_log_files,
            'log_folder_name'      => $this->formatFolderName($log_folder_name),
            'filename'           => $filename,
        ];
    }

    /**
     * Extract the filename from a given date string.
     *
     * @param string $input_file_name The date string to process.
     * @return string Returns the extracted filename.
     */
    private function extractFilenameFromDate($input_file_name)
    {
        $explode = explode("_", $input_file_name);
        return array_pop($explode);
    }

    /**
     * Prepare and sort log_folder_name's log files.
     *
     * @param array  $files         List of files.
     * @param string $log_folder_name The log_folder_name's name.
     * @return array Returns the sorted files array.
     */
    private function prepareAndSortLogFiles($files, $log_folder_name)
    {
        $log_folder_log_files = $this->prepareFolderLogsArray($files, $log_folder_name);
        ksort($log_folder_log_files, SORT_STRING | SORT_FLAG_CASE);
        return $log_folder_log_files;
    }

    /**
     * Prioritize a specific log file by moving it to the start of the array.
     *
     * @param array  $files       Array of log_folder_name log files.
     * @param string $priorityLog The value of the log file to prioritize.
     * @return array Returns the reordered files array.
     */
    private function prioritizeLogFile($files, $priorityLog)
    {
        $key = array_search($priorityLog, $files);
        if ($key !== false) {
            $prioritizedLog = [$key => $files[$key]];
            unset($files[$key]);
            $files = $prioritizedLog + $files;
        }
        return $files;
    }

    /**
     * Format the log_folder_name name by replacing underscores and capitalizing words.
     *
     * @param string $log_folder_name The original log_folder_name name.
     * @return string Returns the formatted log_folder_name name.
     */
    private function formatFolderName($log_folder_name)
    {
        return ucfirst(str_replace("_", " ", str_replace("_laravel.log", "", $log_folder_name)));
    }

    /**
     * Resolve log_folder_name ID and name from log file.
     *
     * @param string $input_file_name The date to process.
     * @param \Illuminate\Support\Collection $log_folders Collection of folders' log data.
     * @return array An array containing log_folder_name ID sync and log_folder_name name.
     */
    protected function resolveFolderFromLog($input_file_name)
    {
        $explode = explode("_", $input_file_name);
        array_pop($explode);
        $log_file_name       = ucfirst(implode("_", $explode));
        $log_file_name_clean = strtolower(str_replace("_", " ", str_replace("_laravel.log", "", $log_file_name)));

        $log_folder_id_sync =  '';
        $log_folder_name    = ucfirst(implode("_", $explode));

        return [$log_folder_id_sync, $log_folder_name];
    }

    /**
     * Fetch log files from the storage path for a given log_folder_name.
     *
     * @param string $log_folder_name The log_folder_name's name.
     * @return array The array of log file objects.
     */
    protected function fetchLogFiles($log_folder_name)
    {
        $logPath = storage_path('logs/' . $log_folder_name);

        if (!is_dir($logPath)) {
            return [];
        }

        return File::files($logPath);
    }

    /**
     * Prepare the log_folder_name logs array with processed filenames.
     *
     * @param array $files The array of file objects.
     * @param string $log_folder_name The log_folder_name's name.
     * @return array The flattened associative array of log files.
     */
    protected function prepareFolderLogsArray($files, $log_folder_name)
    {
        // Map through each file to apply transformations
        $files_array = array_map(function ($file) use ($log_folder_name) {
            $folder = $file->getPath();
            $folderSlug = preg_replace('/\s+/', '_', strtolower(basename($folder)));
            
            // $folderSlug = Str::slug(strtolower(basename($folder)), '_');
            $base_name = basename($folder);
            $filename = $file->getFilename();
            if (strpos($filename, $folderSlug) === false && strpos($filename, $base_name) === false && $base_name != 'logs') {
                $filename = str_replace($file->getFilename(), $base_name . '_' . $file->getFilename(), $file->getFilename());
            }

            if ($file->getExtension() === 'log') {
                return [$filename => ucfirst(str_replace(strtolower($log_folder_name) . "_", "", $file->getFilename()))];
            } else {
                return [];
            }
        }, $files);

        // Flatten the array of arrays into one associative array
        $log_folder_log_files = [];
        foreach ($files_array as $innerArray) {
            if (!empty($innerArray)) {
                $log_folder_log_files += $innerArray; // Merge arrays to one associative array
            }
        }

        return $log_folder_log_files;
    }

}
