<?php
declare(strict_types=1);

namespace PhpCircle\Jira\Worklogs\Http\Interfaces;

use DateTime;

interface WorkLogsApiInterface
{
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
    ): object;

    /**
     * Get Work logs.
     *
     * @param null|string[] $issues
     * @param null|\DateTime $from
     * @param null|\DateTime $to
     *
     * @return mixed[]
     */
    public function getWorkLogs(
        ?array $issues,
        ?DateTime $from = null,
        ?DateTime $to = null
    ): array;
}
