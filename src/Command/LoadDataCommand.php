<?php

namespace App\Command;

use App\Service\ItemCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'LoadDataCommand',
    description: 'Load data from a file request.json(from root folder) to the database',
)]
class LoadDataCommand extends Command
{
    protected static $defaultName = 'app:load-data';

    public function __construct(private ItemCollection $itemCollection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get the content of the file request.json
        $json = file_get_contents('request.json');
        $items = json_decode($json, true);

        foreach ($items as $item) {
            $this->itemCollection->add($item);
        }

        return Command::SUCCESS;
    }
}
