<?php

declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Slim\Setting;

interface SettingsInterface
{
    public function get(string $key = '');
}
