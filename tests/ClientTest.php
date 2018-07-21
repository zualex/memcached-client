<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    const HOST = 'localhost';
    const PORT = 11211;

    public function testSetServer()
    {
        $m = new Client;
        $result = $m->setServer(self::HOST, self::PORT);

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());
        $this->assertEquals(true, $result);
        $this->assertEquals(['host' => self::HOST, 'port' => self::PORT], $m->getServer());
    }

    public function testSetServerFail()
    {
        $m = new Client;
        $result = $m->setServer(self::HOST, 223);

        $this->assertEquals('Connection refused (111)', $m->getResultMessage());
        $this->assertEquals($m::RES_CONNECTION_SOCKET_CREATE_FAILURE, $m->getResultCode());
        $this->assertEquals(false, $result);
    }

    public function testAddServerDefaultArg()
    {
        $m = new Client;
        $m->setServer(self::HOST);

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());
        $this->assertEquals(['host' => self::HOST, 'port' => $m::DEFAULT_PORT], $m->getServer());
    }

    public function testAddTwoServer()
    {
        $m = new Client;
        $m->setServer(self::HOST);
        $m->setServer('127.0.0.1');

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());
        $this->assertEquals('127.0.0.1', $m->getServer()['host']);
    }
}