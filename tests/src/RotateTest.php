<?php

use studio24\Rotate\Rotate;

class RotateTest extends PHPUnit_Framework_TestCase {

    public function testSettingFiles()
    {
        $rotate = new Rotate('path/to/pattern.log');
        $this->assertEquals('path/to/pattern.log', $rotate->getFiles());
    }
    
}