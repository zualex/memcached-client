<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testSetServer()
    {
        $m = new Client;
        $resultAdd = $m->setServer('test.host', 80);

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());

        $this->assertEquals(true, $resultAdd);
        $this->assertEquals(['host' => 'test.host', 'port' => 80], $m->getServer());
    }

    public function testAddServerDefaultArg()
    {
        $m = new Client;
        $m->setServer('test.host');

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());

        $this->assertEquals(['host' => 'test.host', 'port' => $m::DEFAULT_PORT], $m->getServer());
    }

    public function testAddTwoServer()
    {
        $m = new Client;
        $m->setServer('test.host');
        $m->setServer('test2.host');

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::CODE_SUCCESS, $m->getResultCode());

        $this->assertEquals('test2.host', $m->getServer()['host']);
    }
}