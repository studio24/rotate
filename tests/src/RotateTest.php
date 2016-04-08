<?php

use studio24\Rotate\Rotate;
use studio24\Rotate\Delete;
use studio24\Rotate\DirectoryIterator;

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
        $this->assertFalse(file_exists('orders.log.1'));

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

    public function testRotate2()
    {
        $oldDir = getcwd();
        chdir($this->dir . '/rotate2');

        $rotate = new Rotate('orders.log');
        $rotate->keep(3);

        $rotate->run();
        $this->assertFalse(file_exists('orders.log'));
        $this->assertTrue(file_exists('orders.log.1'));
        $this->assertTrue(file_exists('orders.log.2'));
        $this->assertTrue(file_exists('orders.log.3'));

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
        $rotate->size('25KB');
        $files = $rotate->run();
        $this->assertEmpty($files);
        $this->assertFalse(file_exists('test-size-a.log.1'));

        $rotate = new Rotate('test-size-b.log');
        $rotate->size('25KB');
        $rotate->run();
        $this->assertTrue(file_exists('test-size-b.log.1'));

        chdir($oldDir);
    }

    public function testDeleteByFilenameTime()
    {
        $oldDir = getcwd();
        chdir($this->dir . '/logs');

        $rotate = new Delete('payment.{Ymd}.log');
        $rotate->setDryRun(true);
        $rotate->setNow(new DateTime('2016-04-07 00:00:00'));
        $files = $rotate->deleteByFilenameTime('1 month');
        $this->assertEmpty($files);

        $rotate->setNow(new DateTime('2016-04-26 00:00:00'));
        $files = $rotate->deleteByFilenameTime('1 month');
        $this->assertEquals([
            './payment.20160324.log',
            './payment.20160325.log'
        ], $files);

        $rotate->setNow(new DateTime('2016-04-08 00:00:00'));
        $files = $rotate->deleteByFilenameTime(new DateInterval('P7D'));
        $this->assertEquals([
            './payment.20160324.log',
            './payment.20160325.log',
            './payment.20160326.log',
            './payment.20160331.log'
        ], $files);

        $rotate->setDryRun(false);
        $rotate->setNow(new DateTime('2016-04-26 00:00:00'));
        $rotate->deleteByFilenameTime('1 month');
        $this->assertFalse(file_exists('payment.20160324.log'));
        $this->assertTrue(file_exists('payment.20160401.log'));

        chdir($oldDir);
    }


    public function testDeleteByFileModifiedDate()
    {
        $oldDir = getcwd();
        if (!is_dir($this->dir . '/logs/time')) {
           mkdir($this->dir . '/logs/time');
        }
        chdir($this->dir . '/logs/time');

        touch('test1.log', strtotime('2016-03-01 12:00:00'));
        touch('test2.log', strtotime('2016-03-02 12:00:00'));
        touch('test3.log', strtotime('2016-03-03 12:00:00'));
        touch('test4.log', strtotime('2016-03-10 12:00:00'));
        touch('test5.log', strtotime('2016-03-10 12:00:00'));

        $rotate = new Delete('test*.log');
        $rotate->setDryRun(true);
        $rotate->setNow(new DateTime('2016-03-09 00:00:00'));
        $files = $rotate->deleteByFileModifiedDate('7 days');
        $this->assertEquals([
            './test1.log'
        ], $files);

        $rotate->setNow(new DateTime('2016-03-09 13:00:00'));
        $files = $rotate->deleteByFileModifiedDate('7 days');
        $this->assertEquals([
            './test1.log',
            './test2.log'
        ], $files);

        $rotate->setDryRun(false);
        $rotate->setNow(new DateTime('2016-03-14 12:00:00'));
        $files = $rotate->deleteByFileModifiedDate('7 days');
        $this->assertEquals([
            './test1.log',
            './test2.log',
            './test3.log'
        ], $files);
        $this->assertFalse(file_exists('test1.log'));
        $this->assertTrue(file_exists('test4.log'));

        chdir($oldDir);
    }

    public function testDeleteByCallback()
    {
        $oldDir = getcwd();
        chdir($this->dir . '/files');

        $rotate = new Delete('*');
        $files = $rotate->deleteByCallback(function(DirectoryIterator $file) {
            if ($file->getExtension() == 'pdf' && $file->getBasename() > 2) {
                return true;
            }
            return false;
        });
        $this->assertEquals([
            './3.pdf'
        ], $files);

        chdir($oldDir);
    }

}