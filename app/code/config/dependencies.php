<?php
declare(strict_types=1);

use GR\Common\Cache\RedisBridge\Infrastructure\Cache\Redis\PredisCache;
use GR\DevEnvBoilerplate\Infrastructure\Slim\Setting\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Predis\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $dsn = 'mysql:dbname=' . $settings->get('database.name') . ';host=' . $settings->get('database.host') . ';port=' . $settings->get('database.port') . ';charset=' . $settings->get('database.charset');

            return new PDO(
                $dsn,
                $settings->get('database.user'),
                $settings->get('database.password'),
            );
        },
        Client::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            return new Client(
                $settings->get('redis.schema') . '://' . $settings->get('redis.host') . ':' . $settings->get('redis.port')
            );
        },
        Memcached::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $memcached = new Memcached();

            $memcached->addServer(
                $settings->get('memcached.host'),
                $settings->get('memcached.port')
            );

            $memcached->setOption(
                Memcached::OPT_BINARY_PROTOCOL,
                true
            );

            return $memcached;
        },
    ]);
};
