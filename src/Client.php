<?php

namespace zualex\Memcached;

class Client
{
    /**
     * Memcached constants
     * See: http://php.net/manual/en/memcached.constants.php
     */
    const RES_SUCCESS = 0;                  // The operation was successful.
    const RES_FAILURE = 1;                  // The operation failed in some fashion.
    const RES_CONNECTION_SOCKET_CREATE_FAILURE = 11;  // Failed to create network socket.

    /**
     * Result messages of the last operation
     */
    const MESSAGE_NOTHING = '';

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

        return $this->connect();
    }

    /**
     * Connect to memcached server
     *
     * @return  boolean
     */
    protected function connect()
    {
        $result = false;
        $server = $this->getServer();
        $host   = $server['host'];
        $port   = $server['port'];

        $error = 0;
        $errstr = '';
        $result = @fsockopen($host, $port, $error, $errstr);

        if ($result === false) {
            $this->resultCode = self::RES_CONNECTION_SOCKET_CREATE_FAILURE;
            $this->resultMessage = "$errstr ($error)";
            return false;
        }

        $this->setSocket($result);

        $this->setResultCode(self::RES_SUCCESS);
        $this->setResultMessage(self::MESSAGE_NOTHING);
        return true;
    }
}