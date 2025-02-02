<?php
declare(strict_types=1);

namespace GR\DevEnvBoilerplate\Infrastructure\Slim\Setting;

use RuntimeException;

class Settings implements SettingsInterface
{
    public function __construct(
        private array $settings
    ) {}

    public function get(string $key = '')
    {
        if (empty($key)) {
            return $this->settings;
        }

        $value = $this->settings;
        $components = explode('.', $key);
        foreach ($components as $component) {
            if (!isset($value[$component])) {
                throw new RuntimeException('Key "' . $key . '" not found.');
            }

            $value = $value[$component];
        }

        return $value;
    }
}
