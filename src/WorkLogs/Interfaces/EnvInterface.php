<?php
declare(strict_types=1);

namespace PhpCircle\WorkLogs\Interfaces;

interface EnvInterface
{
    /**
     * Set value to environment config.
     *
     * @param string $key
     * @param $value
     *
     * @return void
     */
    public function set(string $key, string $value): void;
}
