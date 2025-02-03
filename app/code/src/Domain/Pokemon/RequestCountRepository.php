<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Domain\Pokemon;

interface RequestCountRepository
{
    public function add(string $aPokemonName): int;
}