<?php
declare(strict_types=1);

namespace App;

use PDO;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Api
{
    private App $app; # App from 'skin' framework

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->app = AppFactory::create(); # Slim framework stuff

        $this->setup($this->app); # Run setup()
    }

    public function run()
    {
        $this->app->run();
    }

    function getDatabaseSettings(): DatabaseSettings
    {
        $settings = new DatabaseSettings();
        $settings->username = getenv('MYSQL_USERNAME');
        $settings->password = getenv('MYSQL_PASSWORD');
        $settings->host = getenv('MYSQL_HOST');
        $settings->database = getenv('MYSQL_DATABASE');


        return $settings;
    }

    private function setup(App $app): App
    {
        $pdo = (new PdoFactory($this->getDatabaseSettings()))->createPdo(); # PHP Database Object. From PHP DB.
        $counterApi = new CounterApi(new CounterService($pdo)); # Create a api instance with pdo as settings
        $fnApi = new FNApi(new FNService($pdo)); # Create a api instance with pdo as settings

        $app->options('/{routes:.*}', function (Request $request, Response $response) {
            // CORS Pre-Flight OPTIONS Request Handler
            return $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        });

        $app->group('/api/counters', function (Group $group) use ($counterApi) {
            $counterApi->setup($group);
        });

        $app->group('/api/articles', function (Group $group) use ($fnApi) {
            $fnApi->setup($group);
        });


        return $app;
    }
}
