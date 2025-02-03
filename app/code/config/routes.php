<?php

declare(strict_types=1);

use GR\DevEnvBoilerplate\Infrastructure\Slim\Action\Pokemon\ViewAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

return static function (App $app) {
    // Middleware para encabezados CORS
    $app->add(function (Request $request, RequestHandlerInterface $handler): Response {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });

    // Ruta OPTIONS para manejar preflight requests
    $app->options('/{routes:.*}', function (Request $request, Response $response): Response {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });

    $app->get('/{pokemon}', ViewAction::class);
};
