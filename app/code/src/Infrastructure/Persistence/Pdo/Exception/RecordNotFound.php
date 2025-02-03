<?php

namespace GR\DevEnvBoilerplate\Infrastructure\Persistence\Pdo\Exception;

use RuntimeException;

class RecordNotFound extends RuntimeException
{
    private const MSG = 'The "%s" Pokemon was not found';

    public static function build(string $aPokemonName): self
    {
        return new self(sprintf(self::MSG, $aPokemonName));
    }
}
