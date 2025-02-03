<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Persistence\Memcached;

use GR\DevEnvBoilerplate\Domain\Pokemon\RequestCountRepository;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\Memcached\Exception\MemcachedServerFailure;
use Memcached;

class MemcachedPokemonRepository implements RequestCountRepository
{
    public function __construct(
        private Memcached $client
    ) {}

    public function add(string $aPokemonName): int
    {
        $count = $this->client->increment($aPokemonName, 1, 1);

        if (is_bool($count)) {
            throw MemcachedServerFailure::build($aPokemonName);
        }

        return $count;
    }
}