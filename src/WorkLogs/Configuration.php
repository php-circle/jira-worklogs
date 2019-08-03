<?php
declare(strict_types=1);

namespace PhpCircle\WorkLogs;

use PhpCircle\Worklogs\Interfaces\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $authorId;

    /**
     * @var string
     */
    private $token;

    /**
     * ConfigurationInterface constructor.
     *
     * @param string $authorId
     * @param string $token
     */
    public function __construct(
        string $authorId,
        string $token
    ) {
        $this->authorId = $authorId;
        $this->token = $token;
    }

    /**
     * Get jira author id.
     *
     * @return string
     */
    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    /**
     * Get tempo api token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set author id to config.
     *
     * @param string $authorId
     *
     * @return \PhpCircle\Worklogs\Interfaces\ConfigurationInterface
     */
    public function setAuthorId(string $authorId): ConfigurationInterface
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * Set api token to config.
     *
     * @param string $token
     *
     * @return \PhpCircle\Worklogs\Interfaces\ConfigurationInterface
     */
    public function setToken(string $token): ConfigurationInterface
    {
        $this->token = $token;

        return $this;
    }
}
