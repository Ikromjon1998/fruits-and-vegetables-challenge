<?php

namespace App\Tests\App\Service;

use App\Service\ItemCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ItemCollectionTest extends KernelTestCase
{
    private ItemCollection $itemCollection;

    protected function setUp(): void
    {
        // Boot the kernel
        self::bootKernel();

        // Get the ItemCollection service from the container
        $this->itemCollection = self::getContainer()->get(ItemCollection::class);
    }

    public function testAddItem()
    {
        $itemData = [
            'name' => 'Test Fruit',
            'type' => 'fruit',
            'quantity' => 100,
            'unit' => 'g',
        ];

        $fruits = $this->itemCollection->list('fruit');

        $this->itemCollection->add($itemData);
        $items = $this->itemCollection->list('fruit');
        $this->assertCount(count($fruits) + 1, $items);
        $this->assertEquals('Test Fruit', $items[count($fruits)]->getName());
    }

    public function testRemoveItem()
    {
        // First, add an item to remove
        $itemData = [
            'name' => 'Test Vegetable',
            'type' => 'vegetable',
            'quantity' => 200,
            'unit' => 'g',
        ];
        $this->itemCollection->add($itemData);

        $items = $this->itemCollection->list('vegetable');
        $itemId = $items[0]->getId(); // Get the ID of the added item

        // Now, remove the item
        $this->itemCollection->remove($itemId);

        $itemsAfter = $this->itemCollection->list('vegetable');
        $this->assertCount(count($items) - 1, $itemsAfter);
    }
}