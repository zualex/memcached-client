<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    const HOST = 'localhost';
    const PORT = 11211;

    protected function getObject()
    {
        $m = new Client;
        $m->setServer(self::HOST, self::PORT);

        return $m;
    }

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
        $m = $this->getObject();
        $m->setServer('127.0.0.1');

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());
        $this->assertEquals('127.0.0.1', $m->getServer()['host']);
    }

    public function testSet()
    {
        $m = $this->getObject();
        $result = $m->set('my_key', 'my_value');

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals('my_value', $m->get('my_key'));
    }

    public function testSetArray()
    {
        $m = $this->getObject();
        $result = $m->set('my_key', ['key' => 1]);

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals(['key' => 1], $m->get('my_key'));
    }

    public function testKeyMaxLength()
    {
        $m = $this->getObject();

        $result = $m->set(str_repeat('a', $m::DEFAULT_MAX_KEY_LENGTH), 'max length 250');
        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());
        $this->assertEquals(true, $result);

        $result = $m->set(str_repeat('a', $m::DEFAULT_MAX_KEY_LENGTH + 1), 'max length 250');
        $this->assertEquals($m::MESSAGE_KEY_MAX_LENGTH, $m->getResultMessage());
        $this->assertEquals($m::RES_BAD_KEY_PROVIDED, $m->getResultCode());
        $this->assertEquals(false, $result);
    }

    public function testKeyFail()
    {
        $m = $this->getObject();

        $result = $m->set('my key', 'space not allowed');
        $this->assertEquals($m::MESSAGE_KEY_BAD_CHARS, $m->getResultMessage());
        $this->assertEquals($m::RES_BAD_KEY_PROVIDED, $m->getResultCode());
        $this->assertEquals(false, $result);

        $result = $m->set(['array'], 'array not allowed');
        $this->assertEquals($m::MESSAGE_KEY_NOT_STRING, $m->getResultMessage());
        $this->assertEquals($m::RES_BAD_KEY_PROVIDED, $m->getResultCode());
        $this->assertEquals(false, $result);

        $result = $m->set('my+key', 'not allowed');
        $this->assertEquals($m::MESSAGE_KEY_BAD_CHARS, $m->getResultMessage());
        $this->assertEquals($m::RES_BAD_KEY_PROVIDED, $m->getResultCode());
        $this->assertEquals(false, $result);
    }
}