<?php
declare(strict_types=1);

namespace App;


use PDO;

class PdoFactory
{
    /**
     * @var DatabaseSettings
     */
    private DatabaseSettings $settings;

    public function __construct(DatabaseSettings $settings)
    {
        $this->settings = $settings;
    }

    public function createPdo(): PDO
    {
        $settings = $this->settings;

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',

        );

        $username = $settings->username;
        $password = $settings->password;
        $host = $settings->host;
        $db = $settings->database;

        return new PDO("mysql:dbname=$db;host=$host", $username, $password, $options);
    }
}
