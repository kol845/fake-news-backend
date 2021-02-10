<?php
declare(strict_types=1);

namespace App;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

// Handles the catching of API URIs
class CounterApi
{
    /**
     * @var CounterService
     */
    private CounterService $counterService;


    /**
     * CounterApi constructor.
     * @param CounterService $counterService
     */
    public function __construct(CounterService $counterService)
    {
        $this->counterService = $counterService;
    }

    public function setup(Group $group)
    {
        $group->get('', function (Request $request, Response $response, $args) { # GET api/counters  - Get all counter
            $response->getBody()->write(json_encode($this->counterService->getCounters()));
            return $response->withHeader('Content-Type', 'application/json');
        });
        $group->post('', function (Request $request, Response $response, $args) { # POST api/counters - Create new counter
            $input = json_decode(file_get_contents('php://input'));
            $name =  $input->name;
            $response->getBody()->write(json_encode($this->counterService->createCounter($name)));
            return $response->withHeader('Content-Type', 'application/json');
        });
        $group->get('/{id}', function (Request $request, Response $response, $args) { # GET api/counters/{id} - Get specific counter
            $response->getBody()->write(json_encode($this->counterService->getCounter((int)$args['id'])));
            return $response->withHeader('Content-Type', 'application/json');
        });
        $group->post('/{id}', function (Request $request, Response $response, $args) { # POST api/counters/{id} - Change specific counter
            $response->getBody()->write(json_encode($this->counterService->increaseCounter((int)$args['id'])));
            return $response->withHeader('Content-Type', 'application/json');
        });

    }
}
