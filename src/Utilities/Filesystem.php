<?php

declare (strict_types = 1);

namespace Venkatesanchinna\LogMonitor\Utilities;

use Exception;
use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Venkatesanchinna\LogMonitor\Contracts\Utilities\Filesystem as FilesystemContract;
// use Venkatesanchinna\LogMonitor\Helpers\LogParser;
use Venkatesanchinna\LogMonitor\Exceptions\FilesystemException;

/**
 * Class     Filesystem
 *
 * @author   Venkatesan <venkatesangee@gmail.com>
 */
class Filesystem implements FilesystemContract
{
    /* -----------------------------------------------------------------
    |  Properties
    | -----------------------------------------------------------------
     */

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The base storage path.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * The log files prefix pattern.
     *
     * @var string
     */
    protected $prefixPattern;

    /**
     * The log files date pattern.
     *
     * @var string
     */
    protected $datePattern;

    /**
     * The log files extension.
     *
     * @var string
     */
    protected $extension;

    protected $log_folder_name;
    protected $log_file;
    protected $is_dashboard;

    /* -----------------------------------------------------------------
    |  Constructor
    | -----------------------------------------------------------------
     */

    /**
     * Filesystem constructor.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string                             $storagePath
     */
    public function __construct(IlluminateFilesystem $files, $storagePath)
    {
        $this->filesystem = $files;
        $this->setPath($storagePath);
        $this->setPattern();
    }

    /* -----------------------------------------------------------------
    |  Getters & Setters
    | -----------------------------------------------------------------
     */

    /**
     * Get the files instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getInstance()
    {
        return $this->filesystem;
    }

    /**
     * Set the log storage path.
     *
     * @param  string  $storagePath
     *
     * @return $this
     */
    public function setPath($storagePath)
    {
        $this->storagePath = $storagePath;

        return $this;
    }

    /**
     * Get the log pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->prefixPattern . $this->datePattern . $this->extension;
    }

    public function getDateTimePattern(): string
    {
        $timestampPattern = '\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}\+\d{2}:\d{2}';
        $pattern          = '\[' . $timestampPattern . '\] *';
        return $pattern;
    }
    public function getCustomPattern(): string
    {
        $regex   = '\d{4}(-\d{2}){2} \d{2}(:\d{2}){2}';
        $pattern = 'Date: ' . $regex . '';
        return $pattern;
    }

    /**
     * Set the log pattern.
     *
     * @param  string  $date
     * @param  string  $prefix
     * @param  string  $extension
     *
     * @return $this
     */
    public function setPattern(
        $prefix = self::PATTERN_PREFIX,
        $date = self::PATTERN_DATE,
        $extension = self::PATTERN_EXTENSION
    ) {
        $this->setPrefixPattern($prefix);
        $this->setDatePattern($date);
        $this->setExtension($extension);

        return $this;
    }

    /**
     * Set the log date pattern.
     *
     * @param  string  $datePattern
     *
     * @return $this
     */
    public function setDatePattern($datePattern)
    {
        $this->datePattern = $datePattern;

        return $this;
    }

    /**
     * Set the log prefix pattern.
     *
     * @param  string  $prefixPattern
     *
     * @return $this
     */
    public function setPrefixPattern($prefixPattern)
    {
        $this->prefixPattern = $prefixPattern;

        return $this;
    }

    /**
     * Set the log extension.
     *
     * @param  string  $extension
     *
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /* -----------------------------------------------------------------
    |  Main Methods
    | -----------------------------------------------------------------
     */

    /**
     * Get all log files.
     *
     * @return array
     */
    public function all()
    {
        return $this->getFiles('*' . $this->extension);
    }

    /**
     * Get all valid log files.
     *
     * @return array
     */
    public function logs($log_file = '')
    {
        return $this->getFiles($this->getPattern());
    }

