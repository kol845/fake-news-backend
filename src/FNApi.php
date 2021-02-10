<?php
declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

// Handles the catching of API URIs
class FNApi
{
    /**
     * @var FNService

     */
    private FNService $fnService;


    /**
     * CounterApi constructor.
     * @param FNService $fnService
     */
    public function __construct(FNService $fnService)
    {
        $this->fnService = $fnService;
    }

    public function setup(Group $group)
    {
        $group->get('', function (Request $request, Response $response, $args) { # GET api/articles
            $response->getBody()->write(json_encode($this->fnService->getArticles()));
            return $response->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8000')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        });
        $group->get('/{id}', function (Request $request, Response $response, $args) { # GET api/articles
            $response->getBody()->write(json_encode($this->fnService->getArticle((int)$args['id'])));
            return $response->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8000')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        });
        $group->post('', function (Request $request, Response $response, $args) { # POST api/articles
            // $input = "Input: ";
            $reqBody = json_decode(file_get_contents('php://input'));
            $headline = (isset($reqBody->headline) ? $reqBody->headline:false);
            $text = (isset($reqBody->text) ? $reqBody->text:false);
            $image = (isset($reqBody->image) ? $reqBody->image:false);
            if(!$headline || !$text || !$image){
                $response->getBody()->write("Missing arguments");
                return $response->withStatus(422)
                    ->withHeader('Content-Type', 'application/json')
                    ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8000')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
            }
            $response->getBody()->write(json_encode($this->fnService->createArticle($headline, $text, $image)));
            return $response->withHeader('Content-Type', 'application/json')->withHeader('Access-Control-Allow-Origin', 'http://localhost:8000');
        });
        $group->post('/{id}', function (Request $request, Response $response, $args) { # POST api/articles/{article_id}
            // $input = "Input: ";
            $reqBody = json_decode(file_get_contents('php://input'));
            $headline = (isset($reqBody->headline) ? $reqBody->headline:false);
            $text = (isset($reqBody->text) ? $reqBody->text:false);
            if(!$headline || !$text){
                $response->getBody()->write("Missing arguments");
                return $response->withStatus(422)
                    ->withHeader('Content-Type', 'application/json')
                    ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8000')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
            }

            $response->getBody()->write(json_encode($this->fnService->editArticle((int)$args['id'],$headline, $text)));
            return $response->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8000')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');;
        });
        $group->delete('/{id}', function (Request $request, Response $response, $args) { # POST api/articles/{article_id}
            $response->getBody()->write(json_encode($this->fnService->deleteArticle((int)$args['id'])));
            return $response->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8000')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        });
        // $group->get('/{id}', function (Request $request, Response $response, $args) { # GET api/counters/{id}
        //     $response->getBody()->write(json_encode($this->counterService->getCounter((int)$args['id'])));
        //     return $response->withHeader('Content-Type', 'application/json');
        // });
        // $group->post('/{id}', function (Request $request, Response $response, $args) { # POST api/counters/{id}
        //     $response->getBody()->write(json_encode($this->counterService->increaseCounter((int)$args['id'])));
        //     return $response->withHeader('Content-Type', 'application/json');
        // });
    }
}
