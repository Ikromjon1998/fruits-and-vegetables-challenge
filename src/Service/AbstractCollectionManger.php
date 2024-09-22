<?php

namespace App\Service;

use App\Config\ItemType;
use App\Config\ItemUnit;
use App\DTO\FruitDTO;
use App\DTO\VegetableDTO;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractCollectionManger
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    abstract protected function getEntityClass(): string;

    public function add(FruitDTO $itemData): mixed
    {
        $entityClass = $this->getEntityClass();
        $item = new $entityClass();
        $item->setName($itemData->name);
        $item->setWeight($itemData->weight);
        $this->em->persist($item);
        $this->em->flush();

        return $item;
    }

    public function remove(int $id): void
    {
        $entityClass = $this->getEntityClass();
        $item = $this->em->getRepository($entityClass)->find($id);
        if ($item) {
            $this->em->remove($item);
            $this->em->flush();
        }
    }

    public function list(array $filters = [], array $sorts = []): array
    {
        $entityClass = $this->getEntityClass();

        // Создаем QueryBuilder
        $qb = $this->em->getRepository($entityClass)->createQueryBuilder('e');

        // Применяем фильтры
        foreach ($filters as $key => $value) {
            if ('weight_min' === $key) {
                $qb->andWhere('e.weight >= :weight_min')
                    ->setParameter('weight_min', $value);
            } elseif ('weight_max' === $key) {
                $qb->andWhere('e.weight <= :weight_max')
                    ->setParameter('weight_max', $value);
            } else {
                $qb->andWhere("e.{$key} = :{$key}")
                    ->setParameter($key, $value);
            }
        }

        // Применяем сортировки
        foreach ($sorts as $key => $direction) {
            $qb->addOrderBy("e.{$key}", $direction);
        }

        // Выполняем запрос и возвращаем результат
        return $qb->getQuery()->getResult();
    }

    public function processItemsFromJsonFile(string $filepath): void
    {
        $items = json_decode(file_get_contents($filepath), true);
        $entityClass = $this->getEntityClass();

        foreach ($items as $item) {
            if ($item['type'] === $entityClass::TYPE) {
                $itemToAdd = ItemType::FRUIT->value === $entityClass::TYPE ? new FruitDTO() : new VegetableDTO();
                $itemToAdd->name = $item['name'];
                $itemToAdd->weight = $item['unit'] === ItemUnit::KG->value ? $item['quantity'] * 1000 : $item['quantity'];
                $this->add($itemToAdd);
            }
        }
    }

    public function validateId(int $id): void
    {
        $entityClass = $this->getEntityClass();
        $item = $this->em->getRepository($entityClass)->find($id);

        if (!$item) {
            throw new \InvalidArgumentException("Item with id {$id} not found");
        }
    }
}
