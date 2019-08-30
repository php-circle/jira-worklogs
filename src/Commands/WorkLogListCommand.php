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
            'issues',
            InputOption::VALUE_REQUIRED,
            'Issues / Ticket numbers comma separated. (Eg. OP-1498,ONLINE-515)'
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
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->setupConfiguration($input, $output);

        $dateFrom = $input->getOption('from');
        $dateTo = $input->getOption('to');

        $response = $this->workLogs->getWorkLogs(
            $input->getArgument('issues'),
            $dateFrom ? new DateTime($input->getOption('from')) : null,
            $dateTo ? new DateTime($input->getOption('to')) : null
        );

        $table = new Table($output);

        $logs = [];

        $totalHours = 0.00;

        foreach ($response['results'] as $log) {
            $hours = (float)$log['timeSpentSeconds'] / 60 / 60;

            $logs[] = [
                $log['issue']['key'],
                \trim($log['description']),
                \sprintf('%s %s', $log['startDate'], $log['startTime']),
                \sprintf('%s H', $hours),
                $log['createdAt']
            ];

            $totalHours += $hours;
        }

        $table->setHeaders(['Ticket No.', 'Description', 'Date Start', 'Total Time Spent', 'Date Logged'])
            ->setRows($logs)
            ->setFooterTitle(\sprintf('Total Hours: %sH', $totalHours))
            ->render();
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