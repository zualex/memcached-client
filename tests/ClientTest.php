<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Client;
use zualex\Memcached\Connection;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    const HOST = 'localhost';
    const PORT = 11211;
    const KEY_TEST = 'my_key';

    protected function getObject()
    {
        $memcached = new Client();
        $memcached->setServer(self::HOST, self::PORT);

        return $memcached;
    }

    protected function tearDown()
    {
        $memcached = $this->getObject();
        $memcached->delete(self::KEY_TEST);
    }

    public function testSetServer()
    {
        $memcached = new Client;
        $result = $memcached->setServer(self::HOST, self::PORT);

        $this->assertEquals($memcached::MESSAGE_NOTHING, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_SUCCESS, $memcached->getResultCode());
        $this->assertEquals(true, $result);
        $this->assertEquals(['host' => self::HOST, 'port' => self::PORT], $memcached->getServer());
    }

    public function testSetServerFail()
    {
        $memcached = new Client;
        $result = $memcached->setServer(self::HOST, 223);

        $this->assertEquals('Connection refused (111)', $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_CONNECTION_SOCKET_CREATE_FAILURE, $memcached->getResultCode());
        $this->assertEquals(false, $result);
    }

    public function testAddServerDefaultArg()
    {
        $memcached = new Client;
        $memcached->setServer(self::HOST);

        $this->assertEquals($memcached::MESSAGE_NOTHING, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_SUCCESS, $memcached->getResultCode());
        $this->assertEquals(['host' => self::HOST, 'port' => $memcached::DEFAULT_PORT], $memcached->getServer());
    }

    public function testAddTwoServer()
    {
        $memcached = $this->getObject();
        $memcached->setServer('127.0.0.1');

        $this->assertEquals($memcached::MESSAGE_NOTHING, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_SUCCESS, $memcached->getResultCode());
        $this->assertEquals('127.0.0.1', $memcached->getServer()['host']);
    }

    public function testSet()
    {
        $memcached = $this->getObject();
        $result = $memcached->set(self::KEY_TEST, 'my_value');

        $this->assertEquals($memcached::MESSAGE_NOTHING, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_SUCCESS, $memcached->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals('my_value', $memcached->get(self::KEY_TEST));
    }

    public function testSetArray()
    {
        $memcached = $this->getObject();
        $result = $memcached->set(self::KEY_TEST, ['key' => 1]);

        $this->assertEquals($memcached::MESSAGE_NOTHING, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_SUCCESS, $memcached->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals(['key' => 1], $memcached->get(self::KEY_TEST));
    }

    public function testGetNotExistKey()
    {
        $memcached = $this->getObject();
        $result = $memcached->get(self::KEY_TEST);

        $this->assertEquals($memcached::MESSAGE_KEY_NOT_FOUND, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_NOTFOUND, $memcached->getResultCode());
        $this->assertEquals(false, $result);
    }

    public function testKeyMaxLength()
    {
        $memcached = $this->getObject();

        $result = $memcached->set(str_repeat('a', $memcached::DEFAULT_MAX_KEY_LENGTH), 'max length 250');
        $this->assertEquals($memcached::MESSAGE_NOTHING, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_SUCCESS, $memcached->getResultCode());
        $this->assertEquals(true, $result);

        $result = $memcached->set(str_repeat('a', $memcached::DEFAULT_MAX_KEY_LENGTH + 1), 'max length 250');
        $this->assertEquals($memcached::MESSAGE_KEY_MAX_LENGTH, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_BAD_KEY_PROVIDED, $memcached->getResultCode());
        $this->assertEquals(false, $result);
    }

    public function testKeyFail()
    {
        $memcached = $this->getObject();

        $result = $memcached->set('my key', 'space not allowed');
        $this->assertEquals($memcached::MESSAGE_KEY_BAD_CHARS, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_BAD_KEY_PROVIDED, $memcached->getResultCode());
        $this->assertEquals(false, $result);

        $result = $memcached->set(['array'], 'array not allowed');
        $this->assertEquals($memcached::MESSAGE_KEY_NOT_STRING, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_BAD_KEY_PROVIDED, $memcached->getResultCode());
        $this->assertEquals(false, $result);

        $result = $memcached->set('my+key', 'not allowed');
        $this->assertEquals($memcached::MESSAGE_KEY_BAD_CHARS, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_BAD_KEY_PROVIDED, $memcached->getResultCode());
        $this->assertEquals(false, $result);
    }

    public function testDelete()
    {
        $memcached = $this->getObject();
        $memcached->set(self::KEY_TEST, 'my_value');
        $result = $memcached->delete(self::KEY_TEST);

        $this->assertEquals($memcached::MESSAGE_NOTHING, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_SUCCESS, $memcached->getResultCode());

        $this->assertEquals(true, $result);
        $this->assertEquals(null, $memcached->get(self::KEY_TEST));
    }

    public function testDeleteFail()
    {
        $memcached = $this->getObject();
        $result = $memcached->delete(self::KEY_TEST);

        $this->assertEquals($memcached::MESSAGE_DELETE_FAIL, $memcached->getResultMessage());
        $this->assertEquals($memcached::RES_FAILURE, $memcached->getResultCode());

        $this->assertEquals(false, $result);
    }

    public function testConnection()
    {
        $memcached = new Client();
        $result = $memcached->setServer(self::HOST, self::PORT);

        $this->assertEquals(true, $memcached->getConnection() instanceof Connection);
    }
}
