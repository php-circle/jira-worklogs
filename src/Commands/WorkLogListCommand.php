<?php
declare(strict_types=1);

namespace PhpCircle\Jira\Worklogs\Commands;

use DateTime;
use PhpCircle\Jira\Worklogs\Exceptions\MissingArgumentException;
use PhpCircle\Jira\Worklogs\Http\Interfaces\WorkLogsApiInterface;
use PhpCircle\Jira\Worklogs\Interfaces\ConfigurationInterface;
use PhpCircle\Jira\Worklogs\Interfaces\EnvInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class WorkLogListCommand extends Command
{
    /** @var \PhpCircle\Jira\Worklogs\Interfaces\ConfigurationInterface  */
    private $configuration;

    /** @var \PhpCircle\Jira\Worklogs\Interfaces\EnvInterface  */
    private $env;

    /** @var \PhpCircle\Jira\Worklogs\Http\Interfaces\WorkLogsApiInterface  */
    private $workLogs;

    /**
     * WorkLogCommand constructor.
     *
     * @param \PhpCircle\Jira\Worklogs\Interfaces\ConfigurationInterface $configuration
     * @param \PhpCircle\Jira\Worklogs\Interfaces\EnvInterface $env
     * @param \PhpCircle\Jira\Worklogs\Http\Interfaces\WorkLogsApiInterface $workLogs
     * @param null|string $name
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvInterface $env,
        WorkLogsApiInterface $workLogs,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->configuration = $configuration;
        $this->env = $env;
        $this->workLogs = $workLogs;
    }

    /**
     * Configure work log command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument(
            'issueNo',
            InputOption::VALUE_REQUIRED,
            'Issue / Ticket number. (Eg. OP-1498)'
        );

        $this->addOption(
            'from',
            'df',
            InputOption::VALUE_OPTIONAL,
            'Get work logs from date YYYY-MM-DD'
        );

        $this->addOption(
            'to',
            'dt',
            InputOption::VALUE_OPTIONAL,
            'Get work logs to date YYYY-MM-DD'
        );
    }

    /**
     * Execute work logger.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     *
     * @throws \PhpCircle\Jira\Worklogs\Exceptions\MissingArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->setupConfiguration($input, $output);

        $issue = $input->getArgument('issueNo');
        $dateFrom = $input->getOption('from');
        $dateTo = $input->getOption('to');

        if ($issue === null) {
            throw new MissingArgumentException('Missing issueNo. Call for --help!');
        }

        $response = $this->workLogs->getWorkLogs(
            $issue,
            $dateFrom ? new DateTime($input->getOption('from')) : null,
            $dateTo ? new DateTime($input->getOption('to')) : null
        );

        $table = new Table($output);

        $logs = [];

        foreach ($response['results'] as $log) {
            $logs[] = [
                $log['issue']['key'],
                \sprintf('%s %s', $log['startDate'], $log['startTime']),
                \sprintf('%s H', (int)$log['timeSpentSeconds'] / 60 / 60),
                $log['createdAt']
            ];
        }

        $table->setHeaders(['Ticket No.', 'Date Start', 'Total Time Spent', 'Date Logged'])->setRows($logs);
        $table->render();
    }

    /**
     * Setup credential configurations.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    private function setupConfiguration(InputInterface $input, OutputInterface $output): void
    {
        if (empty(\getenv('AUTHOR_ID')) === true) {
            $helper = $this->getHelper('question');

            $authorQst = new Question('Set your AUTHOR_ID : ', false);

            $authorId = $helper->ask($input, $output, $authorQst);

            $this->env->set('AUTHOR_ID', $authorId);
            $this->configuration->setAuthorId($authorId);
        }

        if (empty(\getenv('API_TOKEN')) === true) {
            $helper = $this->getHelper('question');

            $apiTokenQst = new Question('Set your API_TOKEN : ', false);

            $token = $helper->ask($input, $output, $apiTokenQst);

            $this->env->set('API_TOKEN', $token);
            $this->configuration->setToken($token);
        }
    }
}