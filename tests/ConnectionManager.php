<?php

namespace Sentiweb\Rserve\Tests;

use Sentiweb\Rserve\Connection;

class ConnectionManager
{
    /** @var string|null */
    protected $host;

    /** @var int */
    protected $port;

    /** @var string|null */
    protected $username;

    /** @var string|null */
    protected $password;

    public function __construct()
    {
        $this->host = $this->getvar("RSERVE_HOST");

        $port = $this->getvar("RSERVE_PORT");
        if ($port) {
            if ($port == "0" || $port == "unix" || $port == "socket") {
                $this->port = 0;
            }
            if ($port == "") {
                $this->port = Connection::DEFAULT_PORT;
            } else {
                $this->port = (int) $port;
            }
        }
        $this->username = $this->getvar("RSERVE_USER");
        $this->password = $this->getvar("RSERVE_PASS");
    }

    protected function getvar(string $name): ?string
    {
        if (defined($name)) {
            return constant($name);
        }
        $value = getenv($name);
        return $value !== false ? $value : null;
    }

    public function create(bool $requireAuth = false): ?Connection
    {
        if (!$this->host) {
            return null;
        }

        if (!$this->username && $requireAuth) {
            return null;
        }
        $params = [];
        if ($this->username) {
            $params["username"] = $this->username;
            $params["password"] = $this->password;
        }
        return new Connection($this->host, $this->port, $params);
    }
}
