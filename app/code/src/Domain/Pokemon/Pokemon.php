<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Domain\Pokemon;

use GR\DevEnvBoilerplate\Domain\Type;

class Pokemon
{
    public function __construct(
        private string $name,
        private Type $type
    ) {}

    public function getType(): Type
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType()->name()
        ];
    }
}