    /**
     * Retrieve and process log dates.
     *
     * @param bool $withPaths    Whether to include paths in the returned array.
     * @param bool $log_folder_name     Customer name for log association.
     * @param bool $log_file     Specific log file to process.
     * @param bool $is_dashboard Whether the context is a dashboard.
     * @return array Processed dates associated with log files.
     */
    public function dates($withPaths = false, $log_folder_name = false, $log_file = false, $is_dashboard = false)
    {
        $this->log_folder_name = $log_folder_name;
        $this->log_file        = $log_file;
        $this->is_dashboard    = $is_dashboard;

        // Fetch and reverse the order of log files
        $files = $this->getReversedLogFiles($log_file);
        // Extract dates from the log files
        $dates = $this->extractDates($files);

        // Optionally combine dates with paths
        if ($withPaths) {
            $dates = $this->combineDatesWithPaths($dates, $files);
        }

        // Sort the dates
        $dates = $this->sortDates($dates);

        // Move the Laravel log to the end if it exists
        $dates = $this->moveLogFileToEnd($dates);

        return $dates;
    }

    /**
     * Fetch and reverse the order of log files.
     *
     * @param string|bool $log_file Specific log file to fetch.
     * @return array Reversed array of log files.
     */
    private function getReversedLogFiles($log_file)
    {
        return array_reverse($this->logs($log_file));
    }

    /**
     * Combine dates with their corresponding log file paths.
     *
     * @param array $dates Dates extracted from logs.
     * @param array $files Log files matched to dates.
     * @return array Combined array of dates and paths.
     */
    private function combineDatesWithPaths($dates, $files)
    {
        return array_combine($dates, $files);
    }

    /**
     * Sort dates in a case-insensitive manner.
     *
     * @param array $dates Dates to sort.
     * @return array Sorted dates.
     */
    private function sortDates($dates)
    {
        ksort($dates, SORT_STRING | SORT_FLAG_CASE);
        return $dates;
    }

    /**
     * Move the Laravel log file to the end of the dates array.
     *
     * @param array $dates Array of dates to process.
     * @return array Updated array with the Laravel log moved to the end.
     */
    private function moveLogFileToEnd($dates)
    {
        $lastKey = $this->log_folder_name ? strtolower($this->log_folder_name) . "_laravel.log" : 'laravel.log';

        if (array_key_exists($lastKey, $dates)) {
            $laravelLog = [$lastKey => $dates[$lastKey]];
            unset($dates[$lastKey]);

            // Append laravel log to the end of the array
            $dates = $this->log_folder_name ? $laravelLog + $dates : $dates + $laravelLog;
        }

        return $dates;
    }

    /**
     * Read the log.
     *
     * @param  string  $date
     *
     * @return string
     *
     * @throws \Venkatesanchinna\LogMonitor\Exceptions\FilesystemException
     */
    public function read($date, $path = '')
    {
        try {
            $log = $this->filesystem->get(
                $this->getLogPath($date, $path)
            );
        } catch (Exception $e) {
            throw new FilesystemException($e->getMessage());
        }

        return $log;
    }

    /**
     * Delete the log.
     *
     * @param  string  $date
     *
     * @return bool
     *
     * @throws \Venkatesanchinna\LogMonitor\Exceptions\FilesystemException
     */
    public function delete(string $date)
    {
        $path = $this->getLogPath($date);

        throw_unless($this->filesystem->delete($path), FilesystemException::cannotDeleteLog());

        return true;
    }

    /**
     * Clear the log files.
     *
     * @return bool
     */
    public function clear()
    {
        return $this->filesystem->delete($this->logs());
    }

    /**
     * Get the log file path.
     *
     * @param  string  $date
     *
     * @return string
     */
    public function path($date)
    {
        return $this->getLogPath($date);
    }

    /* -----------------------------------------------------------------
    |  Other Methods
    | -----------------------------------------------------------------
     */

    /**
     * Get all files.
     *
     * @param  string  $pattern
     *
     * @return array
     */
    /**
     * Main method to get files based on pattern.
     *
     * @param string $pattern The pattern to search for.
     * @return array An array of file paths.
     */
    private function getFiles($pattern = '')
    {
        $files = $this->determineFiles($pattern);
        return $this->resolveRealPaths($files);
    }

    /**
     * Determine which files to process based on provided conditions.
     *
     * @param string $pattern Pattern to match files.
     * @return array Array of file paths to process.
     */
    private function determineFiles($pattern = '')
    {
        $files = [];

        // Process the log file if it's set and not empty
        if (!empty($this->log_file)) {
            list($file_name, $folder_name) = $this->processLogFile($this->log_file);
        }

        if (!empty($this->log_folder_name)) {
            // Fetch files for the specific log_folder_name
            $files = $this->getFilesForFolder($pattern);
        } elseif (!empty($folder_name)) {
            // Get a specific file path when folder name is provided
            $files = $this->getFilePathForName($folder_name, $file_name);
        } else {
            // Get files using pattern and search through directories
            $files = $this->getFilesByPattern($pattern, $folder_name ?? '');

        }

        return $files;
    }

