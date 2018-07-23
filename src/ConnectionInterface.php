<?php

namespace zualex\Memcached;


interface ConnectionInterface
{
    public function setSocket($socket);
    public function getSocket();
    public function readLine();
    public function write($query);
}