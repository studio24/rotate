<?php
namespace studio24\Rotate;

/**
 * Class to manage file deletion
 *
 * @package studio24\Rotate
 */
class Delete
{
    use FilesTrait;

    /**
     * Constructor
     *
     * @param string|null $filenameFormat
     */
    public function __construct ($filenameFormat = null)
    {
        if ($filenameFormat !== null) {
            $this->setFilenameFormat($filenameFormat);
        }
    }

    /**
     * Delete files over a certain time period
     *
     * @param mixed $timePeriod DateInterval object, or time interval supported by DateInterval::createFromDateString, e.g. 3 months
     */
    public function deleteTimePeriod($timePeriod)
    {
        if ($timePeriod instanceof DateInterval) {
            $interval = $timePeriod;
        } else {
            $interval = \DateInterval::createFromDateString($timePeriod);
        }

        $now = new \DateTime();
        $now = new \DateTime($now->format('Y-m-d') . ' 00:00:00');
        $oldestDate = $now->sub($interval);

        $dir = new DirectoryIterator($this->filenameFormat->getPath());
        foreach ($dir as $file) {
            if ($file->isFile() && $file->isMatch()) {
                if ($file->getFilenameDate() < $oldestDate) {
                    unlink($file->getPathname());
                }
            }
        }
    }

    public function deleteFilePattern($filePattern)
    {

    }

    public function deleteCallback(callback $callback)
    {

    }

}