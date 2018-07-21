<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testAddServer()
    {
        $m = new Client;
        $resultAdd = $m->addServer('test.host', 80, 100);
        $expected = [[
            'host' => 'test.host',
            'port' => 80,
            'weight' => 100,
        ]];

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());

        $this->assertEquals(true, $resultAdd);
        $this->assertEquals($expected, $m->getServerList());
    }

    public function testAddServerDefaultArg()
    {
        $m = new Client;
        $m->addServer('test.host');
        $expected = [[
            'host' => 'test.host',
            'port' => $m::DEFAULT_PORT,
            'weight' => $m::DEFAULT_WEIGHT,
        ]];

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());

        $this->assertEquals($expected, $m->getServerList());
    }

    public function testAddTwoServer()
    {
        $m = new Client;
        $m->addServer('test.host');
        $m->addServer('test2.host');
        $serverList = $m->getServerList();

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());

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

        $this->assertEquals($m::MESSAGE_SERVER_DUPLICATE, $m->getResultMessage());
        $this->assertEquals($m::CODE_FAILURE, $m->getResultCode());

        $this->assertEquals(true, $resultAddOk);
        $this->assertEquals(false, $resultAddFail);
        $this->assertEquals(1, count($serverList));
    }

    public function testAddServers()
    {
        $servers = [
            ['mem.domain.com'],
            ['mem.domain.com'],        // duplicate
            ['mem1.domain.com', 11222, 20],
            ['mem2.domain.com', 11223, 20],
        ];
        $m = new Client;
        $result = $m->addServers($servers);
        $serverList = $m->getServerList();

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals(3, count($serverList));
        $this->assertEquals($m::DEFAULT_PORT, $serverList[0]['port']);
        $this->assertEquals($m::DEFAULT_WEIGHT, $serverList[0]['weight']);
    }

    public function testAddServersEmpty()
    {
        $m = new Client;
        $result = $m->addServers([]);
        $serverList = $m->getServerList();

        $this->assertEquals($m::MESSAGE_NOT_FOUND_SERVERS, $m->getResultMessage());
        $this->assertEquals($m::CODE_FAILURE, $m->getResultCode());

        $this->assertEquals(false, $result);
        $this->assertEquals(0, count($serverList));
    }
}