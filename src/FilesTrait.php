<?php
namespace studio24\Rotate;

trait FilesTrait 
{
    protected $files;

    public function __construct ($files = null)
    {
        if ($files !== null) {
            $this->setFiles($files);
        }
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function getFiles()
    {
        return $this->files;
    }

}
