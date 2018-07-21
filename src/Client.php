<?php

namespace zualex\Memcached;

class Client
{
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
     * @param   int     $port   port on which memcache is running, default 11211
     * @param   int     $weight weight of the server relative to the total weight of all the servers in the pool
     * @return  boolean
     */
    public function addServer($host, $port = 11211, $weight = 0)
    {
        if ($this->isServerDuplicate($host, $port, $weight)) {
            return false;
        }

        $key = $this->getServerKey($host, $port, $weight);
        $this->serverList[$key] = [
            'host'  => $host,
            'port'  => $port,
            'weight'    => $weight,
        ];

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