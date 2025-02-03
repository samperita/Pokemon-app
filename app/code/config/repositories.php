<?php

declare(strict_types=1);

use GR\DevEnvBoilerplate\Domain\Pokemon\PokemonRepository;
use DI\ContainerBuilder;
use GR\DevEnvBoilerplate\Domain\Pokemon\RequestCountRepository;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\Memcached\MemcachedPokemonRepository;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\Pdo\MySqlPokemonRepository;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\Redis\CacheRedisPokemonRepository;
use Predis\Client;
use Psr\Container\ContainerInterface;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PokemonRepository::class => function (ContainerInterface $c) {
            return new CacheRedisPokemonRepository(
                $c->get(Client::class),
                new MySqlPokemonRepository($c->get(PDO::class))
            );
        },
        RequestCountRepository::class => function (ContainerInterface $c) {
            return new MemcachedPokemonRepository($c->get(Memcached::class));
        },
    ]);
};
