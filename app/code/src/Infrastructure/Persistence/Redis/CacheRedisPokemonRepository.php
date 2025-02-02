<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Persistence\Redis;

use GR\DevEnvBoilerplate\Domain\Pokemon\Pokemon;
use GR\DevEnvBoilerplate\Domain\Pokemon\PokemonRepository;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\Pdo\MySqlPokemonRepository;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\PokemonFactory;
use JsonException;
use Predis\Client;
use Psr\SimpleCache\InvalidArgumentException;

class CacheRedisPokemonRepository implements PokemonRepository
{
    public function __construct(
        private Client $client,
        private MySqlPokemonRepository $mySqlPokemonRepository
    ) {}

    /** @throws JsonException|InvalidArgumentException */
    public function getByName(string $aPokemonName): ?Pokemon
    {
        $pokemonFromRedis = $this->getFromRedis($aPokemonName);

        if (null !== $pokemonFromRedis) {
            return $pokemonFromRedis;
        }

        $pokemon = $this->mySqlPokemonRepository->getByName($aPokemonName);

        if (null === $pokemon) {
            return null;
        }

        $this->storeInRedis($pokemon);

        return $pokemon;
    }

    /** @throws JsonException|InvalidArgumentException */
    private function getFromRedis(string $aPokemonName): ?Pokemon
    {
        $rawPokemonFromRedis = $this->client->get($aPokemonName);

        return null === $rawPokemonFromRedis
            ? null
            : PokemonFactory::buildFromArray(
                json_decode($rawPokemonFromRedis, true, 512, JSON_THROW_ON_ERROR)
            );
    }

    /** @throws JsonException|InvalidArgumentException */
    private function storeInRedis(Pokemon $pokemon): void
    {
        $this->client->set(
            $pokemon->getName(),
            json_encode($pokemon->toArray(), JSON_THROW_ON_ERROR)
        );
    }
}
