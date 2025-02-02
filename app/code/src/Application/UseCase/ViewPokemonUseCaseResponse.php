<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Application\UseCase;

use GR\DevEnvBoilerplate\Domain\Pokemon\Pokemon;

class ViewPokemonUseCaseResponse
{
    public function __construct(
        private Pokemon $pokemon,
        private int $count
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->pokemon->getName(),
            'type' => $this->pokemon->getType()->name(),
            'count' => $this->count
        ];
    }
}
