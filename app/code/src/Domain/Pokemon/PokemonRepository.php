<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Domain\Pokemon;

interface PokemonRepository
{
    public function getByName(string $aPokemonName): ?Pokemon;
}