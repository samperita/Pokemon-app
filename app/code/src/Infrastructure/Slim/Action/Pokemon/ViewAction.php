<?php
declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Slim\Action\Pokemon;

use GR\DevEnvBoilerplate\Infrastructure\Slim\Action\Action;
use GR\DevEnvBoilerplate\Application\UseCase\ViewPokemonUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ViewAction extends Action
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ViewPokemonUseCase $useCase
    ) {
        parent::__construct($logger);
    }

    /** {@inheritdoc} */
    protected function action(): Response
    {
        $pokemonName = $this->request->getAttribute('pokemon');

        $response = $this->useCase->execute($pokemonName);

        return $this->respondWithData($response->toArray());
    }
}
