<?php

use studio24\Rotate\Rotate;
use studio24\Rotate\Delete;

class RotateTest extends PHPUnit_Framework_TestCase
{
    protected $dir;

    protected function setUp()
    {
        // Copy logs to temp location
        $from = 'tests/test-files';
        $to = 'tests/tmp';
        $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_SELF));
        foreach ($dir as $path => $item) {
            if ($item->isFile() && $item->getFilename() != '.gitkeep') {
                if (!is_dir($to . '/' . $item->getSubPath())) {
                    mkdir($to . '/' . $item->getSubPath(), 0777, true);
                }
                if (!file_exists($to . '/' . $item->getSubPathName())) {
                    copy($path, $to . '/' . $item->getSubPathName());
                }
            }
        }

        $this->dir = realpath(__DIR__ . '/../tmp');
        if ($this->dir === false) {
            throw new Exception("Cannot determine directory path");
        }
    }

    public static function tearDownAfterClass()
    {
        // Clean up temp files
        $folder = 'tests/tmp';
        $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder));
        foreach ($dir as $path => $file) {
            if ($file->isFile() && $file->getFilename() != '.gitkeep') {
                unlink($path);
            }
        }
    }

    public function testRotateDryRun()
    {
        $oldDir = getcwd();
        chdir($this->dir . '/rotate');

        $rotate = new Rotate('orders.log');
        $rotate->keep(3);
        $rotate->setDryRun(true);

        $files = $rotate->run();
        $this->assertEquals(['./orders.log'], $files);

        chdir($this->dir . '/rotate2');

        $rotate = new Rotate('orders.log');
        $rotate->keep(3);
        $rotate->setDryRun(true);

        $files = $rotate->run();
        $this->assertEquals([
            './orders.log.2',
            './orders.log.1',
            './orders.log'
        ], $files);

        chdir($oldDir);
    }

    public function testRotate()
    {
        $oldDir = getcwd();
        chdir($this->dir . '/rotate');

        $rotate = new Rotate('orders.log');
        $rotate->keep(3);

        $this->assertFalse(file_exists('orders.log.1'));

        $rotate->run();
        $this->assertFalse(file_exists('orders.log'));
        $this->assertTrue(file_exists('orders.log.1'));
        $this->assertFalse(file_exists('orders.log.2'));

        touch('orders.log');
        $rotate->run();
        $this->assertFalse(file_exists('orders.log'));
        $this->assertTrue(file_exists('orders.log.1'));
        $this->assertTrue(file_exists('orders.log.2'));
        $this->assertFalse(file_exists('orders.log.3'));

        touch('orders.log');
        $rotate->run();
        $this->assertFalse(file_exists('orders.log'));
        $this->assertTrue(file_exists('orders.log.1'));
        $this->assertTrue(file_exists('orders.log.2'));
        $this->assertTrue(file_exists('orders.log.3'));
        $this->assertFalse(file_exists('orders.log.4'));

        chdir($oldDir);
    }

    public function testRotateSize()
    {
        $oldDir = getcwd();
        chdir($this->dir . '/size');

        $rotate = new Rotate('test-size-a.log');
        $rotate->size('150KB');
        $files = $rotate->run();
        $this->assertEmpty($files);
        $this->assertFalse(file_exists('test-size-a.log.1'));

        $rotate = new Rotate('test-size-b.log');
        $rotate->size('150KB');
        $rotate->run();
        $this->assertTrue(file_exists('test-size-b.log.1'));

        chdir($oldDir);
    }

    public function testDeleteByFilenameTime()
    {
        $oldDir = getcwd();
        chdir($this->dir . '/logs');

        $rotate = new Delete('payment.{Ymd}.log');
        $rotate->setNow(new DateTime('2016-04-07 00:00:00'));
        $files = $rotate->deleteByFilenameTime('1 month');
        $this->assertEmpty($files);

        $rotate->setNow(new DateTime('2016-04-26 00:00:00'));
        $files = $rotate->deleteByFilenameTime('1 month');
        $this->assertEquals([
            './payment.20160324.log',
            './payment.20160325.log'
        ], $files);

        chdir($oldDir);
    }

    /*
    public function testDeleteByFileModifiedDate()
    {

    }

    public function testDeleteByCallback()
    {

    }
    */
}