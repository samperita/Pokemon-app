<?php
declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Slim\Handler;

use GR\DevEnvBoilerplate\Infrastructure\Slim\ResponseEmitter\ResponseEmitter;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

class ShutdownHandler
{
    public function __construct(
        private Request $request,
        private HttpErrorHandler $errorHandler,
        private bool $displayErrorDetails
    ) {}

    public function __invoke()
    {
        $error = error_get_last();
        if ($error) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $errorType = $error['type'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_USER_ERROR:
                        $message = "FATAL ERROR: {$errorMessage}. ";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;

                    case E_USER_WARNING:
                        $message = "WARNING: {$errorMessage}";
                        break;

                    case E_USER_NOTICE:
                        $message = "NOTICE: {$errorMessage}";
                        break;

                    default:
                        $message = "ERROR: {$errorMessage}";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;
                }
            }

            $exception = new HttpInternalServerErrorException($this->request, $message);
            $response = ($this->errorHandler)($this->request, $exception, $this->displayErrorDetails, false, false);

            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
}
