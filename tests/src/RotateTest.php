<?php

use studio24\Rotate\Rotate;
use studio24\Rotate\FilenameFormat;

class RotateTest extends PHPUnit_Framework_TestCase {

    public function testFileFormat()
    {
        $rotate = new Rotate('tests/test-files/orders.log');
        $this->assertEquals(new FilenameFormat('tests/test-files/orders.log'), $rotate->getFilenameFormat());
    }
    
}