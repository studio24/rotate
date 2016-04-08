<?php
namespace studio24\Rotate;

use DateTime, DateInterval;

/**
 * Class to manage file deletion
 *
 * @package studio24\Rotate
 */
class Delete extends RotateAbstract
{
    /**
     * Current date & time for file comparison purposes
     *
     * @var DateTime
     */
    protected $now;

    /**
     * Set the current date & time
     *
     * @param DateTime $dateTime
     */
    public function setNow(DateTime $dateTime)
    {
        $this->now = $dateTime;
    }

    /**
     * Return current time
     *
     * Defaults to current date, midnight
     *
     * @return DateTime
     */
    public function getNow()
    {
        if (!$this->now instanceof DateTime) {
            $now = new DateTime();
            $this->now = new DateTime($now->format('Y-m-d') . ' 00:00:00');
        }

        return $this->now;
    }

    /**
     * Delete files by the filename time stored within the filename itself
     *
     * For example delete all files with a filename date pattern of payment.2016-01-01.log over 3 months old
     *
     * @param mixed $timePeriod DateInterval object, or time interval supported by DateInterval::createFromDateString, e.g. 3 months
     * @return array Array of deleted files
     * @throws FilenameFormatException
     * @throws RotateException
     */
    public function deleteByFilenameTime($timePeriod)
    {
        if (!$this->hasFilenameFormat()) {
            throw new FilenameFormatException('You must set a filename format to match files against');
        }

        $deleted = [];

        if ($timePeriod instanceof DateInterval) {
            $interval = $timePeriod;
        } else {
            $interval = DateInterval::createFromDateString($timePeriod);
        }

        $oldestDate = clone $this->now;
        $oldestDate = $oldestDate->sub($interval);

        $dir = new DirectoryIterator($this->getFilenameFormat()->getPath());
        $dir->setFilenameFormat($this->getFilenameFormat());
        foreach ($dir as $file) {
            if ($file->isFile() && $file->isMatch()) {
                if ($file->getFilenameDate() < $oldestDate) {
                    if (!$this->isDryRun()) {
                        if (!unlink($file->getPathname())) {
                            throw new RotateException('Cannot delete file: ' . $file->getPathname());
                        }
                    }
                    $deleted[] = $file->getPathname();
                }
            }
        }

        return $deleted;
    }

    /**
     * Delete files by the last modified time of the filename
     *
     * For example delete all files over 3 months old.
     *
     * @param mixed $timePeriod DateInterval object, or time interval supported by DateInterval::createFromDateString, e.g. 3 months
     * @return array Array of deleted files
     * @throws FilenameFormatException
     * @throws RotateException
     */
    public function deleteByFileModifiedDate($timePeriod)
    {
        if (!$this->hasFilenameFormat()) {
            throw new FilenameFormatException('You must set a filename format to match files against');
        }

        $deleted = [];

        if ($timePeriod instanceof DateInterval) {
            $interval = $timePeriod;
        } else {
            $interval = DateInterval::createFromDateString($timePeriod);
        }

        $oldestDate = clone $this->now;
        $oldestDate = $oldestDate->sub($interval);

        $dir = new DirectoryIterator($this->getFilenameFormat()->getPath());
        $dir->setFilenameFormat($this->getFilenameFormat());
        foreach ($dir as $file) {
            if ($file->isFile() && $file->isMatch()) {
                $fileDate = new DateTime();
                $fileDate->setTimestamp($file->getMTime());
                if ($fileDate < $oldestDate) {
                    if (!$this->isDryRun()) {
                        if (!unlink($file->getPathname())) {
                            throw new RotateException('Cannot delete file: ' . $file->getPathname());
                        }
                    }
                    $deleted[] = $file->getPathname();
                }
            }
        }

        return $deleted;
    }

    /**
     * Delete files by a custom callback function
     *
     * For example, do a database lookup on the order ID stored within the filename to ensure the order has been completed, if so delete the file.
     *
     * Callback function must accept one parameter (DirectoryIterator $file) and must return true (delete file) or false (do not delete file)
     *
     * Example to delete all files over 5Mb in size:
     * $callback = function(DirectoryIterator $file) {
     *     if ($file->getSize() > 5 * 1024 * 1024) {
     *         return true;
     *     } else {
     *         return false;
     *     }
     * }
     *
     * @param callable $callback
     * @return array Array of deleted files
     * @throws FilenameFormatException
     * @throws RotateException
     */
    public function deleteByCallback(callable $callback)
    {
        if (!$this->hasFilenameFormat()) {
            throw new FilenameFormatException('You must set a filename format to match files against');
        }

        $deleted = [];

        $dir = new DirectoryIterator($this->getFilenameFormat()->getPath());
        $dir->setFilenameFormat($this->getFilenameFormat());
        foreach ($dir as $file) {
            if ($file->isFile() && $file->isMatch()) {
                if ($callback($file)) {
                    if (!$this->isDryRun()) {
                        if (!unlink($file->getPathname())) {
                            throw new RotateException('Cannot delete file: ' . $file->getPathname());
                        }
                    }
                    $deleted[] = $file->getPathname();
                }
            }
        }

        return $deleted;
    }

}