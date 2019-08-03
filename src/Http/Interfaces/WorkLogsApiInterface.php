<?php
declare(strict_types=1);

namespace PhpCircle\Http\Interfaces;

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
     * @param mixed[]|null $query
     *
     * @return mixed[]
     */
    public function getWorkLogs(?array $query = null): array;
}
