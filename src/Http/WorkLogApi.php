<?php
declare(strict_types=1);

namespace PhpCircle\Jira\Worklogs\Http;

use DateTime;
use GuzzleHttp\Client;
use PhpCircle\Jira\Worklogs\Http\Interfaces\WorkLogsApiInterface;
use PhpCircle\Jira\Worklogs\Interfaces\ConfigurationInterface;

final class WorkLogApi implements WorkLogsApiInterface
{
    /** @var string */
    private const RESOURCE = 'core/3/worklogs';

    /** @var string */
    private const URI = 'https://api.tempo.io';

    /** @var \GuzzleHttp\Client */
    private $client;

    private $configuration;

    /**
     * WorkLogApi constructor.
     *
     * @param \PhpCircle\Jira\Worklogs\Interfaces\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->client = new Client(['base_uri' => self::URI]);
        $this->configuration = $configuration;
    }

    /**
     * Create Work log.
     *
     * @param string $issueNo
     * @param float $spentInSeconds
     * @param \DateTime $logDateTime
     * @param null|string $description
     *
     * @return object
     */
    public function createWorkLog(
        string $issueNo,
        float $spentInSeconds,
        DateTime $logDateTime,
        ?string $description
    ): object {
        return $this->client->post(self::RESOURCE, [
            'json' => [
                'issueKey' => $issueNo,
                'timeSpentSeconds' => $spentInSeconds,
                'startDate' => $logDateTime->format('Y-m-d'),
                'startTime' => $logDateTime->format('H:i:s'),
                'description' => $description ?? '[NO_DESCRIPTION]',
                'authorAccountId' => $this->configuration->getAuthorId()
            ],
            'headers' => [
                'AUTHORIZATION' => 'Bearer ' . $this->configuration->getToken()
            ]
        ]);
    }

    /**
     * Get Work logs.
     *
     * @param mixed[]|null $query
     *
     * @return mixed[]
     */
    public function getWorkLogs(?array $query = null): array
    {
        $response = $this->client->get(self::RESOURCE, [
            'headers' => [
                'AUTHORIZATION' => 'Bearer ' . $this->configuration->getToken()
            ]
        ]);

        return \json_decode($response->getBody()->getContents(), true);
    }
}
