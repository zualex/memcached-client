<?php

namespace zualex\Memcached;

use Exception;
use zualex\Memcached\Connection;
use zualex\Memcached\ConnectionInterface;

class Client
{
    /**
     * Memcached constants
     * See: http://php.net/manual/en/memcached.constants.php
     */
    const RES_SUCCESS          = 0;         // The operation was successful.
    const RES_FAILURE          = 1;         // The operation failed in some fashion.
    const RES_BAD_KEY_PROVIDED = 33;        // Bad key.
    const RES_NOTFOUND         = 16;        // Item with this key was not found (with "get" operation or "cas" operations).
    const RES_CONNECTION_SOCKET_CREATE_FAILURE = 11;  // Failed to create network socket.

    /**
     * Result messages of the last operation
     */
    const MESSAGE_NOTHING          = '';
    const MESSAGE_KEY_NOT_STRING   = 'Key is not string.';
    const MESSAGE_KEY_MAX_LENGTH   = 'The length limit of a key 250 characters.';
    const MESSAGE_KEY_BAD_CHARS    = 'Key include control characters or whitespace. Allow a-Z 0-9 _';
    const MESSAGE_SET_FAIL         = 'Set fail.';
    const MESSAGE_GET_FAIL         = 'Get fail.';
    const MESSAGE_DELETE_FAIL      = 'Delete fail.';
    const MESSAGE_KEY_NOT_FOUND    = 'Key not exists.';
    const MESSAGE_NOT_FOUND_SOCKET = 'Not found socket.';

    /**
     * Default params
     */
    const DEFAULT_PORT           = 11211;
    const DEFAULT_MAX_KEY_LENGTH = 250;

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
     * Connection
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Return connection
     * 
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set connection class
     * 
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

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

        try {
            $connection = new Connection($this->server['host'], $this->server['port']);
        } catch (Exception $e) {
            $this->resultCode = self::RES_CONNECTION_SOCKET_CREATE_FAILURE;
            $this->resultMessage = $e->getMessage();
            return false;
        }

        $this->setConnection($connection);

        $this->setResultCode(self::RES_SUCCESS);
        $this->setResultMessage(self::MESSAGE_NOTHING);
        return true;
    }

    /**
     * Store an item
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   int     $expiration
     * @return  boolean
     */
    public function set($key, $value, $expiration = 0)
    {
        $prepareValue = $this->prepareValueForConnection($value);
        $lenValue = strlen($prepareValue);
        if ($this->keyIsValid($key) === false) {
            return false;
        }

        $connection = $this->getConnection();
        $connection->write("set {$key} 0 {$expiration} {$lenValue}");
        $connection->write($prepareValue);
        $result = $connection->readLine();

        if ($result !== 'STORED') {
            $this->setResultCode(self::RES_FAILURE);
            $this->setResultMessage(self::MESSAGE_SET_FAIL);
            return false;
        }

        $this->setResultCode(self::RES_SUCCESS);
        $this->setResultMessage(self::MESSAGE_NOTHING);
        return true;
    }

    /**
     * Retrieve an item
     *
     * @param   string  $key
     * @return  mixed
     */
    public function get($key)
    {
        if ($this->keyIsValid($key) === false) {
            return false;
        }

        $connection = $this->getConnection();
        $connection->write("get {$key}");
        $resultQuery = $connection->readLine();

        if (is_null($resultQuery) || substr($resultQuery, 0, 5) !== 'VALUE') {
            $this->setResultCode(self::RES_NOTFOUND);
            $this->setResultMessage(self::MESSAGE_KEY_NOT_FOUND);
            return false;
        }

        $result = '';
        $line = '';
        while ($line !== 'END') {
            $result .= $line;
            $line = $connection->readLine();
        }

        $this->setResultCode(self::RES_SUCCESS);
        $this->setResultMessage(self::MESSAGE_NOTHING);

        return $this->prepareValueForApp($result);
    }

    /**
     * Delete an item
     *
     * @param   string  $key
     * @return  boolean
     */
    public function delete($key)
    {
        if ($this->keyIsValid($key) === false) {
            return false;
        }

        $connection = $this->getConnection();
        $connection->write("delete {$key}");
        $result = $connection->readLine();

        if ($result !== 'DELETED') {
            $this->setResultCode(self::RES_FAILURE);
            $this->setResultMessage(self::MESSAGE_DELETE_FAIL);
            return false;
        }

        $this->setResultCode(self::RES_SUCCESS);
        $this->setResultMessage(self::MESSAGE_NOTHING);
        return true;
    }

    /**
     * Prepare value before query connection
     * 
     * @param  string $value
     * @return string
     */
    protected function prepareValueForConnection($value)
    {
        return serialize($value);
    }

    /**
     * Prepare value for application
     * 
     * @param  string $value
     * @return string
     */
    protected function prepareValueForApp($value)
    {
        return unserialize($value);
    }

    /**
     * Check key
     * 
     * @param  sting $key
     * @return boolean
     */
    protected function keyIsValid($key)
    {
        if (is_string($key) === false) {
            $this->setResultCode(self::RES_BAD_KEY_PROVIDED);
            $this->setResultMessage(self::MESSAGE_KEY_NOT_STRING);
            return false;
        }

        if (strlen($key) > self::DEFAULT_MAX_KEY_LENGTH) {
            $this->setResultCode(self::RES_BAD_KEY_PROVIDED);
            $this->setResultMessage(self::MESSAGE_KEY_MAX_LENGTH);
            return false;
        }

        if (preg_match('/[^\w]/', $key)) {
            $this->setResultCode(self::RES_BAD_KEY_PROVIDED);
            $this->setResultMessage(self::MESSAGE_KEY_BAD_CHARS);
            return false;
        }

        return true;
    }
}