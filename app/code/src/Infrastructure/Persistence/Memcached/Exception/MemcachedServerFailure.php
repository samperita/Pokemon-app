<?php

namespace GR\DevEnvBoilerplate\Infrastructure\Persistence\Memcached\Exception;

use MemcachedException;

class MemcachedServerFailure extends MemcachedException
{
    private const MSG = 'Failure trying to increment "%s" requests counter';

    public static function build(string $aPokemonName): self
    {
        return new self(sprintf(self::MSG, $aPokemonName));
    }
}