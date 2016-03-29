<?php

use studio24\Rotate\Filename;

class FilenameTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException studio24\Rotate\RotateException
     */
    public function testFolderDoesNotExist()
    {
        $dir = new Filename('invalid/path/file.jpg');
    }

    public function testFolder()
    {
        $dir = new Filename('tests/test-files/logs/file.jpg');
        $this->assertEquals('tests/test-files/logs', $dir->getPath());

        $dir = new Filename('tests/test-files/logs/file2.jpg');
        $this->assertNotEquals('tests/test-files/logs/file2.jpg', $dir->getPath());
    }

    public function testFilename()
    {
        $dir = new Filename('tests/test-files/logs/file.log');
        $this->assertEquals('file.log', $dir->getFilenamePattern());
        $this->assertEquals('/^file\.log$/U', $dir->getFilenameRegex());
        $this->assertNotEquals('tests/test-files/logs/file.log', $dir->getFilenameRegex());

        $dir = new Filename('tests/test-files/logs/*.log');
        $this->assertEquals('*.log', $dir->getFilenamePattern());
        $this->assertEquals('/^.+\.log$/U', $dir->getFilenameRegex());

        $dir = new Filename('tests/test-files/logs/payment.{Ymd}.log');
        $this->assertEquals('payment.{Ymd}.log', $dir->getFilenamePattern());
        $this->assertEquals('/^payment\.{([^}]+)}\.log$/U', $dir->getFilenameRegex());

        $dir = new Filename('tests/test-files/logs/payment.{Ymd}.log');
        $this->assertEquals('/^payment\.{([^}]+)}\.log$/U', $dir->getFilenameRegex());

        $dir = new Filename('tests/test-files/logs/payment.{Ymd}.test}.log');
        $this->assertEquals('/^payment\.{([^}]+)}\.test}\.log$/U', $dir->getFilenameRegex());
    }

    public function testHasDate()
    {
        $dir = new Filename('tests/test-files/logs/file.log');
        $this->assertNotTrue($dir->hasDateFormat());

        $dir = new Filename('tests/test-files/logs/*.log');
        $this->assertNotTrue($dir->hasDateFormat());

        $dir = new Filename('tests/test-files/logs/payment.{Ymd}.log');
        $this->assertTrue($dir->hasDateFormat());
    }

}