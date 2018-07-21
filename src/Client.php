<?php

namespace zualex\Memcached;

class Client
{
    /**
     * Memcached constants
     * See: http://php.net/manual/en/memcached.constants.php
     */
    const CODE_SUCCESS = 0;                  // MEMCACHED_SUCCESS
    const CODE_FAILURE = 1;                  // MEMCACHED_FAILURE

    /**
     * Result messages of the last operation
     */
    const MESSAGE_NOTHING             = '';
    const MESSAGE_NOT_FOUND_SERVERS   = 'Not found servers.';
    const MESSAGE_CONNECT_SERVER_FAIL = 'Connect server fail.';

    /**
     * Default params
     */
    const DEFAULT_PORT = 11211;

    /**
     * Result code of the last operation
     * 
     * @var int
     */
    protected $resultCode;

    /**
     * Message describing the result of the last operation
     * 
     * @var int
     */
    protected $resultMessage;

    /**
     * Show server in pool
     *
     * Example: ['host' => $host, 'port' => $port]
     * 
     * @var array
     */
    protected $server = [];

    /**
     * Socket connect
     *
     * @var resource
     */
    protected $socket;

    /**
     * Return the result code of the last operation
     * 
     * @return int
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * Set result code of the last operation
     * 
     * @param int $codeInt
     */
    public function setResultCode($codeInt)
    {
        $this->resultCode = $codeInt;
    }

    /**
     * Return the message describing the result of the last operation
     * 
     * @return string
     */
    public function getResultMessage()
    {
        return $this->resultMessage;
    }

    /**
     * Set message describing the result of the last operation
     * 
     * @param string $codeInt
     */
    public function setResultMessage($message)
    {
        $this->resultMessage = $message;
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
     * Get the list of the servers in the pool
     * 
     * @return array
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set a serer to the server pool
     *
     * @param   string  $host   hostname of the memcache server
     * @param   int     $port   port on which memcache is running
     * @return  boolean
     */
    public function setServer($host, $port = null)
    {
        $this->server = [
            'host' => $host,
            'port' => ($port !== null) ? $port : self::DEFAULT_PORT,
        ];

        $this->setResultCode(self::CODE_SUCCESS);
        $this->setResultMessage(self::MESSAGE_NOTHING);
        return true;
    }
}