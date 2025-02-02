<?php

declare(strict_types=1);

use GR\DevEnvBoilerplate\Infrastructure\Slim\Setting\Settings;
use GR\DevEnvBoilerplate\Infrastructure\Slim\Setting\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return static function (ContainerBuilder $containerBuilder)
{
    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true,
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'database' => [
                    'host' => $_ENV['DB_HOST'],
                    'port' => $_ENV['DB_PORT'],
                    'name' => $_ENV['MYSQL_DATABASE'],
                    'user' => $_ENV['MYSQL_USER'],
                    'password' => $_ENV['MYSQL_PASSWORD'],
                    'charset' => 'utf8mb4',
                ],   
                'redis' => [
                    'schema' => 'tcp',
                    'host' => $_ENV['REDIS_HOST'],
                    'port' => $_ENV['REDIS_PORT'],
                ],
                'memcached' => [
                    'host' => $_ENV['MEMCACHED_HOST'],
                    'port' => intval ($_ENV['MEMCACHED_PORT'],10),
                ],

            ]);
        }
    ]);
};
