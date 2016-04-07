<?php

use studio24\Rotate\Rotate;
use studio24\Rotate\FilenameFormat;

class RotateSetupTest extends PHPUnit_Framework_TestCase
{

    public function testFileFormat()
    {
        $rotate = new Rotate('tests/test-files/orders.log');
        $this->assertEquals(new FilenameFormat('tests/test-files/orders.log'), $rotate->getFilenameFormat());
    }

    public function testKeep()
    {
        $rotate = new Rotate('tests/test-files/orders.log');
        $this->assertEquals(10, $rotate->getKeepNumber());

        $rotate->keep(20);
        $this->assertEquals(20, $rotate->getKeepNumber());
    }

    public function testSize()
    {
        $rotate = new Rotate('tests/test-files/orders.log');
        $this->assertFalse($rotate->hasSizeToRotate());

        $rotate->size('500 B');
        $this->assertEquals(500, $rotate->getSizeToRotate());

        $rotate->size('10KB');
        $this->assertEquals(10240, $rotate->getSizeToRotate());

        $rotate->size('10 mb');
        $this->assertEquals(10485760, $rotate->getSizeToRotate());

        $rotate->size('2gb');
        $this->assertEquals(2147483648, $rotate->getSizeToRotate());
    }

    /**
     * @expectedException studio24\Rotate\RotateException
     */
    public function testInvalidSize()
    {
        $rotate = new Rotate('tests/test-files/orders.log');
        $rotate->size('10');
    }

}