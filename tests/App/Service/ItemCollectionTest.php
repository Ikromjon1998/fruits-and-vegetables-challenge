<?php

namespace App\Tests\App\Service;

use App\DTO\ItemData;
use App\Service\ItemCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ItemCollectionTest extends KernelTestCase
{
    private ItemCollection $itemCollection;

    protected function setUp(): void
    {
        // Boot the kernel
        self::bootKernel();

        $this->itemCollection = self::getContainer()->get(ItemCollection::class);
    }

    public function testAddItem()
    {
        $itemData = new ItemData();
        $itemData->name = 'Test Fruit';
        $itemData->type = 'fruit';
        $itemData->quantity = 100;
        $itemData->unit = 'g';

        $fruits = $this->itemCollection->list('fruit', '', 'asc');

        $this->itemCollection->add($itemData);
        $items = $this->itemCollection->list('fruit', '');
        $this->assertCount(count($fruits) + 1, $items);
        $this->assertEquals('Test Fruit', $items[count($fruits)]->getName());
    }

    public function testRemoveItem()
    {
        // First, add an item to remove
        $itemData = new ItemData();
        $itemData->name = 'Test Vegetable';
        $itemData->type = 'vegetable';
        $itemData->quantity = 200;
        $itemData->unit = 'g';

        $this->itemCollection->add($itemData);

        $items = $this->itemCollection->list('vegetable', '', 'asc');
        $itemId = $items[0]->getId(); // Get the ID of the added item

        // Now, remove the item
        $this->itemCollection->remove($itemId);

        $itemsAfter = $this->itemCollection->list('vegetable', '', 'asc');
        $this->assertCount(count($items) - 1, $itemsAfter);
    }
}
