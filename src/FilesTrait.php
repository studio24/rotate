<?php
namespace studio24\Rotate;

trait FilesTrait 
{
    /**
     * The filename format we're matching against
     *
     * @var FilenameFormat
     */
    protected $filenameFormat;
    
    /**
     * Set the filename format we're matching
     *
     * @param $filenameFormat
     */
    public function setFilenameFormat($filenameFormat)
    {
        $this->filenameFormat = new FilenameFormat($filenameFormat);
    }

    /**
     * Return the filename format we're matching
     *
     * @return FilenameFormat
     */
    public function getFilenameFormat()
    {
        return $this->filenameFormat;
    }

}