    /**
     * Resolve the real file system paths of all files in the list.
     *
     * @param array $files Array of file paths to check.
     * @return array Array of real paths for files that exist.
     */
    private function resolveRealPaths($files)
    {
        $realPaths = [];

        foreach ($files as $file) {
            $realPath = realpath($file);

            if ($realPath !== false) {
                // Add resolved real path if it exists
                $realPaths[] = $realPath;
            } else {
                // Attempt to resolve the real path again with a modified filename
                $realPath = $this->attemptToFixFilePath($file);

                if ($realPath !== false) {
                    $realPaths[] = $file;
                }
            }
        }

        return $realPaths;
    }

    /**
     * Try to fix the file path and resolve its real path.
     *
     * @param string $file The original file path to attempt to fix.
     * @return string|false The real path if resolved, otherwise false.
     */
    private function attemptToFixFilePath($file)
    {
        $explode  = explode("_", basename($file));
        $pattern  = array_pop($explode);
        $new_path = str_replace(basename($file), $pattern, $file);
        return realpath($new_path);
    }

    /**
     * Process the log file to get file and folder names.
     *
     * @param string $logFile The log file string.
     * @return array An array containing file name and folder name.
     */
    private function processLogFile($logFile)
    {
        $explode = explode("_", $logFile);
        $pattern = array_pop($explode);

        $fileName    = $logFile;
        $folder_name = !empty($explode) ? str_replace("_" . $pattern, "", $logFile) : '';
        $folder_name = !empty($folder_name) ? ucfirst($folder_name) : $folder_name;
        if (empty($folder_name) && $logFile !== 'laravel.log') {
            $folder_name = 'logs';
        }

        return [$fileName, $folder_name];
    }

    /**
     * Retrieve files for a specific log_folder_name.
     *
     * @return array An array of file paths.
     */
    private function getFilesForFolder($pattern = '')
    {

        $folderSlug = ucfirst(preg_replace('/\s+/', '_', strtolower(basename($this->log_folder_name))));

        $files = $this->filesystem->glob(
            $this->storagePath . DIRECTORY_SEPARATOR . $folderSlug . DIRECTORY_SEPARATOR . '*'
        );

        $files = $this->addPatternMatchingFiles($this->storagePath . DIRECTORY_SEPARATOR . $folderSlug, $files, $pattern);
        return $files;
    }

    /**
     * Get a file path based on folder and file name.
     *
     * @param string $folder_name The folder name.
     * @param string $fileName The file name.
     * @return array An array containing the constructed file path.
     */
    private function getFilePathForName($folder_name, $fileName)
    {
        $path = str_replace("logs/logs/", "logs/", $this->storagePath . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $fileName);
        return [$path];
    }

    /**
     * Search for files using a pattern, includes directory search.
     *
     * @param string $pattern The search pattern.
     * @param string $folder_name Optional folder name for the base path.
     * @return array An array of file paths.
     */
    private function getFilesByPattern($pattern, $folder_name)
    {
        // Get files matching the given pattern directly from the storage path
        $files = $this->getMatchingFiles($pattern);

        // Determine the base path based on the presence of a folder name
        $basePath = $this->determineBasePath($folder_name);

        // Retrieve and process directories
        $folders = $this->getDirectories($basePath);
        $files   = $this->processDirectories($folders, $files, $pattern);

        return $files;
    }

    /**
     * Retrieve files matching a specified pattern from the storage path.
     *
     * @param string $pattern
     * @return array
     */
    private function getMatchingFiles($pattern)
    {
        return $this->filesystem->glob(
            $this->storagePath . DIRECTORY_SEPARATOR . $pattern, defined('GLOB_BRACE') ? GLOB_BRACE : 0
        );
    }

    /**
     * Determine the base path for file retrieval, depending on folder presence.
     *
     * @param string|null $folder_name
     * @return string
     */
    private function determineBasePath($folder_name)
    {
        return !empty($folder_name)
        ? $this->storagePath . DIRECTORY_SEPARATOR . $folder_name
        : $this->storagePath;
    }

