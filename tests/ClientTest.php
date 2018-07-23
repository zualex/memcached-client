<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    const HOST = 'localhost';
    const PORT = 11211;
    const KEY_TEST = 'my_key';

    protected function getObject()
    {
        $m = new Client;
        $m->setServer(self::HOST, self::PORT);

        return $m;
    }

    protected function tearDown()
    {
        $m = $this->getObject();
        $m->delete(self::KEY_TEST);
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
        $result = $m->set(self::KEY_TEST, 'my_value');

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals('my_value', $m->get(self::KEY_TEST));
    }

    public function testSetArray()
    {
        $m = $this->getObject();
        $result = $m->set(self::KEY_TEST, ['key' => 1]);

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals(['key' => 1], $m->get(self::KEY_TEST));
    }

    public function testGetNotExistKey()
    {
        $m = $this->getObject();
        $result = $m->get(self::KEY_TEST);

        $this->assertEquals($m::MESSAGE_KEY_NOT_FOUND, $m->getResultMessage());
        $this->assertEquals($m::RES_NOTFOUND, $m->getResultCode());
        $this->assertEquals(false, $result);
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

    public function testDelete()
    {
        $m = $this->getObject();
        $m->set(self::KEY_TEST, 'my_value');
        $result = $m->delete(self::KEY_TEST);

        $this->assertEquals($m::MESSAGE_NOTHING, $m->getResultMessage());
        $this->assertEquals($m::RES_SUCCESS, $m->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals(null, $m->get(self::KEY_TEST));
    }

    public function testDeleteFail()
    {
        $m = $this->getObject();
        $result = $m->delete(self::KEY_TEST);

        $this->assertEquals($m::MESSAGE_DELETE_FAIL, $m->getResultMessage());
        $this->assertEquals($m::RES_FAILURE, $m->getResultCode());

        $this->assertEquals(false, $result);
    }
}
