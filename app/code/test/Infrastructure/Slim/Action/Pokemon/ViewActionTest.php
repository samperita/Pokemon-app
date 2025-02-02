<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Tests\Infrastructure\Slim\Action\Hello;

use GR\DevEnvBoilerplate\Infrastructure\Slim\Action\ActionPayload;
use GR\DevEnvBoilerplate\Tests\Infrastructure\Slim\Action\ActionTestCase;
use Dotenv\Dotenv;

class ViewActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Cargar las variables de entorno necesarias para los tests
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../../');
        $dotenv->load();
    }

    public function testAction(): void
    {
        // Obtener instancia de la aplicación Slim
        $app = $this->getAppInstance();

        // Crear una solicitud simulada
        $request = $this->createRequest('GET', '/bulbasaur');
        $response = $app->handle($request);

        // Leer el payload de la respuesta
        $payload = (string) $response->getBody();

        // Decodificar la respuesta JSON
        $data = json_decode($payload, true)['data'];

        // Validar que la respuesta contiene las claves esperadas
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('count', $data);

        // Validar valores específicos
        $this->assertEquals('bulbasaur', $data['name']);
        $this->assertEquals('planta', $data['type']);
        $this->assertGreaterThan(0, $data['count']);
    }
}
