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
    const MESSAGE_NOTHING           = '';
    const MESSAGE_SERVER_DUPLICATE  = 'Server duplicate.';
    const MESSAGE_NOT_FOUND_SERVERS = 'Not found servers.';

    /**
     * Default params
     */
    const DEFAULT_PORT   = 11211;
    const DEFAULT_WEIGHT = 0;

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
     * $server list of the servers in the pool
     *
     * Example:
     *     [
     *         [
     *             'host' => host,
     *             'port' => port,
     *             'weight' => weight
     *         ],
     *     ]
     * 
     * @var array
     */
    protected $serverList = array();

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
    public function getServerList()
    {
        return array_values($this->serverList);
    }

    /**
     * Add a serer to the server pool
     *
     * @param   string  $host   hostname of the memcache server
     * @param   int     $port   port on which memcache is running
     * @param   int     $weight weight of the server relative to the total weight of all the servers in the pool
     * @return  boolean
     */
    public function addServer($host, $port = null, $weight = null)
    {
        $port = ($port !== null) ? $port : self::DEFAULT_PORT;
        $weight = ($weight !== null) ? $weight : self::DEFAULT_WEIGHT;

        if ($this->isServerDuplicate($host, $port, $weight)) {
            $this->setResultCode(self::CODE_FAILURE);
            $this->setResultMessage(self::MESSAGE_SERVER_DUPLICATE);
            return false;
        }

        $key = $this->getServerKey($host, $port, $weight);
        $this->serverList[$key] = [
            'host'   => $host,
            'port'   => $port,
            'weight' => $weight,
        ];

        $this->setResultCode(self::CODE_SUCCESS);
        $this->setResultMessage(self::MESSAGE_NOTHING);
        return true;
    }

    /**
     * Add multiple servers to the server pool
     *
     * @param   array   $servers
     * @return  boolean
     */
    public function addServers($servers)
    {
        if (is_array($servers) === false || count($servers) === 0) {
            $this->setResultCode(self::CODE_FAILURE);
            $this->setResultMessage(self::MESSAGE_NOT_FOUND_SERVERS);
            return false;
        }

        foreach ($servers as $row) {
            $host = $row[0];
            $port = isset($row[1]) ? $row[1] : self::DEFAULT_PORT;
            $weight = isset($row[2]) ? $row[2] : self::DEFAULT_WEIGHT;

            $this->addServer($host, $port, $weight);
        }

        return true;
    }

    /**
     * Check server duplicate
     * 
     * @param  string  $host   hostname of the memcache server
     * @param  int     $port   port on which memcache is running
     * @param  int     $weight weight of the server relative to the total weight of all the servers in the pool
     * 
     * @return boolean
     */
    protected function isServerDuplicate($host, $port, $weight)
    {
        $key = $this->getServerKey($host, $port, $weight);

        return isset($this->serverList[$key]);
    }

    /**
     * Get key of server array
     *
     * @param   string  $host
     * @param   int     $port
     * @param   int     $weight
     * @return  string
     */
    protected function getServerKey($host, $port = 11211, $weight = 0)
    {
        return "{$host}_{$port}_{$weight}";
    }
}