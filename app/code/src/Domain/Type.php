<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Domain;

class Type
{
    private const TYPE_NAMES = [
        'fuego' => true,
        'agua' => true,
        'eléctrico' => true,
        'hada' => true,
        'planta' => true,
        'fantasma' => true,
        'lucha' => true,
        'roca' => true,
    ];

    private string $type;

    private function __construct(string $aTypeName)
    {
        $this->type = $aTypeName;
    }

    public static function buildFromString(string $aTypeName): self
    {
        if (!isset(self::TYPE_NAMES[$aTypeName])) {
            throw InvalidType::buildFromString($aTypeName);
        }

        return new self($aTypeName);
    }

    public function name(): string
    {
        return $this->type;
    }

    public function equals(Type $anotherType): bool
    {
        return $this->type === $anotherType->type;
    }
}
