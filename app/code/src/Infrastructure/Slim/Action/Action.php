<?php
declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Slim\Action;

use GR\DevEnvBoilerplate\Domain\Exception\RecordNotFoundException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    protected Request $request;

    protected Response $response;

    protected array $args;

    public function __construct(
        protected LoggerInterface $logger
    ) {}

     /** @throws HttpNotFoundException|HttpBadRequestException|RecordNotFoundException */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (RecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    /** @throws RecordNotFoundException|HttpBadRequestException */
    abstract protected function action(): Response;

    /** @throws HttpBadRequestException|JsonException */
    protected function getFormData(): array
    {
        $input = json_decode(file_get_contents('php://input'), false, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpBadRequestException($this->request, 'Malformed JSON input.');
        }

        return $input;
    }

    /** @throws HttpBadRequestException */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    /** @throws JsonException */
    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($payload->getStatusCode());
    }
}
