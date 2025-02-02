<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Persistence\Pdo;

use GR\DevEnvBoilerplate\Domain\Pokemon\Pokemon;
use GR\DevEnvBoilerplate\Domain\Pokemon\PokemonRepository;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\Pdo\Exception\RecordNotFound;
use GR\DevEnvBoilerplate\Infrastructure\Persistence\PokemonFactory;
use JsonException;
use PDO;

class MySqlPokemonRepository implements PokemonRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    /** @throws JsonException */
    public function getByName(string $aPokemonName): ?Pokemon
    {
        $sql = <<<SQL
            SELECT `name`, `type` FROM `pokemon` WHERE `name` = :name LIMIT 1;
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->bindParam('name', $aPokemonName);
        $statement->execute();

        $rawPokemonFromMysql = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rawPokemonFromMysql)) {
            throw RecordNotFound::build($aPokemonName);
        }

        return PokemonFactory::buildFromArray(array_pop($rawPokemonFromMysql));
    }
}