    /**
     * Retrieve directories from a given base path.
     *
     * @param string $basePath
     * @return array
     */
    private function getDirectories($basePath)
    {
        return File::directories($basePath);
    }

    /**
     * Process directories to filter and retrieve .log files and pattern matching files.
     *
     * @param array $folders
     * @param array $files
     * @param string $pattern
     * @return array
     */
    private function processDirectories($folders, $files, $pattern)
    {
        foreach ($folders as $folder) {
            if (File::isDirectory($folder)) {
                if ($this->isDashboard()) {
                    $files = $this->collectLogFiles($folder, $files);
                } else {
                    $files = $this->addPatternMatchingFiles($folder, $files, $pattern);
                }
            }
        }
        return $files;
    }

    /**
     * Collect .log files from a specified directory.
     *
     * @param string $folder
     * @param array $files
     * @return array
     */
    private function collectLogFiles($folder, $files)
    {
        $logFiles = $this->getFilesByFolder($folder);
        return array_merge($files, $logFiles);
    }

    /**
     * Add files matching the pattern from folders, adjusting their path names.
     *
     * @param string $folder
     * @param array $files
     * @param string $pattern
     * @return array
     */
    private function addPatternMatchingFiles($folder, $files, $pattern)
    {

        $folderSlug = preg_replace('/\s+/', '_', strtolower(basename($folder)));

        $filePath = $folder . '/' . $folderSlug . '_' . $pattern;
        if (file_exists($filePath)) {
            $files[] = $filePath;
        } else {
            $logFiles = $this->getFilesByFolder($folder);

            if (@$this->log_folder_name && !empty($logFiles)) {
                $files = $logFiles;

            } else if (!empty($logFiles)) {
                $files[] = $logFiles[0];
            }

        }

        return $files;
    }

    /**
     * Check if the dashboard mode is active.
     *
     * @return bool
     */
    private function isDashboard()
    {
        return $this->is_dashboard ?? false;
    }

    /**
     * Get the log file path.
     *
     * @param  string  $date
     *
     * @return string
     *
     * @throws \Venkatesanchinna\LogMonitor\Exceptions\FilesystemException
     */
    /**
     * Get the path of the log file.
     *
     * @param string $log_file_name The name of the log file.
     * @return string The real path of the log file.
     * @throws FilesystemException if the log file path is invalid.
     */
    private function getLogPath(string $log_file_name, $realpath = '')
    {
        $path = $this->buildInitialPath($log_file_name);

        if ($this->needsFolderResolution($log_file_name)) {
            $path = $this->resolveFolderPath($log_file_name, $realpath);

        }
        if (!$this->filesystem->exists($path)) {
            throw FilesystemException::invalidPath($path);
        }

        return realpath($path);
    }

    /**
     * Build the initial path for the log file.
     *
     * @param string $log_file_name The log file name.
     * @return string The initial path.
     */
    private function buildInitialPath(string $log_file_name)
    {
        return $this->storagePath . DIRECTORY_SEPARATOR . $log_file_name;
    }

    /**
     * Determine if the folder name resolution is needed.
     *
     * @param string $log_file_name The name of the log file.
     * @return bool True if folder resolution is needed.
     */
    private function needsFolderResolution(string $log_file_name)
    {
        return !empty($log_file_name) && $log_file_name !== 'laravel.log';
    }

    /**
     * Resolve the folder path for a given log file name.
     *
     * @param string $log_file_name The log file name to resolve.
     * @param string $realpath    Optional real path to use if conditions are met.
     * @return string The resolved folder path.
     */
    private function resolveFolderPath(string $log_file_name, $realpath = '')
    {
        // Determine the effective log file name
        $log_file_name = $this->determineLogFileName($log_file_name);

        // Extract pattern and folder name from the log file name
        list($pattern, $folder_name) = $this->extractPatternAndFolderName($log_file_name);

        // Build the initial path based on file naming conventions
        $path = $this->buildInitialPathFothForLog($log_file_name, $folder_name, $pattern, $realpath);

        // Validate if the generated path exists, otherwise fallback to alternate path
        return $this->validateOrFallbackPath($path, $folder_name, $pattern);
    }

