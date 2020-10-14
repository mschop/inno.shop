<?php


namespace InnoShop\Kernel\Commands;


use InnoShop\Kernel\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    private Migrator $migrator;

    public function __construct(Migrator $migrator)
    {
        parent::__construct();
        $this->migrator = $migrator;
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
        $this->migrator->migrate($input, $output);

        return 0;
    }
}