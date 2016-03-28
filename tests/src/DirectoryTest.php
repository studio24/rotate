<?php

use studio24\Rotate\Directory;

class DirectoryTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException studio24\Rotate\RotateException
     */
    public function testInvalidFolder()
    {
        $dir = new Directory('invalid/path/file.jpg');
    }

    public function testFolder()
    {
        $dir = new Directory('tests/test-files/logs/file.jpg');
        $this->assertEquals('tests/test-files/logs', $dir->getPath());
    }

    public function testFilenameRegex()
    {
        $dir = new Directory('tests/test-files/logs/file.log');
        $this->assertEquals('file.log', $dir->getFilenamePattern());
        $this->assertEquals('/^file\.log$/U', $dir->getFilenameRegex());
        $this->assertNotEquals('tests/test-files/logs/file.log', $dir->getFilenameRegex());

        $dir = new Directory('tests/test-files/logs/*.log');
        $this->assertEquals('*.log', $dir->getFilenamePattern());
        $this->assertEquals('/^(.+)\.log$/U', $dir->getFilenameRegex());

        $dir = new Directory('tests/test-files/logs/payment.YYYYMMDD.log');
        $this->assertEquals('payment.YYYYMMDD.log', $dir->getFilenamePattern());
        $this->assertEquals('/^payment\.(\d{4})(\d{2})(\d{2})\.log$/U', $dir->getFilenameRegex());
    }

}