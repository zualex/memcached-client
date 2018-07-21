<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    const DEFAULT_PORT = 11211;
    const DEFAULT_WEIGHT = 0;

    public function testAddServer()
    {
        $m = new Client;
        $resultAdd = $m->addServer('test.host', 80, 100);
        $expected = [[
            'host' => 'test.host',
            'port' => 80,
            'weight' => 100,
        ]];

        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());
        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());

        $this->assertEquals(true, $resultAdd);
        $this->assertEquals($expected, $m->getServerList());
    }

    public function testAddServerDefaultArg()
    {
        $m = new Client;
        $m->addServer('test.host');
        $expected = [[
            'host' => 'test.host',
            'port' => self::DEFAULT_PORT,
            'weight' => self::DEFAULT_WEIGHT,
        ]];

        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());
        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());

        $this->assertEquals($expected, $m->getServerList());
    }

    public function testAddTwoServer()
    {
        $m = new Client;
        $m->addServer('test.host');
        $m->addServer('test2.host');
        $serverList = $m->getServerList();

        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());
        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());

        $this->assertEquals(2, count($serverList));
        $this->assertEquals('test.host', $serverList[0]['host']);
        $this->assertEquals('test2.host', $serverList[1]['host']);
    }

    public function testAddDuplicateServer()
    {
        $m = new Client;
        $resultAddOk = $m->addServer('test.host');
        $resultAddFail = $m->addServer('test.host');
        $serverList = $m->getServerList();

        $this->assertEquals($m::CODE_FAILURE, $m->getResultCode());
        $this->assertEquals($m::MESSAGE_SERVER_DUPLICATE, $m->getResultMessage());

        $this->assertEquals(true, $resultAddOk);
        $this->assertEquals(false, $resultAddFail);
        $this->assertEquals(1, count($serverList));
    }
}