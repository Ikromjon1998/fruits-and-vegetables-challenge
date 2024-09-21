<?php

namespace App\Service;

use App\Config\ItemUnit;
use App\DTO\ItemData;
use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class ItemCollection
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ItemRepository $itemRepository,
    ) {
    }

    public function add(ItemData $itemData): Item
    {
        $item = new Item();
        $item->setName($itemData->name);
        $item->setType($itemData->type);
        $item->setQuantity($itemData->unit === ItemUnit::KG->value ? $itemData->quantity * 1000 : $itemData->quantity);
        $item->setUnit('g');

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $item;
    }

    /**
     * Get a list of items based on the type, search, and orderBy parameters.
     */
    public function list(?string $type, string $search, ?string $orderBy = null): array
    {
        // Create QueryBuilder
        $qb = $this->itemRepository->createQueryBuilder('i');

        // Filter by type if provided
        if ($type) {
            $qb->andWhere('i.type = :type')
                ->setParameter('type', $type);
        }

        // Search by partial name match
        if ($search) {
            $qb->andWhere('i.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        if ($orderBy) {
            $qb->orderBy('i.name', $orderBy);
        }

        // Execute the query and return the results
        return $qb->getQuery()->getResult();
    }

    public function remove(int $id): void
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);

        if ($item) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }
    }

    /**
     * Update an item with the provided data.
     */
    public function update(int $id, ItemData $itemData): Item
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);

        if ($item) {
            $item->setName($itemData->name);
            $item->setType($itemData->type);
            $item->setQuantity($itemData->unit === ItemUnit::KG->value ? $itemData->quantity * 1000 : $itemData->quantity);
            $item->setUnit('g');

            $this->entityManager->flush();
        }

        return $item;
    }

    /**
     * @retunr void
     *
     * @throws \Exception
     */
    public function toValidateId(int $id): void
    {
        $item = $this->entityManager->getRepository(Item::class)->find($id);

        if (!$item) {
            throw new \Exception('Item not found');
        }
    }

    public function itemWithNameIsExist(string $name): bool
    {
        $item = $this->itemRepository->findOneBy(['name' => $name]);

        return null !== $item;
    }
}
