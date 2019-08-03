<?php
declare(strict_types=1);

namespace PhpCircle\Worklogs\Interfaces;

interface ConfigurationInterface
{
    /**
     * Get jira author id.
     *
     * @return string
     */
    public function getAuthorId(): string;

    /**
     * Get tempo api token.
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Set author id to config.
     *
     * @param string $authorId
     *
     * @return \PhpCircle\Worklogs\Interfaces\ConfigurationInterface
     */
    public function setAuthorId(string $authorId): ConfigurationInterface;

    /**
     * Set api token to config.
     *
     * @param string $token
     *
     * @return \PhpCircle\Worklogs\Interfaces\ConfigurationInterface
     */
    public function setToken(string $token): ConfigurationInterface;
}
