<?php
declare(strict_types=1);

namespace PhpCircle\WorkLogs\Services;

use PhpCircle\WorkLogs\Interfaces\EnvInterface;

final class EnvConfiguration implements EnvInterface
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * EnvConfiguration constructor.
     *
     * @param string $envPath
     */
    public function __construct(string $envPath)
    {
        $this->envPath = $envPath;
    }

    /**
     * Set value to environment config.
     *
     * @param string $key
     * @param $value
     *
     * @return void
     */
    public function set(string $key, string $value): void
    {
        $envPath = $this->envPath;
        $contents = \file_get_contents($envPath);

        $contents .= "\n{$key}={$value}";

        if (empty(\getenv($key)) === false) {
            $oldConfiguration = $key . '=' . \getenv($key);
            $contents = \str_replace($oldConfiguration, '', $contents);
        }

        $file = fopen($envPath, 'w');

        fwrite($file, preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $contents));
        fclose($file);
    }
}
