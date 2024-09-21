<?php

namespace App\Command;

use App\DTO\ItemData;
use App\Service\ItemCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get the content of the file request.json
        $json = file_get_contents('request.json');
        $items = json_decode($json, true);

        foreach ($items as $item) {
            // create new ItemData object
            $itemToAdd = new ItemData();
            $itemToAdd->name = $item['name'];
            $itemToAdd->type = $item['type'];
            $itemToAdd->quantity = $item['quantity'];
            $itemToAdd->unit = $item['unit'];

            // add the item to the database if this name is not exist
            if (!$this->itemCollection->itemWithNameIsExist($itemToAdd->name)) {
                $this->itemCollection->add($itemToAdd);
            }
        }

        return Command::SUCCESS;
    }
}
