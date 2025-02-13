<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Application\UseCase;

use GR\DevEnvBoilerplate\Domain\Pokemon\PokemonRepository;
use GR\DevEnvBoilerplate\Domain\Pokemon\RequestCountRepository;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\Pdo\Exception\RecordNotFound;
use Slim\Exception\HttpNotFoundException;

class ViewPokemonUseCase
{
    public function __construct(
        private PokemonRepository $cachedRepository,
        private RequestCountRepository $requestCountRepository,
    ) {}

    public function execute(string $aPokemonName): ViewPokemonUseCaseResponse
    {
        return new ViewPokemonUseCaseResponse(
            $this->cachedRepository->getByName($aPokemonName),
            $this->requestCountRepository->add($aPokemonName)
        );
    }
}
