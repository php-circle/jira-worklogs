<?php
declare(strict_types=1);

namespace PhpCircle\WorkLogs\Commands;

use DateTime;
use PhpCircle\Http\Interfaces\WorkLogsApiInterface;
use PhpCircle\WorkLogs\Exception\MissingArgumentException;
use PhpCircle\Worklogs\Interfaces\ConfigurationInterface;
use PhpCircle\WorkLogs\Interfaces\EnvInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class WorkLogCommand extends Command
{
    /**
     * @var \PhpCircle\Worklogs\Interfaces\ConfigurationInterface
     */
    private $configuration;

    /**
     * @var \PhpCircle\WorkLogs\Interfaces\EnvInterface
     */
    private $env;

    /**
     * @var \PhpCircle\Http\Interfaces\WorkLogsApiInterface
     */
    private $workLogs;

    /**
     * WorkLogCommand constructor.
     *
     * @param \PhpCircle\Worklogs\Interfaces\ConfigurationInterface $configuration
     * @param \PhpCircle\WorkLogs\Interfaces\EnvInterface $env
     * @param \PhpCircle\Http\Interfaces\WorkLogsApiInterface $workLogs
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

        $this->addArgument(
            'timeSpent',
            InputOption::VALUE_REQUIRED,
            'Time spent in hours'
        );

        $this->addArgument(
            'description',
            InputOption::VALUE_REQUIRED,
            'Ticket description'
        );

        $this->addOption(
            'datetime',
            'dt',
            InputOption::VALUE_OPTIONAL,
            'Time log with format YYYY-MM-DD HH:mm',
            (new DateTime())->format('Y-m-d H:i')
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
     * @throws \PhpCircle\WorkLogs\Exception\MissingArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->setupConfiguration($input, $output);

        [$issueNo, $timeSpent] = [$input->getArgument('issueNo'), $input->getArgument('timeSpent')];

        if ($issueNo === null || $timeSpent === null) {
            throw new MissingArgumentException('Missing issueNo or timeSpent. Call for --help!');
        }

        $this->workLogs->createWorkLog(
            $input->getArgument('issueNo'),
            ((float)$input->getArgument('timeSpent') * 60) * 60,
            new DateTime($input->getOption('datetime')),
            $input->getArgument('description')
        );

        $output->write('Work log has been sent.');
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
