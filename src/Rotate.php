<?php
namespace studio24\Rotate;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;

/**
 * Class to manage file rotation
 *
 * @package studio24\Rotate
 */
class Rotate
{
    use FilesTrait;

    /**
     * Number of copies to keep, defaults to 10
     *
     * @var int
     */
    protected $keepNumber = 10;

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
     * Set the number of old copies to keep
     *
     * @param $number
     */
    public function keep($number)
    {
        $this->keepNumber = $number;
    }

    /**
     * Run the file rotation
     *
     * @throws RotateException
     */
    public function run()
    {
        $dir = new DirectoryIterator($this->filenameFormat->getPath());
        foreach ($dir as $file) {
            if ($file->isFile() && $file->isMatch()) {
                for ($x = $this->keepNumber; $x--; $x > 0) {
                    if ($x === $this->keepNumber) {
                        if (!unlink($file->getPath() . '/' . $file->getRotatedFilename($x))) {
                            throw new RotateException('Cannot rotate file: ' . $file->getRotatedFilename($x));
                        }
                    } elseif ($x === 1) {
                        if (!rename($file->getPath() . '/' . $file->getBasename(), $file->getPath() . '/' . $this->getRotatedFilename(1))) {
                            throw new RotateException('Cannot rotate file: ' . $file->getBasename());
                        }
                    } else {
                        if (!rename($file->getPath() . '/' . $file->getRotatedFilename($x), $file->getPath() . '/' . $this->getRotatedFilename($x + 1))) {
                            throw new RotateException('Cannot rotate file: ' . $file->getRotatedFilename($x));
                        }
                    }
                }
            }
        }
    }

}