<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Persistence;

use GR\DevEnvBoilerplate\Domain\Pokemon\Pokemon;
use GR\DevEnvBoilerplate\Domain\Type;

class PokemonFactory
{
    public static function buildFromArray(array $rawPokemon): Pokemon
    {
        return new Pokemon(
            $rawPokemon['name'],
            Type::buildFromString($rawPokemon['type'])
        );
    }
}
