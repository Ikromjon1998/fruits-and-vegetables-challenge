<?php

namespace App\Controller;

use App\Helpers\ItemHelpers;
use App\Service\FruitCollectionManager;
use App\Service\VegetableCollectionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api')]
class CollectionController extends AbstractController
{
    public function __construct(private FruitCollectionManager $fruitCollectionManager, private VegetableCollectionManager $vegetableCollectionManager, private readonly ItemHelpers $itemHelpers)
    {
    }

    #[Route('/process', name: 'process_json_file')]
    public function processJsonFile(): Response
    {
        $filePath = $this->getParameter('kernel.project_dir').'/request.json';

        if (!file_exists($filePath)) {
            return $this->json([
                'message' => 'File not found',
            ]);
        }

        $this->fruitCollectionManager->processItemsFromJsonFile($filePath);
        $this->vegetableCollectionManager->processItemsFromJsonFile($filePath);

        return $this->json([
            'message' => 'Items added successfully',
        ]);
    }
}
