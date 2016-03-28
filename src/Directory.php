<?php
namespace studio24\Rotate;

class Directory
{

    /**
     * Path to files to rotate / delete
     *
     * @var string
     */
    protected $path;

    /**
     * Filename pattern of files to rotate / delete
     *
     * @var string
     */
    protected $filenamePattern;


    /**
     * Filename regex pattern to match files
     *
     * @var string
     */
    protected $filenameRegex;

    /**
     * Constructor
     *
     * @param string $files Path to files to rotate or delete, filename part can contain patterns
     * @throws RotateException
     */
    public function __construct($files)
    {
        $this->path = dirname($files);
        if (!is_dir($this->path) || !is_readable($this->path)) {
            throw new RotateException("Directory path does not exist or is not readable at: " . strip_tags($files));
        }

        $this->filenamePattern = basename($files);
        $this->filenameRegex = $this->extractRegex($this->filenamePattern);
    }

    /**
     * Extract regex pattern from filename pattern
     *
     * * matches any string, for example _*.log_ matches all files ending .log
     * YYYYMMDD = matches time segment in a file, for example _order.YYYYMMDD.log_ matches a file in the format order.20160401.log
     *
     * The following time segments are supported:
     *
     * YYYY = 4 digit year (e.g. 2016)
     * MM = 2 digit month (e.g. 03)
     * DD = 2 digit day (e.g. 01)
     * hh = 2 digit hour (e.g. 12 or 15)
     * mm = 2 digit minutes (e.g. 30)
     * ss = 2 digit seconds (e.g. 25)
     * W = 1-2 digit Week number (e.g. 5 or 12)
     *
     * @param string $filename
     * @return string Regex pattern for matching files (with the ungreedy modifier set)
     * @throws RotateException
     */
    public function extractRegex($filename)
    {
        if (strpos('/', $filename) !== false) {
            throw new RotateException("Filename part cannot contain '/' character");
        }

        $escape = [
            '/\./'     => '\.',
            '/\+/'     => '\+',
            '/\:/'     => '\:',
            '/\-/'     => '\-'
        ];
        $pattern = preg_replace(array_keys($escape), array_values($escape), $filename);

        $replacements = [
            '/\*/'     => '(.+)',
            '/YYYY/'  => '(\d{4})',
            '/MM/'    => '(\d{2})',
            '/DD/'    => '(\d{2})',
            '/hh/'    => '(\d{2})',
            '/mm/'    => '(\d{2})',
            '/ss/'    => '(\d{2})',
            '/W/'     => '(\d{1,2})'
        ];
        $pattern = preg_replace(array_keys($replacements), array_values($replacements), $pattern);

        return '/^' . $pattern . '$/U';
    }

    /**
     * Return path to folder we're looking for files in
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return filename pattern to match files
     *
     * @return string
     */
    public function getFilenamePattern()
    {
        return $this->filenamePattern;
    }

    /**
     * Return regex to match files
     *
     * @return string
     */
    public function getFilenameRegex()
    {
        return $this->filenameRegex;
    }

    public function match($filename, $pattern)
    {

    }




}