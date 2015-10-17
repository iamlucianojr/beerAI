<?php

namespace Bavarianlabs;


class Connection
{
    protected $host;
    protected $port;
    protected $schema;
    protected $user;
    protected $password;

    /**
     * Connection constructor.
     *
     * @param $host
     * @param $port
     * @param $schema
     * @param $user
     * @param $password
     */
    public function __construct($host = 'localhost', $port = '7474', $schema = 'graph.db', $user = 'neo4j', $password = 'neo4j')
    {
        $this->host = $host;
        $this->port = $port;
        $this->schema = $schema;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function gethost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

}