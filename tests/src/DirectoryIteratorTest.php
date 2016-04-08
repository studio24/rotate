<?php

use studio24\Rotate\FilenameFormat;
use studio24\Rotate\DirectoryIterator;

class DirectoryIteratorTest extends PHPUnit_Framework_TestCase {

    public function testIsMatch()
    {
        $it = new DirectoryIterator('tests/test-files/files');
        $it->setFilenameFormat(new FilenameFormat('*.pdf'));
        foreach ($it as $item) {
            if ($it->isFile()) {
                $this->assertEquals('studio24\Rotate\DirectoryIterator', get_class($item));
                $this->assertTrue($item->isMatch());
                return;
            }
        }
    }

    public function testHasDate()
    {
        $it = new DirectoryIterator('tests/test-files/logs');
        $it->setFilenameFormat(new FilenameFormat('payment.{Ymd}.log'));
        $this->assertTrue($it->hasDate());
    }

    public function testGetDate()
    {
        $it = new DirectoryIterator('tests/test-files/logs');
        $it->setFilenameFormat(new FilenameFormat('payment.{Ymd}.log'));
        foreach ($it as $item) {
            if ($it->isFile()) {
                $this->assertEquals(\DateTime::createFromFormat('Ymd', '20160324'), $item->getFilenameDate());
                return;
            }
        }
    }

}
