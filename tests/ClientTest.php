<?php

namespace zualex\Memcached\tests;

use zualex\Memcached\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testExample()
    {
        $this->assertEquals('Hello I\'am new class', Client::hello());
    }
}