    /**
     * Determine the effective log file name based on the current state.
     *
     * @param string $log_file_name The provided log file name.
     * @return string The effective log file name.
     */
    private function determineLogFileName(string $log_file_name)
    {
        return !empty($this->log_file) ? $this->log_file : $log_file_name;
    }

    /**
     * Extract the pattern and folder name from the log file name.
     *
     * @param string $log_file_name The log file name to process.
     * @return array An array containing the pattern and folder name.
     */
    private function extractPatternAndFolderName(string $log_file_name)
    {
        $explode = explode("_", $log_file_name);
        $pattern = array_pop($explode);

        $folder_name = str_replace("_" . $pattern, "", $log_file_name);
        $folder_name = !empty($folder_name) ? ucfirst($folder_name) : $folder_name;

        return [$pattern, $folder_name];
    }

    /**
     * Build the initial path for the log file based on naming conventions and conditions.
     *
     * @param string $log_file_name The log file name.
     * @param string $folder_name  The derived folder name.
     * @param string $pattern     The extracted pattern from the file name.
     * @param string $realpath    The optional real path to consider.
     * @return string The initially constructed path.
     */
    private function buildInitialPathFothForLog(string $log_file_name, string $folder_name, string $pattern, string $realpath)
    {
        $request_log_pattern = '/^request-\d{4}-\d{2}-\d{2}\.log$/';

        if (preg_match($request_log_pattern, $log_file_name) && $log_file_name !== 'laravel.log') {
            return $this->storagePath . DIRECTORY_SEPARATOR . $log_file_name;
        }

        if ($log_file_name !== 'laravel.log' && strtolower($folder_name) == strtolower($log_file_name)) {
            return $realpath;
        }

        return $log_file_name == 'laravel.log'
        ? $this->storagePath . DIRECTORY_SEPARATOR . $log_file_name
        : $this->storagePath . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $log_file_name;
    }

    /**
     * Validate the constructed path or fallback to an alternate pattern path.
     *
     * @param string $path       The initially constructed path.
     * @param string $folder_name The derived folder name.
     * @param string $pattern    The extracted pattern from the file name.
     * @return string The validated or fallback path.
     */
    private function validateOrFallbackPath(string $path, string $folder_name, string $pattern)
    {
        if (!file_exists($path)) {
            return $this->storagePath . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $pattern;
        }
        return $path;
    }

    /**
     * Extract dates from files.
     *
     * @param  array  $files
     *
     * @return array
     */
    private function extractDates(array $files)
    {
        return array_map(function ($file) {
            return basename($file);
        }, $files);
    }
    /**
     * Get a list of log files from a specified folder, adjusting filenames as necessary.
     *
     * @param string $folder The folder to scan for log files.
     * @return array Sorted list of log file paths.
     */
    private function getFilesByFolder($folder)
    {
        // Create a slug from the folder name for comparison
        // $folderSlug = Str::slug(strtolower(basename($folder)), '_', null, ['-' => '-']);
        $folderSlug = preg_replace('/\s+/', '_', strtolower(basename($folder)));
        $base_name  = basename($folder);

        // Get all files in the folder
        $logFiles = File::files($folder);
        $files    = [];

        // Process each file in the folder
        foreach ($logFiles as $logFile) {
            // Only consider files with a '.log' extension
            if ($logFile->getExtension() === 'log') {
                // Add the processed filename to the list
                $files[] = $this->processLogFileName($logFile, $folderSlug, $base_name);
            }
        }

        // Sort file paths ignoring case
        sort($files, SORT_STRING | SORT_FLAG_CASE);

        return $files;
    }

    /**
     * Create a standardized filename for a log file based on its folder.
     *
     * @param SplFileInfo $logFile   The log file object.
     * @param string      $folderSlug The slug of the folder name.
     * @param string      $base_name  The base name of the folder.
     * @return string Log file path with adjusted filename if necessary.
     */
    private function processLogFileName($logFile, $folderSlug, $base_name)
    {
        $filename = $logFile->getFilename();

        // Check if filename already contains folder slug or base name
        if (strpos($filename, $folderSlug) === false && strpos($filename, $base_name) === false) {
            // Append the folder's base name if not already included
            return str_replace($logFile->getFilename(), $base_name . '_' . $logFile->getFilename(), $logFile->getPathname());
        } else {
            return $logFile->getPathname();
        }
    }
}
