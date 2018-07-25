<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Connection;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    const HOST = 'localhost';
    const PORT = 11211;

    public function testWrite()
    {
        $conn = new Connection(self::HOST, self::PORT);

        $this->assertEquals(strlen('get my_key'.Connection::END_LINE), $conn->write('get my_key'));
    }

    public function testReadLine()
    {
        $conn = new Connection(self::HOST, self::PORT);

        $conn->write('get my_key');
        $this->assertEquals('END', $conn->readLine());
    }
}
