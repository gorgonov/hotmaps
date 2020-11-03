<?php

namespace tests;

require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use services\LogService;

class LogServiceTest extends TestCase
{
    public function testErrorWithTitle()
    {
        $logger = new LogService();
        $fileName = $_SERVER['DOCUMENT_ROOT'] . "/" . $logger::FILENAME;
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        $randomMess = bin2hex(random_bytes(20));
        $randomTitle = bin2hex(random_bytes(20));
        $logger->error($randomMess, $randomTitle);
        $sFile = file_get_contents($fileName);
        $this->assertTrue(strrpos($sFile, $randomMess) !== false);
        $this->assertTrue(strrpos($sFile, $randomTitle) !== false);
    }

    public function testErrorNoTitle()
    {
        $logger = new LogService();
        $fileName = $_SERVER['DOCUMENT_ROOT'] . "/" . $logger::FILENAME;
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        $randomMess = bin2hex(random_bytes(20));
        $logger->error($randomMess);
        $sFile = file_get_contents($fileName);
        $this->assertTrue(strrpos($sFile, $randomMess) !== false);
    }
}
