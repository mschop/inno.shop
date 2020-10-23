<?php


namespace InnoShop\Kernel\Commands;


use InnoShop\Kernel\Db\DatabaseTruncateInterface;
use InnoShop\Kernel\Db\IsDatabaseEmptyCheckInterface;
use InnoShop\Kernel\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    protected Migrator $migrator;
    protected IsDatabaseEmptyCheckInterface $isEmptyCheck;
    protected DatabaseTruncateInterface $databaseTruncate;

    public function __construct(
        Migrator $migrator,
        IsDatabaseEmptyCheckInterface $isEmptyCheck,
        DatabaseTruncateInterface $databaseTruncate
    )
    {
        parent::__construct();
        $this->migrator = $migrator;
        $this->isEmptyCheck = $isEmptyCheck;
        $this->databaseTruncate = $databaseTruncate;
    }

    protected function configure()
    {
        $this->setName('install');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln("


    _                          __              
   (_)___  ____  ____    _____/ /_  ____  ____ 
  / / __ \/ __ \/ __ \  / ___/ __ \/ __ \/ __ \
 / / / / / / / / /_/ / (__  ) / / / /_/ / /_/ /
/_/_/ /_/_/ /_/\____(_)____/_/ /_/\____/ .___/ 
                                      /_/      

    
        ");

        $io->comment('start installation');

        if (!$this->isEmptyCheck->isEmpty()) {
            $questionText = 'The database is not empty.';
            $io->caution($questionText);
            $truncateDatabase = $io->askQuestion(new ConfirmationQuestion('Truncate Database (all data will be lost)?', false));
            if ($truncateDatabase) {
                $this->databaseTruncate->apply();
            } else {
                $io->warning("Installation not applied, because you decided not to truncate database");
                return 0;
            }
        }

        $this->migrator->migrate($input, $output);

        return 0;
    }
}