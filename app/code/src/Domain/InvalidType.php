<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Domain;

use DomainException;

class InvalidType extends DomainException
{
    private const MESSAGE = 'The provided type "%s" is not valid';

    private function __construct(string $message = "")
    {
        parent::__construct($message);
    }

    public static function buildFromString(string $typeString): self
    {
        return new self(
            sprintf(self::MESSAGE, $typeString)
        );
    }
}