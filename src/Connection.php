<?php

namespace zualex\Memcached;

use Exception;
use zualex\Memcached\ConnectionInterface;

class Connection implements ConnectionInterface
{
    const END_LINE = "\r\n";

    /**
     * Socket connect
     *
     * @var resource
     */
    private $socket;

    public function __construct($host, $port, $async = false)
    {
        $error = 0;
        $errstr = '';
        $result = @fsockopen($host, $port, $error, $errstr);

        if ($result === false) {
            throw new Exception("{$errstr} ({$error})");
        }

        if ($async === true) {
            stream_set_blocking($result, 0);
        }

        $this->socket = $result;
    }

    /**
     * Get socket
     *
     * @return string
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Set socket
     *
     * @param resource $socket
     */
    public function setSocket($socket)
    {
        $this->socket = $socket;
    }

    /**
     * Get data from socket
     *
     * @return string
     */
    public function readLine()
    {
        return trim(fgets($this->getSocket()));
    }

    /**
     * Send data to socket
     *
     * @param  string $query
     * @return mixed
     */
    public function write($query)
    {
        return fwrite($this->getSocket(), $query . self::END_LINE);
    }
}
