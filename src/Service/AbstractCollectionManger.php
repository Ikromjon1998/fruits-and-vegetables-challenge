<?php

namespace App\Service;

use App\Config\ItemUnit;
use App\DTO\ItemData;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractCollectionManger
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    abstract protected function getEntityClass(): string;

    public function add(ItemData $itemData): void
    {
        $entityClass = $this->getEntityClass();
        $item = new $entityClass();
        $item->setName($itemData->name);
        $item->setWeight($itemData->unit === ItemUnit::KG->value ? $itemData->quantity * 1000 : $itemData->quantity);
        $this->em->persist($item);
        $this->em->flush();
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
                $itemToAdd = new ItemData();
                $itemToAdd->name = $item['name'];
                $itemToAdd->type = $entityClass::TYPE;
                $itemToAdd->quantity = $item['quantity'];
                $itemToAdd->unit = $item['unit'];
                $this->add($itemToAdd);
            }
        }
    }
}
