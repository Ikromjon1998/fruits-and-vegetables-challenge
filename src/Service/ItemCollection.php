<?php

namespace App\Service;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;

class ItemCollection
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function add(array $itemData)
    {
        $item = new Item();
        $item->setName($itemData['name']);
        $item->setType($itemData['type']);
        $item->setQuantity($itemData['unit'] === 'kg' ? $itemData['quantity'] * 1000 : $itemData['quantity']);
        $item->setUnit('g');

        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function list(string $type): array
    {
        return $this->entityManager->getRepository(Item::class)->findBy(['type' => $type]);
    }

    public function remove(int $id)
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);

        if ($item) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }
    }

    public function search(string $name): array
    {
        return $this->entityManager->getRepository(Item::class)->findBy(['name' => $name]);
    }

    public function update(int $id, array $itemData)
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);

        if ($item) {
            $item->setName($itemData['name']);
            $item->setType($itemData['type']);
            $item->setQuantity($itemData['unit'] === 'kg' ? $itemData['quantity'] * 1000 : $itemData['quantity']);
            $item->setUnit('g');

            $this->entityManager->flush();
        }
    }
}
