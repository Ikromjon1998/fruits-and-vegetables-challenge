<?php

namespace App\Service;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;

class ItemCollection
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param array $itemData
     * @return Item
     */
    public function add(array $itemData): Item
    {
        $item = new Item();
        $item->setName($itemData['name']);
        $item->setType($itemData['type']);
        $item->setQuantity($itemData['unit'] === 'kg' ? $itemData['quantity'] * 1000 : $itemData['quantity']);
        $item->setUnit('g');

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $item;
    }

    public function list(string $type, ?string $orderBy): array
    {
        $items = $this->entityManager->getRepository(Item::class)->findBy(['type' => $type], ['name' => $orderBy]);

        return $items;
    }

    /**
     * @param int $id
     * @return void
     */
    public function remove(int $id): void
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);

        if ($item) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }
    }

    /**
     * @param string $name
     * @return array<Item[]>
     */
    public function search(string $name): array
    {
        return $this->entityManager->getRepository(Item::class)->findBy(['name' => $name]);
    }

    /**
     * @param int $id
     * @param array $itemData
     * @return Item
     */
    public function update(int $id, array $itemData): Item
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);

        if ($item) {
            $item->setName($itemData['name']);
            $item->setType($itemData['type']);
            $item->setQuantity($itemData['unit'] === 'kg' ? $itemData['quantity'] * 1000 : $itemData['quantity']);
            $item->setUnit('g');

            $this->entityManager->flush();
        }

        return $item;
    }

    /**
     * @param int $id
     * @retunr void
     * @throws \Exception
     */
    public function toValidateId(int $id): void
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);

        if (!$item) {
            throw new \Exception('Item not found');
        }
    }
}
