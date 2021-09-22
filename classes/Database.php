<?php

class Database
{
    private $server_name;
    private $user_name;
    private $password;
    private $db_name;

    protected function connect()
    {
        $this->server_name = "localhost";
        $this->user_name = "root";
        $this->password = "root";
        $this->db_name = "nba2019";

        $conn = new mysqli($this->server_name, $this->user_name, $this->password, $this->db_name);

        return $conn;
    }
}
