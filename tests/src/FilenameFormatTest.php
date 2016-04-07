<?php

use studio24\Rotate\FilenameFormat;

class FilenameFormatTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException studio24\Rotate\FilenameFormatException
     */
    public function testFolderDoesNotExist()
    {
        $dir = new FilenameFormat('invalid/path/file.jpg');
    }

    public function testFolder()
    {
        $dir = new FilenameFormat('tests/test-files/logs/file.jpg');
        $this->assertEquals('tests/test-files/logs', $dir->getPath());

        $dir = new FilenameFormat('tests/test-files/logs/file2.jpg');
        $this->assertNotEquals('tests/test-files/logs/file2.jpg', $dir->getPath());
    }

    public function testFilename()
    {
        $dir = new FilenameFormat('tests/test-files/logs/file.log');
        $this->assertEquals('file.log', $dir->getFilenamePattern());
        $this->assertEquals('/^file\.log$/', $dir->getFilenameRegex());
        $this->assertNotEquals('tests/test-files/logs/file.log', $dir->getFilenameRegex());

        $dir = new FilenameFormat('tests/test-files/logs/*.log');
        $this->assertEquals('*.log', $dir->getFilenamePattern());
        $this->assertEquals('/^.+\.log$/', $dir->getFilenameRegex());

        $dir = new FilenameFormat('tests/test-files/logs/payment.{Ymd}.log');
        $this->assertEquals('payment.{Ymd}.log', $dir->getFilenamePattern());
        $this->assertEquals('/^payment\.([^.]+)\.log$/', $dir->getFilenameRegex());

        $dir = new FilenameFormat('tests/test-files/logs/payment.{Ymd}.log');
        $this->assertEquals('/^payment\.([^.]+)\.log$/', $dir->getFilenameRegex());

        $dir = new FilenameFormat('tests/test-files/logs/payment.{Ymd}.test}.log');
        $this->assertEquals('/^payment\.([^.]+)\.test}\.log$/', $dir->getFilenameRegex());
    }

    public function testHasDate()
    {
        $dir = new FilenameFormat('tests/test-files/logs/file.log');
        $this->assertNotTrue($dir->hasDateFormat());

        $dir = new FilenameFormat('tests/test-files/logs/*.log');
        $this->assertNotTrue($dir->hasDateFormat());

        $dir = new FilenameFormat('tests/test-files/logs/payment.{Ymd}.log');
        $this->assertTrue($dir->hasDateFormat());
    }

    public function testDateFormat()
    {
        $dir = new FilenameFormat('tests/test-files/logs/payment.{Ymd}.log');
        $this->assertEquals('Ymd', $dir->getDateFormat());

        $dir = new FilenameFormat('tests/test-files/logs/payment.{U}.log');
        $this->assertEquals('U', $dir->getDateFormat());
    }

    /**
     * @expectedException studio24\Rotate\FilenameFormatException
     */
    public function testInvalidDateFormat()
    {
        $dir = new FilenameFormat('tests/test-files/logs/payment.{woo}.log');
    }

}